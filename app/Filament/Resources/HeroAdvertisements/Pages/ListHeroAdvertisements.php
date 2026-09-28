<?php

namespace App\Filament\Resources\HeroAdvertisements\Pages;

use App\Filament\Resources\HeroAdvertisements\HeroAdvertisementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHeroAdvertisements extends ListRecords
{
    protected static string $resource = HeroAdvertisementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Hero Advertisement')
                ->icon('heroicon-o-plus'),
        ];
    }
}
