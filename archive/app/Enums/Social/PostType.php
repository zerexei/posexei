<?php

namespace App\Enums\Social;

enum PostType: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case VIDEO = 'video';
}
