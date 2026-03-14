<?php

namespace App\Services\Social;

use App\Data\Social\SocialAccountData;
use App\Data\Social\SocialChannelData;
use App\Enums\Social\SocialChannelStatus;
use App\Enums\Social\SocialProvider;
use App\Services\Social\SocialProvider as ISocialProvider;
use Illuminate\Support\Facades\Http;

class FacebookService implements ISocialProvider
{
    private const string BASE_URL = 'https://graph.facebook.com/v25.0';

    private const string  CHANNEL_TYPE = 'page';

    public function getAccount(string $userAccessToken): SocialAccountData
    {
        $response = Http::get(self::BASE_URL.'/me', [
            'access_token' => $userAccessToken,
        ])->throw();

        return new SocialAccountData(
            id: null,
            user_id: null,
            provider: SocialProvider::FACEBOOK,
            external_user_id: $response->json('id'),
            access_token: $userAccessToken,
            refresh_token: $userAccessToken,
            expires_at: $response->json('expires_at'),
        );
    }

    public function getChannels(string $userAccessToken, int $chunkSize = 50): \Generator
    {
        $url = self::BASE_URL.'/me/accounts';
        $chunk = [];

        do {
            $response = Http::get($url, ['access_token' => $userAccessToken])->throw();
            $data = $response->json('data', []);

            foreach ($data as $account) {
                $chunk[] = new SocialChannelData(
                    id: null,
                    channel_type: self::CHANNEL_TYPE,
                    external_id: $account['id'],
                    name: $account['name'],
                    status: SocialChannelStatus::PROCESSING,
                    access_token: $account['access_token'] ?? null,
                    refresh_token: $account['refresh_token'] ?? null,
                    expires_at: $account['expires_at'] ?? null,
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
            $response = Http::get(self::BASE_URL.'/me/permissions', [
                'access_token' => $accessToken,
            ])->throw();

            $permissions = collect($response->json('data', []))->pluck('status', 'permission');

            return $permissions->get('pages_show_list') === 'granted'
                && $permissions->get('pages_manage_posts') === 'granted';
        } catch (\Exception $e) {
            return false;
        }
    }

    public function validateChannel(string $token, string $externalId): bool
    {
        try {
            $response = Http::get(self::BASE_URL.'/'.$externalId, [
                'access_token' => $token,
                'fields' => 'tasks',
            ])->throw();

            $tasks = collect($response->json('tasks', []));

            return $tasks->contains('CREATE_CONTENT') || $tasks->contains('MANAGE');
        } catch (\Exception $e) {
            return false;
        }
    }

    // public function publishPost(string $pageId, string $pageAccessToken, string $message, ?string $link = null): array
    // {
    //     $payload = [
    //         'access_token' => $pageAccessToken,
    //         'message' => $message,
    //     ];

    //     if ($link) {
    //         $payload['link'] = $link;
    //     }

    //     $response = Http::post(self::BASE_URL."/{$pageId}/feed", $payload)->throw();

    //     return $response->json() ?? [];
    // }

    // public function publishPhoto(string $pageId, string $pageAccessToken, string $imageUrl, ?string $message = null): array
    // {
    //     $payload = [
    //         'access_token' => $pageAccessToken,
    //         'url' => $imageUrl,
    //     ];

    //     if ($message) {
    //         $payload['message'] = $message;
    //     }

    //     $response = Http::post(self::BASE_URL."/{$pageId}/photos", $payload)->throw();

    //     return $response->json() ?? [];
    // }
}
