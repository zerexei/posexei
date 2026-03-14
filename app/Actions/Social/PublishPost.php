<?php

namespace App\Actions\Social;

use App\Jobs\Social\PublishToFacebookJob;
use App\Models\Social\Post;
use App\Models\Social\PostChannel;
use App\Models\Social\SocialChannel;
use App\Enums\Social\SocialProvider;
use Illuminate\Support\Facades\DB;

class PublishPost
{
    public function __invoke(array $data, array $channelIds): Post
    {
        return DB::transaction(function () use ($data, $channelIds) {
            $post = Post::create([
                'user_id' => auth()->id(),
                'content' => $data['content'],
                'post_type' => $data['post_type'] ?? 'text',
                'status' => 'publishing',
            ]);

            if (!empty($data['media'])) {
                foreach ($data['media'] as $mediaItem) {
                    $post->media()->create($mediaItem);
                }
            }

            $channels = SocialChannel::whereIn('id', $channelIds)->with('accounts')->get();

            foreach ($channels as $channel) {
                $postChannel = $post->channels()->create([
                    'social_channel_id' => $channel->id,
                    'status' => 'pending',
                ]);

                // For now, specifically handle Facebook via the job
                // In a mature system, this would use a PlatformDispatcher
                $provider = $channel->accounts->first()->provider;
                
                if ($provider === SocialProvider::FACEBOOK) {
                    PublishToFacebookJob::dispatch($post->id, $postChannel->id);
                }
            }

            return $post;
        });
    }
}
