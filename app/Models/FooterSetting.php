<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    protected $fillable = [
        'tagline',
        'description',
        'phone_primary',
        'phone_secondary',
        'email',
        'office_address',
        'map_url',
        'facebook_url',
        'linkedin_url',
        'privacy_policy_url',
    ];

    public static function current(): ?self
    {
        return static::query()->first();
    }
}
