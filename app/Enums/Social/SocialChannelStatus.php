<?php

namespace App\Enums\Social;

enum SocialChannelStatus: string
{
    case CONNECTED = 'connected';
    case DISCONNECTED = 'disconnected';
    case EXPIRED = 'expired';
}
