<?php

namespace App\Services\Social;

use App\Data\Social\SocialAccountData;

interface SocialProvider
{
    public function getAccount(string $userAccessToken): SocialAccountData;

    public function getChannels(string $userAccessToken, int $chunkSize = 50): \Generator;

    public function validateAccount(string $accessToken): bool;

    public function validateChannel(string $token, string $externalId): bool;
}
