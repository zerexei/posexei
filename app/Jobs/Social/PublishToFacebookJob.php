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

class PublishToFacebookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $postId,
        protected int $postChannelId
    ) {}

    public function handle(FacebookService $facebookService): void
    {
        $post = Post::with('media')->find($this->postId);
        $postChannel = PostChannel::with('channel')->find($this->postChannelId);

        if (!$post || !$postChannel) {
            return;
        }

        $postChannel->update(['status' => 'publishing']);

        try {
            $pageId = $postChannel->channel->external_id;
            
            // Correctly retrieve the access token from the pivot table between account and channel
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
            $postChannel->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
