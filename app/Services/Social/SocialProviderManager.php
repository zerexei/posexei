<?php

namespace App\Services\Social;

use App\Enums\Social\SocialProvider as SocialProviderEnum;

class SocialProviderManager
{
    protected array $providers = [
        SocialProviderEnum::FACEBOOK->value => FacebookService::class,
        SocialProviderEnum::INSTAGRAM->value => InstagramService::class,
        SocialProviderEnum::THREADS->value => ThreadsService::class,
    ];

    public function getProvider(SocialProviderEnum $provider): SocialProvider
    {
        return app($this->providers[$provider->value]);
    }
}
