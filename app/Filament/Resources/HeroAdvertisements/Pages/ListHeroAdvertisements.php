<?php

namespace App\Filament\Resources\HeroAdvertisements\Pages;

use App\Filament\Resources\HeroAdvertisements\HeroAdvertisementResource;
use App\Models\HeroAdvertisement;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListHeroAdvertisements extends ListRecords
{
    protected static string $resource = HeroAdvertisementResource::class;

    protected string $view = 'filament.resources.hero-advertisements.pages.list-hero-advertisements';

    public function getHeroAdvertisements()
    {
        return HeroAdvertisement::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function saveHeroOrder(array $ids): void
    {
        $currentIds = HeroAdvertisement::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $submittedIds = array_map('intval', $ids);

        if ($currentIds !== array_values(array_unique($submittedIds))) {
            Notification::make()
                ->title('The hero order could not be saved.')
                ->danger()
                ->send();

            return;
        }

        DB::transaction(function () use ($submittedIds): void {
            foreach ($submittedIds as $index => $id) {
                HeroAdvertisement::query()
                    ->whereKey($id)
                    ->update([
                        'sort_order' => $index + 1,
                    ]);
            }
        });

        Notification::make()
            ->title('Hero order saved')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->label('Add Hero Advertisement')
                ->icon('heroicon-o-plus'),
        ];
    }
}
