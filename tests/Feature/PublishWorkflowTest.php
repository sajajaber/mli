<?php

namespace Tests\Feature;

use App\Filament\Resources\News\Pages\ListNews;
use App\Filament\Resources\Shows\Pages\ListShows;
use App\Models\News;
use App\Models\Show;
use App\Models\SiteContent;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PublishWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_show_with_a_taken_slug_uses_a_numeric_suffix(): void
    {
        Show::factory()->create([
            'title_en' => 'Alpha Story',
            'slug' => 'alpha-story',
        ]);

        $duplicate = Show::factory()->create([
            'title_en' => 'Alpha Story',
            'slug' => null,
        ]);

        $this->assertSame('alpha-story-2', $duplicate->fresh()->slug);
    }

    public function test_updating_a_show_title_regenerates_a_colliding_slug(): void
    {
        $existing = Show::factory()->create([
            'title_en' => 'Alpha Story',
            'slug' => 'alpha-story',
        ]);

        $duplicate = Show::factory()->create([
            'title_en' => 'Beta Story',
            'slug' => 'beta-story',
        ]);

        $duplicate->title_en = 'Alpha Story';
        $duplicate->save();

        $this->assertSame('alpha-story-2', $duplicate->fresh()->slug);
        $this->assertSame('alpha-story', $existing->fresh()->slug);
    }

    public function test_publish_action_publishes_a_show_and_sets_published_at(): void
    {
        $this->actingAs(UserFactory::new()->create());
        $show = Show::factory()->create(['status' => 'draft', 'published_at' => null]);

        Livewire::test(ListShows::class)
            ->callTableAction('publish', $show);

        $show->refresh();

        $this->assertSame('published', $show->status);
        $this->assertNotNull($show->published_at);
        $this->get(route('shows.show', $show->slug))->assertOk();
    }

    public function test_publish_action_publishes_news_and_sets_published_at(): void
    {
        $this->actingAs(UserFactory::new()->create());
        $news = News::factory()->create(['status' => 'draft', 'published_at' => null]);

        Livewire::test(ListNews::class)
            ->callTableAction('publish', $news);

        $news->refresh();

        $this->assertSame('published', $news->status);
        $this->assertNotNull($news->published_at);
        $this->get(route('news.show', $news->slug))->assertOk();
    }

    public function test_publish_and_schedule_actions_are_hidden_for_published_shows(): void
    {
        $this->actingAs(UserFactory::new()->create());
        $show = Show::factory()->published()->create();

        Livewire::test(ListShows::class)
            ->assertTableActionHidden('publish', $show)
            ->assertTableActionHidden('schedule', $show);
    }

    public function test_scheduling_a_show_keeps_it_hidden_until_the_scheduled_time(): void
    {
        $this->actingAs(UserFactory::new()->create());
        $show = Show::factory()->create(['status' => 'draft', 'published_at' => null]);
        $beirut = now('Asia/Beirut')->addHour()->startOfMinute();

        Livewire::test(ListShows::class)
            ->assertTableActionVisible('schedule', $show)
            ->mountTableAction('schedule', $show)
            ->setTableActionData([
                'published_at' => $beirut->format('Y-m-d H:i'),
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $show->refresh();

        $this->assertSame('scheduled', $show->status);
        $this->assertEquals($beirut->timestamp, $show->published_at->timestamp);
        $this->get(route('shows.show', $show->slug))->assertNotFound();
    }

    public function test_unpublish_action_takes_a_show_off_the_public_site(): void
    {
        $this->actingAs(UserFactory::new()->create());
        $show = Show::factory()->published()->create();

        $this->get(route('shows.show', $show->slug))->assertOk();

        Livewire::test(ListShows::class)
            ->callTableAction('unpublish', $show);

        $show->refresh();

        $this->assertSame('draft', $show->status);
        $this->assertNull($show->published_at);
        $this->get(route('shows.show', $show->slug))->assertNotFound();
    }

    public function test_due_scheduled_items_are_auto_published_when_the_public_query_runs(): void
    {
        $show = Show::factory()->scheduled(now()->subMinute())->create();
        $news = News::factory()->scheduled(now()->subMinute())->create();

        $this->assertNotNull(Show::query()->published()->whereKey($show->getKey())->first());
        $this->assertNotNull(News::query()->published()->whereKey($news->getKey())->first());

        $this->assertSame('published', $show->fresh()->status);
        $this->assertSame('published', $news->fresh()->status);
    }

    public function test_scheduler_publishes_only_due_items_and_is_idempotent(): void
    {
        $dueShow = Show::factory()->scheduled(now()->subMinute())->create();
        $futureShow = Show::factory()->scheduled(now()->addHour())->create();
        $draftShow = Show::factory()->create(['status' => 'draft']);

        $dueNews = News::factory()->scheduled(now()->subMinute())->create();
        $futureNews = News::factory()->scheduled(now()->addHour())->create();
        $draftNews = News::factory()->create(['status' => 'draft']);

        $liveContent = SiteContent::factory()->published()->create([
            'key' => 'workflow-test',
        ]);
        $dueContent = SiteContent::factory()->scheduled(now()->subMinute())->create([
            'key' => $liveContent->key,
        ]);
        $futureContent = SiteContent::factory()->scheduled(now()->addHour())->create([
            'key' => 'workflow-future',
        ]);

        $this->artisan('content:publish-scheduled')
            ->assertSuccessful();

        $this->assertSame('published', $dueShow->fresh()->status);
        $this->assertSame('scheduled', $futureShow->fresh()->status);
        $this->assertSame('draft', $draftShow->fresh()->status);

        $this->assertSame('published', $dueNews->fresh()->status);
        $this->assertSame('scheduled', $futureNews->fresh()->status);
        $this->assertSame('draft', $draftNews->fresh()->status);

        $this->assertSame('published', $dueContent->fresh()->status);
        $this->assertSame('draft', $liveContent->fresh()->status);
        $this->assertSame('scheduled', $futureContent->fresh()->status);
        $this->assertSame(
            1,
            SiteContent::query()->where('key', $liveContent->key)->where('status', 'published')->count()
        );

        $publishedAt = $dueShow->fresh()->published_at->timestamp;

        $this->artisan('content:publish-scheduled')
            ->assertSuccessful();

        $this->assertSame('published', $dueShow->fresh()->status);
        $this->assertSame($publishedAt, $dueShow->fresh()->published_at->timestamp);
        $this->assertSame('scheduled', $futureShow->fresh()->status);
    }

    public function test_site_content_versions_increment_and_only_the_new_version_is_live_after_scheduling(): void
    {
        $live = SiteContent::factory()->published()->create([
            'key' => 'versioned-content',
            'version' => 1,
        ]);

        $next = SiteContent::factory()->scheduled(now()->subMinute())->create([
            'key' => $live->key,
        ]);

        $this->assertSame(2, $next->version);

        $this->artisan('content:publish-scheduled')
            ->assertSuccessful();

        $this->assertSame('draft', $live->fresh()->status);
        $this->assertSame('published', $next->fresh()->status);

        $this->assertSame(
            1,
            SiteContent::query()
                ->where('key', $live->key)
                ->where('status', 'published')
                ->count()
        );
    }

    public function test_publishing_through_the_show_edit_form_sets_published_at(): void
    {
        $this->actingAs(UserFactory::new()->create());
        $show = Show::factory()->create([
            'status' => 'draft',
            'published_at' => null,
        ]);

        Livewire::test(\App\Filament\Resources\Shows\Pages\EditShow::class, [
            'record' => $show->getKey(),
        ])
            ->fillForm([
                'status' => 'published',
                'published_at' => null,
            ])
            ->call('save');

        $show->refresh();

        $this->assertSame('published', $show->status);
        $this->assertNotNull($show->published_at);
    }

    public function test_publishing_through_the_news_edit_form_sets_published_at(): void
    {
        $this->actingAs(UserFactory::new()->create());
        $news = News::factory()->create([
            'status' => 'draft',
            'published_at' => null,
        ]);

        Livewire::test(\App\Filament\Resources\News\Pages\EditNews::class, [
            'record' => $news->getKey(),
        ])
            ->fillForm([
                'status' => 'published',
                'published_at' => null,
            ])
            ->call('save');

        $news->refresh();

        $this->assertSame('published', $news->status);
        $this->assertNotNull($news->published_at);
    }
}
