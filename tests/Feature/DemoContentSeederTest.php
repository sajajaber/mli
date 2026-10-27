<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\Show;
use Database\Seeders\DemoContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_content_seeder_adds_sample_content_and_is_idempotent(): void
    {
        $this->seed(DemoContentSeeder::class);

        $showSlugs = [
            'voices-of-the-coast', 'the-olive-season', 'across-the-levant',
            'the-cedar-house', 'between-two-cities', 'the-ninth-hour',
        ];
        $newsSlugs = [
            'demo-a-new-chapter-for-regional-storytelling',
            'demo-inside-the-work-behind-every-programme',
            'demo-building-a-library-for-every-screen',
            'demo-why-factual-stories-keep-finding-an-audience',
            'demo-the-changing-language-of-content-distribution',
            'demo-what-viewers-look-for-in-a-new-series',
        ];

        $this->assertSame(6, Show::query()->whereIn('slug', $showSlugs)->count());
        $this->assertSame(6, News::query()->whereIn('slug', $newsSlugs)->count());
        $this->assertSame(6, Show::query()->whereIn('slug', $showSlugs)->where('status', 'published')->count());
        $this->assertSame(6, News::query()->whereIn('slug', $newsSlugs)->where('status', 'published')->count());

        $series = Show::query()->where('slug', 'the-cedar-house')->firstOrFail();
        $this->assertSame('series', $series->content_type);
        $this->assertGreaterThanOrEqual(2, $series->genres()->count());
        $this->assertSame(3, $series->castMembers()->count());

        $this->seed(DemoContentSeeder::class);
        $this->assertSame(6, Show::query()->whereIn('slug', $showSlugs)->count());
        $this->assertSame(6, News::query()->whereIn('slug', $newsSlugs)->count());
        $this->assertSame(3, $series->castMembers()->count());
    }
}
