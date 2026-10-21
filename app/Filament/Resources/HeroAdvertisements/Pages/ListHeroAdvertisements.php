<?php

namespace App\Filament\Resources\HeroAdvertisements\Pages;

use Filament\Actions\Action;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
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
            ->map(fn($id) => (int) $id)
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

    public function toggleHeroActive(int $id): void
    {
        $hero = HeroAdvertisement::findOrFail($id);

        $hero->update([
            'is_active' => ! $hero->is_active,
        ]);

        Notification::make()
            ->title('Hero visibility updated')
            ->success()
            ->send();
    }

    public function removeHeroImage(int $id): void
    {
        $hero = HeroAdvertisement::findOrFail($id);
        $imagePath = $hero->image_path;

        if (blank($imagePath)) {
            return;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($imagePath) && ! $disk->delete($imagePath)) {
            throw new RuntimeException(
                'The hero image could not be deleted.'
            );
        }

        $hero->update([
            'image_path' => null,
        ]);

        Notification::make()
            ->title('Hero image removed')
            ->success()
            ->send();
    }

    public function deleteHeroAdvertisement(int $id): void
    {
        $hero = HeroAdvertisement::query()->findOrFail($id);

        $imagePath = $hero->image_path;

        // Delete the database record.
        $hero->delete();

        // Delete the actual uploaded image from public storage.
        if (filled($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        Notification::make()
            ->title('Hero advertisement deleted')
            ->success()
            ->send();
    }
}
