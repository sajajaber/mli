<?php

namespace App\Filament\Resources\HeroAdvertisements\Pages;

use App\Filament\Resources\HeroAdvertisements\HeroAdvertisementResource;
use App\Models\HeroAdvertisement;
use Filament\Resources\Pages\CreateRecord;

class CreateHeroAdvertisement extends CreateRecord
{
    protected static string $resource = HeroAdvertisementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sort_order'] = (int) HeroAdvertisement::max('sort_order') + 1;

        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return HeroAdvertisementResource::getUrl('index');
    }
}
