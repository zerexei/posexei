<?php

namespace App\Services\Social;

class SocialProviderManager
{
    protected array $providers = [
        SocialProvider::FACEBOOK => FacebookService::class,
        SocialProvider::INSTAGRAM => InstagramService::class,
        SocialProvider::THREADS => ThreadsService::class,
    ];

    public function getProvider(SocialProvider $provider): SocialProvider
    {
        return app($this->providers[$provider]);
    }
}
