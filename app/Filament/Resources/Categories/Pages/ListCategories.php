<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    /**
     * Category IDs whose subcategories are currently collapsed.
     *
     * @var array<string, bool>
     */
    public array $collapsedCategoryIds = [];

    public function toggleSubcategories(int|string $categoryId): void
    {
        $categoryId = (string) $categoryId;

        if (isset($this->collapsedCategoryIds[$categoryId])) {
            unset($this->collapsedCategoryIds[$categoryId]);

            return;
        }

        $this->collapsedCategoryIds[$categoryId] = true;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
