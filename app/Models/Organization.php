<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
