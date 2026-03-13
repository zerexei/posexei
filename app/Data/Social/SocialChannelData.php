<?php

namespace App\Data\Social;

use Illuminate\Contracts\Support\Arrayable;

class SocialChannelData implements Arrayable
{
    public function __construct(
        public ?int $id,
        public readonly string $channel_type,
        public readonly string $external_id,
        public readonly string $name,
        public readonly string $metadata_json = '',
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
