<?php

namespace Tests\Feature;

use App\Filament\Resources\Shows\Pages\ListShows;
use App\Filament\Resources\Shows\ShowResource;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Show;
use Database\Factories\UserFactory;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminRecordPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_clicking_a_show_opens_a_read_only_preview_with_an_edit_link(): void
    {
        $this->actingAs(UserFactory::new()->admin()->create());
        $show = Show::factory()->create([
            'title_en' => 'Admin Preview Title',
            'title_ar' => 'عنوان المعاينة',
            'description_en' => 'Complete preview description.',
            'content_type' => 'series',
            'episode_duration' => 42,
            'vimeo_url' => 'https://vimeo.com/123456789',
            'languages' => ['Arabic', 'English'],
            'availability' => ['Worldwide'],
        ]);
        $category = Category::create([
            'name_en' => 'Series',
            'name_ar' => 'مسلسلات',
            'slug' => 'series',
        ]);
        $genre = Genre::create([
            'category_id' => $category->getKey(),
            'name_en' => 'Drama',
            'name_ar' => 'دراما',
            'slug' => 'drama',
        ]);
        $show->categories()->attach($category);
        $show->genres()->attach($genre);
        $show->castMembers()->create([
            'actor_name_en' => 'Actor Name',
            'actor_name_ar' => 'اسم الممثل',
            'character_name_en' => 'Character Name',
            'character_name_ar' => 'اسم الشخصية',
        ]);
        $editUrl = ShowResource::getUrl('edit', ['record' => $show]);

        Livewire::test(ListShows::class)
            ->assertSeeHtml(sprintf(
                'wire:click.prevent.stop="mountTableAction(&#039;view&#039;, &#039;%s&#039;)"',
                $show->getKey(),
            ))
            ->assertTableActionExists(
                'view',
                function (ViewAction $action) use ($editUrl): bool {
                    $footerActions = $action->getModalFooterActions();

                    return $footerActions['edit']->getUrl() === $editUrl
                        && isset($footerActions['cancel']);
                },
                record: $show,
            )
            ->assertTableActionHasUrl('edit', $editUrl, $show)
            ->mountTableAction('view', $show)
            ->assertSchemaComponentExists(
                'title_en',
                checkComponentUsing: fn (TextEntry $entry): bool => $entry->getLabel() === 'Title (English)',
            )
            ->assertSchemaComponentStateSet('title_en', $show->title_en)
            ->assertSchemaComponentStateSet('description_en', $show->description_en)
            ->assertSchemaComponentStateSet('title_ar', $show->title_ar)
            ->assertSchemaComponentStateSet('meta_title_en', $show->meta_title_en)
            ->assertSchemaComponentStateSet('meta_title_ar', $show->meta_title_ar)
            ->assertSchemaComponentStateSet('meta_description_en', $show->meta_description_en)
            ->assertSchemaComponentStateSet('meta_description_ar', $show->meta_description_ar)
            ->assertSchemaComponentStateSet('id', $show->getKey())
            ->assertSchemaComponentStateSet('created_at', $show->created_at)
            ->assertSchemaComponentStateSet('updated_at', $show->updated_at)
            ->assertSchemaComponentExists(
                'status',
                checkComponentUsing: fn (TextEntry $entry): bool => $entry->isBadge()
                    && $entry->getColor('draft') === 'gray'
                    && $entry->getColor('scheduled') === 'warning'
                    && $entry->getColor('published') === 'success',
            )
            ->assertSchemaComponentStateSet('categories', 'Series / مسلسلات')
            ->assertSchemaComponentStateSet('genres', 'Drama / دراما')
            ->assertSchemaComponentStateSet('languages', ['Arabic', 'English'])
            ->assertSchemaComponentStateSet('episode_duration', 42)
            ->assertSchemaComponentStateSet('vimeo_url', 'https://vimeo.com/123456789')
            ->assertSchemaComponentStateSet('availability', ['Worldwide'])
            ->assertSchemaComponentExists('castMembers')
            ->assertSchemaComponentStateSet('castMembers', [[
                'photo_path' => null,
                'actor_name_en' => 'Actor Name',
                'actor_name_ar' => 'اسم الممثل',
                'character_name_en' => 'Character Name',
                'character_name_ar' => 'اسم الشخصية',
            ]]);
    }
}
