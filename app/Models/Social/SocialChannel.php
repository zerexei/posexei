<?php

namespace App\Models\Social;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SocialChannel extends Model
{
    protected $fillable = [
        'destination_type',
        'external_id',
        'name',
        'metadata_json',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata_json' => 'array',
        ];
    }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(SocialAccount::class, 'social_account_channels')
            ->withPivot('access_token');
    }
}
