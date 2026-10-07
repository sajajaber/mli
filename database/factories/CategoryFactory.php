<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name_en' => ucwords($name),
            'name_ar' => 'تصنيف ' . fake()->numberBetween(1, 9999),
            'slug' => Str::slug($name),
        ];
    }
}
