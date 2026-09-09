<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
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
        'ai_flags',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'ai_flags' => 'array',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    protected static function booted(): void
    {
        static::creating(fn(Page $page) => $page->applyAutoMeta());
        static::updating(fn(Page $page) => $page->applyAutoMeta());
    }

    protected function applyAutoMeta(): void
    {
        $this->meta_title_en = $this->title_en;
        $this->meta_title_ar = $this->title_ar;

        $this->meta_description_en = $this->content_en
            ? \Illuminate\Support\Str::limit(strip_tags($this->content_en), 155)
            : null;

        $this->meta_description_ar = $this->content_ar
            ? \Illuminate\Support\Str::limit(strip_tags($this->content_ar), 155)
            : null;
    }
}
