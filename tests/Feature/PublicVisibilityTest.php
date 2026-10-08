<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\Show;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_public_pages_are_successful(): void
    {
        $this->get('/')->assertOk();
        $this->get(route('shows.index'))->assertOk();
        $this->get(route('news.index'))->assertOk();
    }

    public function test_published_show_is_public_but_draft_scheduled_and_deleted_shows_are_hidden(): void
    {
        $published = $this->makeShow('Published Show', 'published');
        $draft = $this->makeShow('Draft Show', 'draft');
        $scheduled = $this->makeShow('Scheduled Show', 'scheduled', now()->addDay());
        $deleted = $this->makeShow('Deleted Show', 'published');
        $deleted->delete();

        $this->get(route('shows.show', $published->slug))
            ->assertOk()
            ->assertSee($published->title_en);

        foreach ([$draft, $scheduled, $deleted] as $hidden) {
            $this->get(route('shows.show', $hidden->slug))
                ->assertNotFound();
        }

        $index = $this->get(route('shows.index'))->assertOk();

        $index->assertSee($published->title_en);
        $index->assertDontSee($draft->title_en);
        $index->assertDontSee($scheduled->title_en);
        $index->assertDontSee($deleted->title_en);
    }

    public function test_published_news_is_public_but_draft_scheduled_and_deleted_news_are_hidden(): void
    {
        $published = $this->makeNews('Published News', 'published');
        $draft = $this->makeNews('Draft News', 'draft');
        $scheduled = $this->makeNews('Scheduled News', 'scheduled', now()->addDay());
        $deleted = $this->makeNews('Deleted News', 'published');
        $deleted->delete();

        $this->get(route('news.show', $published->slug))
            ->assertOk()
            ->assertSee($published->title_en);

        foreach ([$draft, $scheduled, $deleted] as $hidden) {
            $this->get(route('news.show', $hidden->slug))
                ->assertNotFound();
        }

        $index = $this->get(route('news.index'))->assertOk();

        $index->assertSee($published->title_en);
        $index->assertDontSee($draft->title_en);
        $index->assertDontSee($scheduled->title_en);
        $index->assertDontSee($deleted->title_en);
    }

    public function test_homepage_only_exposes_published_new_releases(): void
    {
        $publishedNewRelease = $this->makeShow('Published New Release', 'published');
        $publishedNewRelease->update(['is_new_release' => true]);

        $publishedRegular = $this->makeShow('Published Regular Show', 'published');
        $publishedRegular->update(['is_new_release' => false]);

        $draftNewRelease = $this->makeShow('Draft New Release', 'draft');
        $draftNewRelease->update(['is_new_release' => true]);

        $this->get('/')
            ->assertOk()
            ->assertSee($publishedNewRelease->title_en)
            ->assertDontSee($publishedRegular->title_en)
            ->assertDontSee($draftNewRelease->title_en);
    }

    public function test_homepage_library_marquee_renders_a_loop_copy_for_the_client_side_fit_check(): void
    {
        $shows = [
            $showOne = $this->makeShow('Featured Show One', 'published'),
            $showTwo = $this->makeShow('Featured Show Two', 'published'),
            $showThree = $this->makeShow('Featured Show Three', 'published'),
            $showFour = $this->makeShow('Featured Show Four', 'published'),
            $showFive = $this->makeShow('Featured Show Five', 'published'),
        ];

        foreach ($shows as $show) {
            $show->update(['is_new_release' => true]);
        }

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('data-library-marquee', false);
        $response->assertSee('mli-library-marquee__track');
        $this->assertSame(count($shows), substr_count($response->getContent(), 'data-marquee-clone'));

        foreach ($shows as $show) {
            $response->assertSee($show->title_en);
        }
    }

    public function test_guest_users_are_redirected_from_the_admin_panel(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_non_admin_users_are_blocked_from_the_admin_panel(): void
    {
        $this->actingAs(UserFactory::new()->create())
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_users_can_access_the_panel_in_production_environment(): void
    {
        app()->detectEnvironment(fn () => 'production');

        $this->actingAs(UserFactory::new()->admin()->create())
            ->get('/admin')
            ->assertOk();
    }

    public function test_published_news_renders_sanitized_html_for_untrusted_content(): void
    {
        $news = $this->makeNews('Unsafe News', 'published');
        $news->update([
            'body_en' => '<p>Safe <strong>copy</strong></p><script>alert(1)</script><img src=x onerror="alert(2)"><a href="javascript:alert(3)" onclick="alert(4)">link</a>',
        ]);

        $response = $this->get(route('news.show', $news->slug));

        $response->assertOk();

        $content = $response->getContent();
        $this->assertStringContainsString('Safe', $content);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $content);
        $this->assertStringNotContainsString('onerror=', $content);
        $this->assertStringNotContainsString('onclick=', $content);
        $this->assertStringNotContainsString('javascript:alert(3)', $content);
    }

    public function test_public_pages_render_rtl_when_arabic_locale_is_selected(): void
    {
        $show = $this->makeShow('Arabic Show', 'published');
        $news = $this->makeNews('Arabic News', 'published');

        $this->withSession(['locale' => 'ar'])
            ->get(route('shows.show', $show->slug))
            ->assertOk()
            ->assertSee('dir="rtl"', false);

        $this->withSession(['locale' => 'ar'])
            ->get(route('news.show', $news->slug))
            ->assertOk()
            ->assertSee('dir="rtl"', false);
    }

    private function makeShow(
        string $title,
        string $status,
        ?\DateTimeInterface $publishedAt = null,
    ): Show {
        return Show::create([
            'title_en' => $title,
            'title_ar' => $title . ' AR',
            'description_en' => 'A test show.',
            'description_ar' => 'برنامج تجريبي.',
            'slug' => strtolower(str_replace(' ', '-', $title)),
            'status' => $status,
            'published_at' => $publishedAt ?? ($status === 'published' ? now() : null),
            'is_new_release' => false,
            'sort_order' => 0,
        ]);
    }

    private function makeNews(
        string $title,
        string $status,
        ?\DateTimeInterface $publishedAt = null,
    ): News {
        return News::create([
            'title_en' => $title,
            'title_ar' => $title . ' AR',
            'body_en' => 'A test news article.',
            'body_ar' => 'خبر تجريبي.',
            'slug' => strtolower(str_replace(' ', '-', $title)),
            'news_type' => 'mli_news',
            'status' => $status,
            'published_at' => $publishedAt ?? ($status === 'published' ? now() : null),
        ]);
    }
}
