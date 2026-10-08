<?php

namespace Database\Factories;

use App\Models\SiteContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiteContent>
 */
class SiteContentFactory extends Factory
{
    protected $model = SiteContent::class;

    public function definition(): array
    {
        return [
            'key' => null,
            'title_en' => ucwords(fake()->unique()->words(3, true)),
            'title_ar' => 'محتوى تجريبي',
            'content_en' => fake()->sentence(),
            'content_ar' => 'محتوى تجريبي.',
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
}
