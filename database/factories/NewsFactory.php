<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    protected $model = News::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title_en' => ucwords($title),
            'title_ar' => 'خبر ' . fake()->numberBetween(1, 99999),
            'slug' => Str::slug($title),
            'body_en' => fake()->paragraph(),
            'body_ar' => 'نص خبري تجريبي.',
            'featured_image_path' => null,
            'featured_image_alt' => null,
            'news_type' => 'mli_news',
            'status' => 'draft',
            'published_at' => null,
            'meta_title_en' => null,
            'meta_title_ar' => null,
            'meta_description_en' => null,
            'meta_description_ar' => null,
        ];
    }

    public function published(): static
    {
        return $this->state([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function scheduled(\DateTimeInterface|string|null $at = null): static
    {
        return $this->state([
            'status' => 'scheduled',
            'published_at' => $at ?? now()->addHour(),
        ]);
    }

    public function mediaNews(): static
    {
        return $this->state(['news_type' => 'media_news']);
    }
}
