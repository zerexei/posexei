<?php

namespace App\Services\Social;

interface SocialProvider
{
    public function getAccount(string $userAccessToken): SocialAccountData;

    public function getChannels(string $userAccessToken, int $chunkSize = 50): \Generator;
}
