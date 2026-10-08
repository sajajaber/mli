<?php

namespace App\Filament\Resources\MediaServices\Pages;

use App\Filament\Resources\MediaServices\MediaServiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMediaService extends EditRecord
{
    protected static string $resource = MediaServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
