<?php

namespace App\Data\Social;

use App\Enums\Social\SocialProvider;
use Illuminate\Contracts\Support\Arrayable;

class SocialAccountData implements Arrayable
{
    public function __construct(
        public ?int $id,
        public ?int $user_id,
        public readonly SocialProvider $provider,
        public readonly string $external_user_id,
        public readonly string $access_token,
        public readonly string $refresh_token,
        public readonly string $expires_at,
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
