<?php

namespace App\Services\Social;

use App\Data\Social\SocialAccountData;
use App\Data\Social\SocialChannelData;
use App\Enums\Social\SocialChannelStatus;
use App\Enums\Social\SocialProvider as SocialProviderEnum;
use Illuminate\Support\Facades\Http;

class InstagramService implements SocialProvider
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
            provider: SocialProviderEnum::FACEBOOK,
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
            $response = Http::get($url, [
                'access_token' => $userAccessToken,
                'fields' => 'instagram_business_account',
            ])->throw();
            $data = $response->json('data', []);

            foreach ($data as $account) {
                $chunk[] = new SocialChannelData(
                    id: null,
                    channel_type: self::CHANNEL_TYPE,
                    external_id: $account['instagram_business_account']['id'],
                    name: $account['instagram_business_account']['name'],
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

    // public function createMediaContainer(string $igUserId, string $pageAccessToken, string $imageUrl, ?string $caption = null): array
    // {
    //     $payload = [
    //         'access_token' => $pageAccessToken,
    //         'image_url' => $imageUrl,
    //     ];

    //     if ($caption) {
    //         $payload['caption'] = $caption;
    //     }

    //     $response = Http::post(self::BASE_URL."/{$igUserId}/media", $payload)->throw();

    //     return $response->json() ?? [];
    // }

    // public function publishMedia(string $igUserId, string $pageAccessToken, string $creationId): array
    // {
    //     $response = Http::post(self::BASE_URL."/{$igUserId}/media_publish", [
    //         'access_token' => $pageAccessToken,
    //         'creation_id' => $creationId,
    //     ])->throw();

    //     return $response->json() ?? [];
    // }
}
