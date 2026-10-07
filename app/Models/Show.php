<?php

namespace App\Models;

use App\Models\Concerns\PublishesScheduledContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Show extends Model
{
    use HasFactory, PublishesScheduledContent, SoftDeletes;

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
        'is_new_release',
        'published_at',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'sort_order',
    ];

    protected $appends = ['vimeo_embed_url'];

    protected $casts = [
        'published_at' => 'datetime',
        'is_new_release' => 'boolean',
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
        static::publishDueScheduledItems();

        return $query->where('status', 'published');
    }

    // --- Accessors ---

    public function getVimeoEmbedUrlAttribute(): ?string
    {
        if (blank($this->vimeo_url)) {
            return null;
        }

        $parts = parse_url(trim((string) $this->vimeo_url));
        $host = strtolower($parts['host'] ?? '');

        if (! in_array($host, ['vimeo.com', 'www.vimeo.com', 'player.vimeo.com'], true)) {
            return null;
        }

        $path = $parts['path'] ?? null;

        if (! is_string($path) || ! preg_match('~^/([0-9]+)(?:/[^/]+)?/?$~', $path, $matches)) {
            return null;
        }

        return 'https://player.vimeo.com/video/' . $matches[1];
    }

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
            $show->slug = static::generateUniqueSlug($show->title_en, $show->slug);
            $show->applyAutoMeta();
        });

        static::updating(function (Show $show) {
            $shouldRegenerateFromTitle = $show->isDirty('title_en')
                || blank($show->slug)
                || static::withTrashed()
                    ->whereKeyNot($show->getKey())
                    ->whereRaw('LOWER(slug) = ?', [mb_strtolower((string) $show->slug)])
                    ->exists();

            $show->slug = static::generateUniqueSlug(
                $show->title_en,
                $shouldRegenerateFromTitle ? $show->title_en : $show->slug,
                $show->getKey(),
            );

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

    protected static function generateUniqueSlug(string $titleEn, ?string $preferredSlug = null, ?int $ignoreId = null): string
    {
        $base = filled($preferredSlug)
            ? Str::slug($preferredSlug)
            : Str::slug($titleEn);

        $base = $base ?: 'show';
        $slug = $base;
        $counter = 2;

        while (static::withTrashed()
            ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
            ->whereRaw('LOWER(slug) = ?', [mb_strtolower($slug)])
            ->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
