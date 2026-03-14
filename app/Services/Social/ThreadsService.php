<?php

namespace App\Services\Social;

use App\Data\Social\SocialAccountData;
use App\Data\Social\SocialChannelData;
use App\Enums\Social\SocialChannelStatus;
use App\Enums\Social\SocialProvider as SocialProviderEnum;
use Illuminate\Support\Facades\Http;

class ThreadsService implements SocialProvider
{
    private const string BASE_URL = 'https://graph.threads.net/v1.0';

    private const string  CHANNEL_TYPE = 'page';

    public function getAccount(string $userAccessToken): SocialAccountData
    {
        $response = Http::get(self::BASE_URL.'/me', [
            'access_token' => $userAccessToken,
            'fields' => 'id,username,name,threads_profile_picture_url,threads_biography',
        ])->throw();

        return new SocialAccountData(
            id: null,
            user_id: null,
            provider: SocialProviderEnum::THREADS,
            external_user_id: $response->json('id'),
            access_token: $userAccessToken,
            refresh_token: $userAccessToken,
            expires_at: $response->json('expires_at') ?? '',
        );
    }

    public function getChannels(string $userAccessToken, int $chunkSize = 50): \Generator
    {
        $url = self::BASE_URL.'/me/accounts';
        $chunk = [];

        do {
            $response = Http::get($url, [
                'access_token' => $userAccessToken,
                'fields' => 'id,name,username,threads_profile_picture_url,threads_biography',
            ])->throw();
            $data = $response->json('data', []);

            foreach ($data as $account) {
                $chunk[] = new SocialChannelData(
                    id: null,
                    channel_type: self::CHANNEL_TYPE,
                    external_id: $account['id'],
                    name: $account['name'],
                    status: SocialChannelStatus::PROCESSING,
                );

                if (count($chunk) >= $chunkSize) {
                    yield $chunk;
                    $chunk = [];
                }
            }

            $url = $response->json('paging.next', null);

        } while ($url !== null);

        if (! empty($chunk)) {
            yield $chunk;
        }
    }

    public function validateAccount(string $accessToken): bool
    {
        try {
            Http::get(self::BASE_URL.'/me', [
                'access_token' => $accessToken,
            ])->throw();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function validateChannel(string $token, string $externalId): bool
    {
        try {
            $response = Http::get(self::BASE_URL.'/'.$externalId, [
                'access_token' => $token,
                'fields' => 'id',
            ])->throw();

            return (bool) $response->json('id');
        } catch (\Exception $e) {
            return false;
        }
    }

    // public function createMediaContainer(
    //     string $threadsUserId,
    //     string $userAccessToken,
    //     string $mediaType,
    //     string $text,
    //     ?string $imageUrl = null,
    //     ?string $videoUrl = null
    // ): array {
    //     $payload = [
    //         'access_token' => $userAccessToken,
    //         'media_type' => $mediaType, // 'TEXT', 'IMAGE', or 'VIDEO'
    //         'text' => $text,
    //     ];

    //     if ($mediaType === 'IMAGE' && $imageUrl) {
    //         $payload['image_url'] = $imageUrl;
    //     }

    //     if ($mediaType === 'VIDEO' && $videoUrl) {
    //         $payload['video_url'] = $videoUrl;
    //     }

    //     $response = Http::post(self::BASE_URL."/{$threadsUserId}/threads", $payload)->throw();

    //     return $response->json() ?? [];
    // }

    // public function publishMedia(string $threadsUserId, string $userAccessToken, string $creationId): array
    // {
    //     $response = Http::post(self::BASE_URL."/{$threadsUserId}/threads_publish", [
    //         'access_token' => $userAccessToken,
    //         'creation_id' => $creationId,
    //     ])->throw();

    //     return $response->json() ?? [];
    // }
}
