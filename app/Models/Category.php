<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'slug',
    ];

    public function shows(): HasMany
    {
        return $this->hasMany(Show::class);
    }
}
