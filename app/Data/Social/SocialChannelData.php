<?php

namespace App\Data\Social;

use App\Enums\Social\SocialChannelStatus;
use Illuminate\Contracts\Support\Arrayable;

class SocialChannelData implements Arrayable
{
    public function __construct(
        public ?int $id,
        public readonly string $channel_type,
        public readonly string $external_id,
        public readonly string $name,
        public SocialChannelStatus $status,
        public readonly ?string $access_token = null,
        public readonly ?string $refresh_token = null,
        public readonly ?string $expires_at = null,
        public readonly array $metadata_json = [],
    ) {}

    public function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            channel_type: $data['channel_type'],
            external_id: $data['external_id'],
            name: $data['name'],
            status: SocialChannelStatus::from($data['status']),
            access_token: $data['access_token'] ?? null,
            refresh_token: $data['refresh_token'] ?? null,
            expires_at: $data['expires_at'] ?? null,
            metadata_json: $data['metadata_json'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'channel_type' => $this->channel_type,
            'external_id' => $this->external_id,
            'name' => $this->name,
            'status' => $this->status->value,
            'access_token' => $this->access_token,
            'refresh_token' => $this->refresh_token,
            'expires_at' => $this->expires_at,
            'metadata_json' => $this->metadata_json,
        ];
    }

    public function __serialize(): array
    {
        return $this->toArray();
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'] ?? null;
        $this->channel_type = $data['channel_type'];
        $this->external_id = $data['external_id'];
        $this->name = $data['name'];
        $this->status = SocialChannelStatus::from($data['status']);
        $this->access_token = $data['access_token'] ?? null;
        $this->refresh_token = $data['refresh_token'] ?? null;
        $this->expires_at = $data['expires_at'] ?? null;
        $this->metadata_json = $data['metadata_json'] ?? [];
    }
}
