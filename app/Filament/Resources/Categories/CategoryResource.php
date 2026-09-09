<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Categories';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name_en')
                ->label('Name (English)')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function (string $state, callable $set, string $operation) {
                    // Only auto-fill slug while creating, never overwrite on edit
                    if ($operation === 'create') {
                        $set('slug', Str::slug($state));
                    }
                }),

            TextInput::make('name_ar')
                ->label('Name (Arabic)')
                ->required()
                ->maxLength(255)
                ->extraInputAttributes(['dir' => 'rtl']),

            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->helperText('Auto-filled from the English name — edit if you want a custom URL segment.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_en')
                    ->label('Name (English)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name_ar')
                    ->label('Name (Arabic)')
                    ->searchable(),

                TextColumn::make('slug')
                    ->searchable(),

                TextColumn::make('shows_count')
                    ->label('Shows')
                    ->counts('shows')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name_en')
            ->filters([])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
