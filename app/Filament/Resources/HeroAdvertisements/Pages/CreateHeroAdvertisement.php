<?php

namespace App\Filament\Resources\HeroAdvertisements\Pages;

use App\Filament\Resources\HeroAdvertisements\HeroAdvertisementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHeroAdvertisement extends CreateRecord
{
    protected static string $resource = HeroAdvertisementResource::class;

    protected function getRedirectUrl(): string
    {
        return HeroAdvertisementResource::getUrl('index');
    }
}
