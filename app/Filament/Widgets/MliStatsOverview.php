<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\News;
use App\Models\Person;
use App\Models\Show;
use App\Models\SiteContent;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

class MliStatsOverview extends Widget
{
    protected string $view = 'filament.widgets.mli-stats-overview';

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function getDashboardData(): array
    {
        return Cache::remember(
            'admin.dashboard.overview',
            now()->addSeconds(30),
            function (): array {
                $weekStart = now()->subDays(7);

                $shows = Show::query()
                    ->selectRaw(
                        "
                        COALESCE(SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END), 0) AS draft_count,
                        COALESCE(SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END), 0) AS scheduled_count,
                        COALESCE(SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END), 0) AS published_count,
                        COALESCE(SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END), 0) AS added_this_week
                        ",
                        [$weekStart]
                    )
                    ->first();

                $news = News::query()
                    ->selectRaw(
                        "
                        COALESCE(SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END), 0) AS draft_count,
                        COALESCE(SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END), 0) AS scheduled_count,
                        COALESCE(SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END), 0) AS published_count,
                        COALESCE(SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END), 0) AS added_this_week
                        ",
                        [$weekStart]
                    )
                    ->first();

                $siteContent = SiteContent::query()
                    ->selectRaw(
                        "
                        COALESCE(SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END), 0) AS draft_count,
                        COALESCE(SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END), 0) AS scheduled_count
                        "
                    )
                    ->first();

                $upcomingShows = Show::query()
                    ->where('status', 'scheduled')
                    ->whereNotNull('published_at')
                    ->where('published_at', '>=', now())
                    ->orderBy('published_at')
                    ->limit(3)
                    ->get(['title_en', 'published_at']);

                $upcomingNews = News::query()
                    ->where('status', 'scheduled')
                    ->whereNotNull('published_at')
                    ->where('published_at', '>=', now())
                    ->orderBy('published_at')
                    ->limit(3)
                    ->get(['title_en', 'published_at']);

                $upcoming = $upcomingShows
                    ->map(fn (Show $item) => [
                        'type' => 'Show',
                        'title' => $item->title_en,
                        'published_at' => $item->published_at,
                    ])
                    ->concat($upcomingNews->map(fn (News $item) => [
                        'type' => 'News',
                        'title' => $item->title_en,
                        'published_at' => $item->published_at,
                    ]))
                    ->sortBy('published_at')
                    ->take(4)
                    ->values();

                $recentShows = Show::query()
                    ->latest('created_at')
                    ->limit(3)
                    ->get(['title_en', 'status', 'created_at']);

                $recentNews = News::query()
                    ->latest('created_at')
                    ->limit(3)
                    ->get(['title_en', 'status', 'created_at']);

                $recent = $recentShows
                    ->map(fn (Show $item) => [
                        'type' => 'Show',
                        'title' => $item->title_en,
                        'status' => $item->status,
                        'created_at' => $item->created_at,
                    ])
                    ->concat($recentNews->map(fn (News $item) => [
                        'type' => 'News',
                        'title' => $item->title_en,
                        'status' => $item->status,
                        'created_at' => $item->created_at,
                    ]))
                    ->sortByDesc('created_at')
                    ->take(5)
                    ->values();

                return [
                    'live_count' => (int) ($shows?->published_count ?? 0) + (int) ($news?->published_count ?? 0),
                    'draft_count' => (int) ($shows?->draft_count ?? 0) + (int) ($news?->draft_count ?? 0) + (int) ($siteContent?->draft_count ?? 0),
                    'scheduled_count' => (int) ($shows?->scheduled_count ?? 0) + (int) ($news?->scheduled_count ?? 0) + (int) ($siteContent?->scheduled_count ?? 0),
                    'added_this_week' => (int) ($shows?->added_this_week ?? 0) + (int) ($news?->added_this_week ?? 0),
                    'shows_count' => Show::count(),
                    'news_count' => News::count(),
                    'active_team_members' => Person::where('is_active', true)->count(),
                    'clients' => Client::count(),
                    'recent' => $recent,
                    'upcoming' => $upcoming,
                ];
            }
        );
    }
}
