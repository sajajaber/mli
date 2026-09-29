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
    protected static string $view = 'filament.widgets.mli-stats-overview';

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function getDashboardMetrics(): array
    {
        return Cache::remember(
            'admin.dashboard.stats',
            now()->addSeconds(30),
            function (): array {
                $weekStart = now()->subDays(7);

                $shows = Show::query()
                    ->selectRaw(
                        "
                        COALESCE(SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END), 0) AS draft_count,
                        COALESCE(SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END), 0) AS scheduled_count,
                        COALESCE(SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END), 0) AS published_count,
                        MIN(CASE WHEN status = 'scheduled' THEN published_at END) AS next_scheduled,
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
                        MIN(CASE WHEN status = 'scheduled' THEN published_at END) AS next_scheduled,
                        COALESCE(SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END), 0) AS added_this_week
                        ",
                        [$weekStart]
                    )
                    ->first();

                $siteContent = SiteContent::query()
                    ->selectRaw(
                        "
                        COALESCE(SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END), 0) AS draft_count,
                        COALESCE(SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END), 0) AS scheduled_count,
                        MIN(CASE WHEN status = 'scheduled' THEN published_at END) AS next_scheduled
                        "
                    )
                    ->first();

                $nextScheduled = collect([
                    $shows?->next_scheduled,
                    $news?->next_scheduled,
                    $siteContent?->next_scheduled,
                ])->filter()->sort()->first();

                return [
                    'live_count' => (int) ($shows?->published_count ?? 0) + (int) ($news?->published_count ?? 0),
                    'draft_count' => (int) ($shows?->draft_count ?? 0) + (int) ($news?->draft_count ?? 0) + (int) ($siteContent?->draft_count ?? 0),
                    'scheduled_count' => (int) ($shows?->scheduled_count ?? 0) + (int) ($news?->scheduled_count ?? 0) + (int) ($siteContent?->scheduled_count ?? 0),
                    'next_scheduled' => $nextScheduled,
                    'added_this_week' => (int) ($shows?->added_this_week ?? 0) + (int) ($news?->added_this_week ?? 0),
                    'active_team_members' => Person::where('is_active', true)->count(),
                    'clients' => Client::count(),
                ];
            }
        );
    }
}
