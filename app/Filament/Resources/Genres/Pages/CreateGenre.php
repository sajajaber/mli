<?php

namespace App\Filament\Resources\Genres\Pages;

use App\Filament\Resources\Genres\GenreResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGenre extends CreateRecord
{
    protected static string $resource = GenreResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return GenreResource::getUrl('index');
    }
}
