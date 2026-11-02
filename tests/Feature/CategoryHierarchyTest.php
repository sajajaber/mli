<?php

namespace Tests\Feature;

use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Category;
use App\Models\Show;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

    public function test_shows_page_only_displays_subcategories_for_the_selected_parent(): void
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

        Category::query()->create([
            'name_en' => 'Drama',
            'name_ar' => 'دراما',
            'slug' => 'drama',
        ]);

        $this->get('/shows')
            ->assertOk()
            ->assertSee('Documentary')
            ->assertDontSee('Nature')
            ->assertDontSee('mli-shows-subcategory-filter-row');

        $this->get('/shows?category=documentary')
            ->assertOk()
            ->assertSee('mli-shows-subcategory-filter-row')
            ->assertSee('Nature')
            ->assertDontSee('All Documentary');

        // The second row remains visible when one of the parent's children is selected.
        $this->get('/shows?category=nature')
            ->assertOk()
            ->assertSee('mli-shows-subcategory-filter-row')
            ->assertSee('Nature')
            ->assertDontSee('All Documentary');

        // A selected parent without children must not show an empty second row.
        $this->get('/shows?category=drama')
            ->assertOk()
            ->assertDontSee('mli-shows-subcategory-filter-row');
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

    public function test_category_admin_rows_share_parent_colors_and_distinguish_each_parent(): void
    {
        $firstParent = Category::query()->create([
            'name_en' => 'A Parent',
            'name_ar' => 'أ',
            'slug' => 'a-parent',
        ]);
        Category::query()->create([
            'parent_id' => $firstParent->getKey(),
            'name_en' => 'A Child',
            'name_ar' => 'أ-فرعي',
            'slug' => 'a-child',
        ]);
        Category::query()->create([
            'name_en' => 'B Parent',
            'name_ar' => 'ب',
            'slug' => 'b-parent',
        ]);
        $this->actingAs(UserFactory::new()->admin()->create());

        $component = Livewire::test(ListCategories::class);
        preg_match_all(
            '/style="--mli-category-hue: ([^;]+)deg/',
            $component->html(),
            $hues,
        );

        $this->assertSame(3, count($hues[1]));
        $this->assertSame($hues[1][0], $hues[1][1]);
        $this->assertSame(2, count(array_unique($hues[1])));
        $component->assertSeeHtml('mli-category-colored-row');
    }
}
