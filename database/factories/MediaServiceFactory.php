<?php

namespace DatabaseFactories;

use App\Models\MediaService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MediaService>
 */
class MediaServiceFactory extends Factory
{
    protected $model = MediaService::class;

    public function definition(): array
    {
        return [
            'title_en' => ucwords(fake()->unique()->words(2, true)),
            'title_ar' => 'خدمة إعلامية',
            'content_en' => fake()->sentence(),
            'content_ar' => 'وصف الخدمة الإعلامية.',
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
