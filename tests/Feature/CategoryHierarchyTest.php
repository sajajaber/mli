<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Show;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryHierarchyTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_only_parent_categories_even_when_empty(): void
    {
        $parent = Category::query()->create([
            'name_en' => 'Documentary',
            'name_ar' => 'وثائقي',
            'slug' => 'documentary',
        ]);

        Category::query()->create([
            'parent_id' => $parent->id,
            'name_en' => 'Nature',
            'name_ar' => 'طبيعة',
            'slug' => 'nature',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Documentary')
            ->assertDontSee('Nature');
    }

    public function test_shows_page_lists_subcategories_under_their_parent_even_when_empty(): void
    {
        $parent = Category::query()->create([
            'name_en' => 'Documentary',
            'name_ar' => 'وثائقي',
            'slug' => 'documentary',
        ]);

        Category::query()->create([
            'parent_id' => $parent->id,
            'name_en' => 'Nature',
            'name_ar' => 'طبيعة',
            'slug' => 'nature',
        ]);

        $this->get('/shows')
            ->assertOk()
            ->assertSeeInOrder(['Documentary', 'Nature']);
    }

    public function test_parent_filter_includes_shows_assigned_to_a_child_category(): void
    {
        $parent = Category::query()->create([
            'name_en' => 'Documentary',
            'name_ar' => 'وثائقي',
            'slug' => 'documentary',
        ]);

        $child = Category::query()->create([
            'parent_id' => $parent->id,
            'name_en' => 'Nature',
            'name_ar' => 'طبيعة',
            'slug' => 'nature',
        ]);

        $show = Show::factory()->published()->create([
            'title_en' => 'Wild Nature',
            'title_ar' => 'الطبيعة البرية',
            'slug' => 'wild-nature',
        ]);

        $show->categories()->attach($child->id);

        $this->get('/shows?category=documentary')
            ->assertOk()
            ->assertSee('Wild Nature');

        $this->get('/shows?category=nature')
            ->assertOk()
            ->assertSee('Wild Nature');
    }
}
