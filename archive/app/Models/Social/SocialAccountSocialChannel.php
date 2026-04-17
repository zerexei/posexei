<?php

namespace App\Models\Social;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SocialAccountSocialChannel extends Pivot
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
        ];
    }
}
