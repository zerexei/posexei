<?php

namespace App\Models\Social;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostSocialChannelStatus extends Model
{
    protected $fillable = [
        'post_id',
        'social_channel_id',
        'status',
        'external_post_id',
        'error_message',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function socialChannel(): BelongsTo
    {
        return $this->belongsTo(SocialChannel::class);
    }
}
