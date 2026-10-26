<?php

namespace Tests\Feature;

use App\Models\Show;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeriesCastMembersTest extends TestCase
{
    use RefreshDatabase;

    public function test_series_cast_members_render_with_actor_photos_and_character_names(): void
    {
        $show = $this->makeShow('Series With Cast', 'series');

        $show->castMembers()->create([
            'actor_name_en' => 'Maya Haddad',
            'actor_name_ar' => 'مايا حداد',
            'character_name_en' => 'Lina Mansour',
            'character_name_ar' => 'لينا منصور',
            'photo_path' => 'cast/maya-haddad.jpg',
            'sort_order' => 0,
        ]);

        $this->get(route('shows.show', $show->slug))
            ->assertOk()
            ->assertSee('The Cast')
            ->assertSee('Maya Haddad')
            ->assertSee('Lina Mansour')
            ->assertSee('/storage/cast/maya-haddad.jpg')
            ->assertSee('alt="Maya Haddad"', false);
    }

    public function test_cast_profiles_are_not_rendered_for_non_series_content(): void
    {
        $show = $this->makeShow('General Content With Cast', 'general');

        $show->castMembers()->create([
            'actor_name_en' => 'Hidden Test Actor',
            'character_name_en' => 'Hidden Test Character',
            'photo_path' => 'cast/hidden-test-actor.jpg',
            'sort_order' => 0,
        ]);

        $this->get(route('shows.show', $show->slug))
            ->assertOk()
            ->assertDontSee('Hidden Test Actor')
            ->assertDontSee('Hidden Test Character')
            ->assertDontSee('mli-show-cast', false);
    }

    private function makeShow(string $title, string $contentType): Show
    {
        return Show::create([
            'title_en' => $title,
            'title_ar' => $title . ' AR',
            'description_en' => 'A test show.',
            'description_ar' => 'برنامج تجريبي.',
            'content_type' => $contentType,
            'status' => 'published',
            'published_at' => now(),
            'is_new_release' => false,
            'sort_order' => 0,
        ]);
    }
}
