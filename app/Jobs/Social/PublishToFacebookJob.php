<?php

namespace App\Jobs\Social;

use App\Models\Social\Post;
use App\Models\Social\PostChannel;
use App\Services\Social\FacebookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\Middleware\RateLimited;

class PublishToFacebookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $backoff = 60;

    public function __construct(
        protected int $postId,
        protected int $postChannelId
    ) {}

    public function handle(FacebookService $facebookService): void
    {
        $post = Post::with('media', 'user')->find($this->postId);
        $postChannel = PostChannel::with('channel')->find($this->postChannelId);

        if (!$post || !$postChannel) {
            return;
        }

        // 1. Idempotency Check
        if ($postChannel->status === 'success' || $postChannel->external_post_id) {
            Log::info("Post already published to Facebook: PostChannel {$this->postChannelId}");
            return;
        }

        // 2. Rate Limiting and Atomic Locking
        Redis::throttle('facebook_publishing_' . $postChannel->social_channel_id)
            ->allow(10)
            ->every(60)
            ->then(function () use ($post, $postChannel, $facebookService) {
                $this->processPublishing($post, $postChannel, $facebookService);
            }, function () {
                // Could not obtain lock or rate limit exceeded
                return $this->release(10);
            });
    }

    protected function processPublishing(Post $post, PostChannel $postChannel, FacebookService $facebookService): void
    {
        $lock = Cache::lock("publish_post_channel_{$this->postChannelId}", 60);

        if (!$lock->get()) {
            $this->release(10);
            return;
        }

        try {
            $postChannel->update(['status' => 'publishing']);

            $pageId = $postChannel->channel->external_id;
            
            // Correctly retrieve the access token from the pivot table
            $account = $post->user->socialAccounts()
                ->whereHas('socialChannels', fn($q) => $q->where('social_channels.id', $postChannel->social_channel_id))
                ->first();
                
            $pivot = $account->socialChannels()
                ->where('social_channels.id', $postChannel->social_channel_id)
                ->first()
                ->pivot;
                
            $pageAccessToken = $pivot->access_token;

            $result = [];
            if ($post->post_type === 'image' && $post->media->isNotEmpty()) {
                $result = $facebookService->publishPhoto(
                    $pageId,
                    $pageAccessToken,
                    $post->media->first()->url,
                    $post->content
                );
            } else {
                $result = $facebookService->publishPost(
                    $pageId,
                    $pageAccessToken,
                    $post->content
                );
            }

            $postChannel->update([
                'status' => 'success',
                'external_post_id' => $result['id'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error("Facebook Publishing Failed: " . $e->getMessage());
            
            if ($this->isPermanentFailure($e)) {
                $postChannel->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            } else {
                throw $e;
            }
        } finally {
            $lock->release();
        }
    }

    protected function isPermanentFailure(\Exception $e): bool
    {
        $message = strtolower($e->getMessage());
        return str_contains($message, 'invalid access token') || 
               str_contains($message, 'permission') ||
               str_contains($message, 'unsupported post request');
    }
}
