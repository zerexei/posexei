<?php

namespace App\Enums\Social;

enum PostStatus: string
{
    case  DRAFT = 'draft';
    case  SCHEDULED = 'scheduled';
    case  PUBLISHING = 'publishing';
    case  COMPLETED = 'completed';
    case  FAILED = 'failed';
}
