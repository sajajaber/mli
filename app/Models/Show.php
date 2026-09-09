<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Show extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'title_en',
        'title_ar',
        'slug',
        'description_en',
        'description_ar',
        'cover_image_path',
        'cover_image_alt',
        'vimeo_url',
        'status',
        'published_at',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'ai_flags',
        'sort_order',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'ai_flags' => 'array',
    ];

    // --- Relationships ---

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // --- Scopes ---

    /**
     * Only shows that are actually visible on the public site right now.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    // --- Accessors ---

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image_path
            ? Storage::url($this->cover_image_path)
            : null;
    }

    // --- Slug auto-generation ---

    protected static function booted(): void
    {
        static::creating(function (Show $show) {
            if (empty($show->slug)) {
                $show->slug = static::generateUniqueSlug($show->title_en);
            }

            $show->applyAutoMeta();
        });

        static::updating(function (Show $show) {
            if (empty($show->slug)) {
                $show->slug = static::generateUniqueSlug($show->title_en);
            }

            $show->applyAutoMeta();
        });
    }

    /**
     * Auto-fill SEO meta fields from the show's own content.
     * Admin never sets these manually — always derived automatically.
     */
    protected function applyAutoMeta(): void
    {
        $this->meta_title_en = $this->title_en;
        $this->meta_title_ar = $this->title_ar;

        $this->meta_description_en = $this->description_en
            ? Str::limit(strip_tags($this->description_en), 155)
            : null;

        $this->meta_description_ar = $this->description_ar
            ? Str::limit(strip_tags($this->description_ar), 155)
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
