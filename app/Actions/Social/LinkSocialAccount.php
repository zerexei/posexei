<?php

namespace App\Actions\Social;

use App\Data\Social\SocialAccountData;
use App\Models\Social\SocialAccount;

class LinkSocialAccount
{
    public function __invoke(SocialAccountData $socialAccountData): SocialAccount
    {
        $socialAccount = SocialAccount::where('provider', $socialAccountData->provider)
            ->where('external_user_id', $socialAccountData->external_user_id)
            ->first();

        if ($socialAccount && $socialAccount->user_id !== $socialAccountData->user_id) {
            throw new \Exception(sprintf(
                'This %s account is already connected to another user.',
                $socialAccountData->provider
            ));
        }

        if ($socialAccount) {
            $socialAccount->fill([
                'access_token' => $data->access_token,
                'refresh_token' => $data->refresh_token,
                'expires_at' => $data->expires_at,
            ]);

            if ($socialAccount->isDirty()) {
                $socialAccount->saveOrFail();
            }

            return $socialAccount;
        }

        return SocialAccount::create([
            'user_id' => $socialAccountData->user_id,
            'provider' => $socialAccountData->provider,
            'external_user_id' => $socialAccountData->external_user_id,
            'access_token' => $socialAccountData->access_token,
            'refresh_token' => $socialAccountData->refresh_token,
            'expires_at' => $socialAccountData->expires_at,
        ]);
    }
}
