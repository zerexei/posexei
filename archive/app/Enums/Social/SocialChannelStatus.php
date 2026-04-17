<?php

namespace App\Enums\Social;

enum SocialChannelStatus: string
{
    case CONNECTED = 'connected';
    case PROCESSING = 'processing';
    case DISCONNECTED = 'disconnected';
    case EXPIRED = 'expired';
}
