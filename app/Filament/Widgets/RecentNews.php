<?php

namespace App\Filament\Widgets;

use App\Models\News;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentNews extends BaseWidget
{
    protected static ?string $heading = 'Recently Added News';

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(News::query()->latest()->limit(5))
            ->columns([
                TextColumn::make('title_en')->label('Title'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'published' => 'success',
                    }),
                TextColumn::make('created_at')->label('Added')->since()->sortable(),
            ]);
    }
}
