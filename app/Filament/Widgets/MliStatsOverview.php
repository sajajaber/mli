<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\News;
use App\Models\Person;
use App\Models\Show;
use App\Models\SiteContent;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MliStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $draftCount = Show::where('status', 'draft')->count()
            + News::where('status', 'draft')->count()
            + SiteContent::where('status', 'draft')->count();

        $scheduledCount = Show::where('status', 'scheduled')->count()
            + News::where('status', 'scheduled')->count()
            + SiteContent::where('status', 'scheduled')->count();

        $nextScheduled = collect([
            Show::where('status', 'scheduled')->min('published_at'),
            News::where('status', 'scheduled')->min('published_at'),
            SiteContent::where('status', 'scheduled')->min('published_at'),
        ])
            ->filter()
            ->sort()
            ->first();

        $addedThisWeek = Show::where('created_at', '>=', now()->subDays(7))->count()
            + News::where('created_at', '>=', now()->subDays(7))->count();

        return [
            Stat::make('Live on the Site', Show::where('status', 'published')->count() + News::where('status', 'published')->count())
                ->description('Published shows + news articles')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('success'),

            Stat::make('Needs Attention', $draftCount)
                ->description($draftCount > 0 ? 'Drafts waiting to be finished or published' : 'All caught up')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color($draftCount > 0 ? 'danger' : 'success'),

            Stat::make('Publishing Soon', $scheduledCount)
                ->description($nextScheduled ? 'Next: ' . \Illuminate\Support\Carbon::parse($nextScheduled)->format('M j, g:ia') : 'Nothing scheduled')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            Stat::make('Added This Week', $addedThisWeek)
                ->description('New shows + news in the last 7 days')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('gray'),

            Stat::make('Active Team Members', Person::where('is_active', true)->count())
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),

            Stat::make('Clients', Client::count())
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('gray'),
        ];
    }
}
