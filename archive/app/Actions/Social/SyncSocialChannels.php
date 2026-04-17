<?php

namespace App\Actions\Social;

use App\Jobs\Social\UpsertSocialChannels;
use App\Models\Social\SocialAccount;

class SyncSocialChannels
{
    public function __invoke(SocialAccount $socialAccount, array $socialChannels): void
    {
        if (empty($socialChannels)) {
            return;
        }

        $validatedChannels = [];
        $providerManager = app(\App\Services\Social\SocialProviderManager::class);
        $providerService = $providerManager->getProvider($socialAccount->provider);

        foreach ($socialChannels as $socialChannel) {
            if (! $socialChannel instanceof \App\Data\Social\SocialChannelData) {
                continue;
            }

            $tokenToUse = $socialChannel->access_token ?: $socialAccount->access_token;

            if (! $tokenToUse || ! $providerService->validateChannel($tokenToUse, $socialChannel->external_id)) {
                $socialChannel->status = \App\Enums\Social\SocialChannelStatus::DISCONNECTED;
                \Illuminate\Support\Facades\Log::warning("Validation failed for channel: {$socialChannel->name}");
            }

            $validatedChannels[] = $socialChannel;
        }

        if (empty($validatedChannels)) {
            return;
        }

        $socialChannels = $validatedChannels;
        // $channels = collect($socialChannels)->mapWithKeys(fn ($socialChannel) => [
        //     $socialChannel->external_id => [
        //         'channel_type' => $socialChannel->channel_type,
        //         'external_id' => $socialChannel->external_id,
        //         'name' => $socialChannel->name,
        //         'access_token' => $socialChannel->access_token,
        //         'refresh_token' => $socialChannel->refresh_token,
        //         'expires_at' => $socialChannel->expires_at,
        //         'metadata_json' => $socialChannel->metadata_json,
        //     ],
        // ])->toArray();

        // ws: broadcast chunk socialChannels

        UpsertSocialChannels::dispatch($socialAccount->id, $socialChannels);
    }
}
