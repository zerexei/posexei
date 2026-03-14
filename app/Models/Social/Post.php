<?php

namespace App\Models\Social;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'post_type',
        'content',
        'status',
        'publish_at',
    ];

    protected $casts = [
        'publish_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class);
    }

    public function channels(): HasMany
    {
        return $this->hasMany(PostChannel::class);
    }
}
