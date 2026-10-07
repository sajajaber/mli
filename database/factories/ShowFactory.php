<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Show;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Show>
 */
class ShowFactory extends Factory
{
    protected $model = Show::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'title_en' => ucwords($title),
            'title_ar' => 'برنامج ' . fake()->numberBetween(1, 99999),
            'slug' => Str::slug($title),
            'description_en' => fake()->sentence(),
            'description_ar' => 'وصف تجريبي.',
            'cover_image_path' => null,
            'cover_image_alt' => null,
            'vimeo_url' => null,
            'status' => 'draft',
            'is_new_release' => false,
            'published_at' => null,
            'meta_title_en' => null,
            'meta_title_ar' => null,
            'meta_description_en' => null,
            'meta_description_ar' => null,
            'sort_order' => 0,
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

    public function newRelease(): static
    {
        return $this->state(['is_new_release' => true]);
    }
}
