<?php

namespace App\Filament\Resources\Shows\Pages;

use App\Filament\Resources\Shows\ShowResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Support\Icons\Heroicon;
use Filament\Resources\Pages\ListRecords;

class ListShows extends ListRecords
{
    protected static string $resource = ShowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reorderNewReleases')
                ->label('Reorder New Releases')
                ->icon(Heroicon::ArrowsUpDown)
                ->color('gray')
                ->action(function (): void {
                    $this->tableFilters = [
                        'is_new_release' => ['value' => '1'],
                        'status' => ['value' => 'published'],
                    ];

                    $this->getTableFiltersForm()->fill($this->tableFilters);
                    $this->resetPage();
                    $this->toggleTableReordering();
                ]),

            CreateAction::make(),
        ];
    }
}
