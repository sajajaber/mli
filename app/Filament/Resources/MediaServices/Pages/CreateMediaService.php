<?php

namespace App\Filament\Resources\MediaServices\Pages;

use App\Filament\Resources\MediaServices\MediaServiceResource;
use App\Models\MediaService;
use Filament\Resources\Pages\CreateRecord;

class CreateMediaService extends CreateRecord
{
    protected static string $resource = MediaServiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sort_order'] = (int) MediaService::max('sort_order') + 1;

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
        return MediaServiceResource::getUrl('index');
    }
}
