<?php

namespace App\Filament\Resources\Shows\Pages;

use App\Filament\Resources\Shows\ShowResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShow extends CreateRecord
{
    protected static string $resource = ShowResource::class;

    protected function getRedirectUrl(): string
    {
        return ShowResource::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
