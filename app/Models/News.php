<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class News extends Model
{
    use SoftDeletes;

    protected $table = 'news'; // explicit — avoids Eloquent guessing wrong on pluralization

    protected $fillable = [
        'title_en',
        'title_ar',
        'slug',
        'body_en',
        'body_ar',
        'featured_image_path',
        'featured_image_alt',
        'news_type',
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

    public function scopeMediaNews(Builder $query): Builder
    {
        return $query->where('news_type', 'media_news');
    }

    public function scopeMliNews(Builder $query): Builder
    {
        return $query->where('news_type', 'mli_news');
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->featured_image_path
            ? Storage::url($this->featured_image_path)
            : null;
    }

    protected static function booted(): void
    {
        static::creating(function (News $news) {
            if (empty($news->slug)) {
                $news->slug = static::generateUniqueSlug($news->title_en);
            }

            $news->applyAutoMeta();
        });

        static::updating(function (News $news) {
            if (empty($news->slug)) {
                $news->slug = static::generateUniqueSlug($news->title_en);
            }

            $news->applyAutoMeta();
        });
    }

    /**
     * Auto-fill SEO meta fields from the article's own content.
     * Admin never sets these manually — always derived automatically.
     */
    protected function applyAutoMeta(): void
    {
        $this->meta_title_en = $this->title_en;
        $this->meta_title_ar = $this->title_ar;

        $this->meta_description_en = $this->body_en
            ? \Illuminate\Support\Str::limit(strip_tags($this->body_en), 155)
            : null;

        $this->meta_description_ar = $this->body_ar
            ? \Illuminate\Support\Str::limit(strip_tags($this->body_ar), 155)
            : null;
    }

    protected static function generateUniqueSlug(string $titleEn): string
    {
        $base = Str::slug($titleEn);
        $slug = $base;
        $counter = 2;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
