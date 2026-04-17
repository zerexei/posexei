<?php

namespace App\Models\Social;

use App\Enums\Social\SocialProvider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SocialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'user_id',
        'provider',
        'external_user_id',
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'provider' => SocialProvider::class,
            'expires_at' => 'datetime',
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function socialChannels(): BelongsToMany
    {
        return $this->belongsToMany(SocialChannel::class)->using(SocialAccountSocialChannel::class)->withPivot([
            'access_token',
            'refresh_token',
            'expires_at',
            'status',
        ]);
    }
}
