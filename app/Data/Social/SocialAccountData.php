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
        public readonly ?string $access_token,
        public readonly ?string $refresh_token,
        public readonly ?string $expires_at,
    ) {}

    public function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            user_id: $data['user_id'] ?? null,
            provider: SocialProvider::from($data['provider']),
            external_user_id: $data['external_user_id'],
            access_token: $data['access_token'] ?? null,
            refresh_token: $data['refresh_token'] ?? null,
            expires_at: $data['expires_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'provider' => $this->provider->value,
            'external_user_id' => $this->external_user_id,
            'access_token' => $this->access_token,
            'refresh_token' => $this->refresh_token,
            'expires_at' => $this->expires_at,
        ];
    }

    public function __serialize(): array
    {
        return $this->toArray();
    }

    public function __unserialize(array $data): void
    {
        $this->fromArray($data);
    }
}
