<?php

namespace App\Filament\Resources\SiteContents\Pages;

use App\Filament\Resources\SiteContents\SiteContentResource;
use App\Filament\Resources\SiteContents\Widgets\FooterSettingsRow;
use Filament\Resources\Pages\ListRecords;

class ListSiteContents extends ListRecords
{
    protected static string $resource = SiteContentResource::class;
    protected function getFooterWidgets(): array
    {
        return [
            FooterSettingsRow::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }
}
