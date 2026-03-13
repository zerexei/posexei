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

        $channels = collect($socialChannels)->mapWithKeys(fn ($socialChannel) => [
            $socialChannel['external_id'] => [
                'channel_type' => $socialChannel['channel_type'],
                'external_id' => $socialChannel['external_id'],
                'name' => $socialChannel['name'],
                'metadata_json' => $socialChannel['metadata_json'],
            ],
        ])->toArray();

        // ws: broadcast chunk socialChannels
        
        UpsertSocialChannels::dispatch($socialAccount->id, $channels);
    }
}
