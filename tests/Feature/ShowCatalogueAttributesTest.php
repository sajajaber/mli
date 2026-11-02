<?php

namespace Tests\Feature;

use App\Filament\Resources\Shows\Pages\CreateShow;
use App\Filament\Resources\Shows\ShowResource;
use App\Models\Show;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ShowCatalogueAttributesTest extends TestCase
{
    use RefreshDatabase;

    public function test_languages_and_availability_are_searchable_multi_selects_with_custom_options(): void
    {
        $fields = [];
        $collectFields = function (array $components) use (&$collectFields, &$fields): void {
            foreach ($components as $component) {
                if ($component instanceof Select) {
                    $fields[$component->getName()] = $component;
                }

                if ($component instanceof TextInput) {
                    $fields[$component->getName()] = $component;
                }

                if ($component instanceof Grid || $component instanceof Section) {
                    foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                        $collectFields($childSchema->getComponents(withHidden: true));
                    }
                }
            }
        };
        $collectFields(
            ShowResource::form(Schema::make(app(CreateShow::class)))
                ->getComponents(withHidden: true),
        );

        $languageField = $fields['languages'] ?? null;
        $availabilityField = $fields['availability'] ?? null;
        $episodeDurationField = $fields['episode_duration'] ?? null;
        $vimeoUrlField = $fields['vimeo_url'] ?? null;

        $this->assertInstanceOf(Select::class, $languageField);
        $this->assertInstanceOf(Select::class, $availabilityField);
        $this->assertInstanceOf(TextInput::class, $episodeDurationField);
        $this->assertInstanceOf(TextInput::class, $vimeoUrlField);
        $this->assertArrayNotHasKey('watch_url', $fields);
        $this->assertSame('Episode Duration', $episodeDurationField->getLabel());
        $this->assertSame('Vimeo URL', $vimeoUrlField->getLabel());

        foreach ([
            [$languageField, 'Kurdish Sorani'],
            [$availabilityField, 'North Africa'],
        ] as [$field, $customValue]) {
            $this->assertTrue($field->isMultiple());
            $this->assertTrue($field->isSearchable());
            $this->assertTrue($field->isPreloaded());
            $this->assertNotNull($field->getCreateOptionUsing());
            $this->assertSame(
                $customValue,
                ($field->getCreateOptionUsing())(['value' => " {$customValue} "]),
            );
        }

        $this->assertSame('Arabic', $languageField->getOptions()['Arabic']);
        $this->assertSame('French', $languageField->getOptions()['French']);
        $this->assertSame('Worldwide', $availabilityField->getOptions()['Worldwide']);
    }

    public function test_catalogue_attributes_are_persisted_and_displayed(): void
    {
        $show = Show::create([
            'title_en' => 'Custom Catalogue Values',
            'title_ar' => 'قيم كتالوج مخصصة',
            'description_en' => 'A test show.',
            'description_ar' => 'برنامج تجريبي.',
            'content_type' => 'general',
            'languages' => ['Arabic', 'Kurdish Sorani'],
            'availability' => ['Worldwide', 'North Africa'],
            'episode_duration' => 42,
            'vimeo_url' => 'https://vimeo.com/123456789',
            'status' => 'published',
            'published_at' => now(),
            'is_new_release' => false,
            'sort_order' => 0,
        ]);

        $show->refresh();

        $this->assertSame(['Arabic', 'Kurdish Sorani'], $show->languages);
        $this->assertSame(['Worldwide', 'North Africa'], $show->availability);

        $show->update([
            'languages' => ['English', 'Kurdish Sorani'],
            'availability' => ['MENA', 'North Africa'],
        ]);
        DB::table('shows')->where('id', $show->getKey())->update([
            'watch_url' => 'https://example.com/legacy-watch',
        ]);
        $show->refresh();

        $this->assertSame(['English', 'Kurdish Sorani'], $show->languages);
        $this->assertSame(['MENA', 'North Africa'], $show->availability);

        $this->get(route('shows.show', $show->slug))
            ->assertOk()
            ->assertSee('English · Kurdish Sorani')
            ->assertSee('MENA · North Africa')
            ->assertSee('EPISODE DURATION')
            ->assertSee('42 min')
            ->assertSee('href="https://vimeo.com/123456789"', false)
            ->assertSee('Watch here')
            ->assertDontSee('Watch trailer')
            ->assertDontSee('https://example.com/legacy-watch');
    }

    public function test_common_languages_are_automatically_translated_on_the_arabic_show_page(): void
    {
        $show = Show::factory()->published()->create([
            'languages' => ['Arabic', 'English', 'French', 'Kurdish Sorani'],
            'episode_duration' => 42,
            'vimeo_url' => 'https://vimeo.com/987654321',
        ]);

        $this->withSession(['locale' => 'ar'])
            ->get(route('shows.show', $show->slug))
            ->assertOk()
            ->assertSee('العربية · الإنجليزية · الفرنسية · Kurdish Sorani')
            ->assertDontSee('Arabic · English · French · Kurdish Sorani')
            ->assertSee('مدة الحلقة')
            ->assertSee('42 دقيقة')
            ->assertSee('شاهد هنا')
            ->assertSee('href="https://vimeo.com/987654321"', false);
    }
}
