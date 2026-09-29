<x-filament-panels::page>
    <div class="mli-admin-dashboard-widgets">
        @livewire(\App\Filament\Widgets\MliStatsOverview::class)
        @livewire(\App\Filament\Widgets\RecentContent::class)
        @livewire(\App\Filament\Widgets\RecentNews::class)
    </div>
</x-filament-panels::page>
