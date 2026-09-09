<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'logo_path',
        'sort_order',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path
            ? Storage::url($this->logo_path)
            : null;
    }
}
