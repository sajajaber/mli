<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SiteContent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'key',
        'title_en',
        'title_ar',
        'content_en',
        'content_ar',
        'status',
        'published_at',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeForKey(Builder $query, string $key): Builder
    {
        return $query->where('key', $key);
    }

    protected static function booted(): void
    {
        static::creating(function (SiteContent $content) {
            if (blank($content->key)) {
                $content->key = static::generateKey($content->title_en);
            }

            $content->applyAutoMeta();
        });

        static::updating(fn (SiteContent $content) => $content->applyAutoMeta());
    }

    protected static function generateKey(?string $title): string
    {
        $base = Str::of($title ?: 'site_content')
            ->slug('_')
            ->value();

        $base = $base ?: 'site_content';
        $key = $base;
        $suffix = 2;

        while (static::withTrashed()->where('key', $key)->exists()) {
            $key = $base . '_' . $suffix;
            $suffix++;
        }

        return $key;
    }

    protected function applyAutoMeta(): void
    {
        $this->meta_title_en = $this->title_en;
        $this->meta_title_ar = $this->title_ar;

        $this->meta_description_en = $this->content_en
            ? Str::limit(strip_tags($this->content_en), 155)
            : null;

        $this->meta_description_ar = $this->content_ar
            ? Str::limit(strip_tags($this->content_ar), 155)
            : null;
    }
}
