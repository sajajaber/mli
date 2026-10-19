<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Concerns\ValidatesOnBlur;
use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use UnitEnum;

class CategoryResource extends Resource
{
    use ValidatesOnBlur;

    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Categories';

    protected static UnitEnum|string|null $navigationGroup = 'Library Setup';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByRaw('COALESCE((SELECT parent_categories.name_en FROM categories AS parent_categories WHERE parent_categories.id = categories.parent_id), categories.name_en)')
            ->orderByRaw('CASE WHEN categories.parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('categories.name_en');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Category Information')
                ->description('Create a parent category or place a subcategory under an existing parent.')
                ->columnSpan(['default' => 1, 'xl' => 11])
                ->columns(2)
                ->components([
                    TextInput::make('name_en')
                        ->label('Name (English)')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->validationMessages([
                            'unique' => 'A category with this English name already exists.',
                        ])
                        ->live(onBlur: true)
                        ->afterStateUpdated(static::validateOnBlur())
                        ->afterStateUpdated(function (string $state, callable $set, string $operation) {
                            if ($operation === 'create') {
                                $set('slug', Str::slug($state));
                            }
                        }),

                    TextInput::make('name_ar')
                        ->label('Name (Arabic)')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->validationMessages([
                            'unique' => 'A category with this Arabic name already exists.',
                        ])
                        ->extraInputAttributes(['dir' => 'rtl'])
                        ->live(onBlur: true)
                        ->afterStateUpdated(static::validateOnBlur()),

                    Select::make('parent_id')
                        ->label('Parent category (optional)')
                        ->helperText('Leave this empty to create a parent category. Choose a parent only if this is a subcategory.')
                        ->relationship(
                            'parent',
                            'name_en',
                            function (Builder $query, ?Category $record): void {
                                $query->whereNull('parent_id');

                                if ($record) {
                                    $query->whereKeyNot($record->getKey());
                                }
                            },
                        )
                        ->getOptionLabelFromRecordUsing(
                            fn (Category $record): string => "{$record->name_en} / {$record->name_ar}",
                        )
                        ->searchable()
                        ->preload()
                        ->placeholder('No parent — top-level category'),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->live(onBlur: true)
                        ->afterStateUpdated(static::validateOnBlur())
                        ->helperText('Auto-filled from the English name — edit if you want a custom URL segment.')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_en')
                    ->label('Category / Subcategory (English)')
                    ->formatStateUsing(
                        fn (string $state, Category $record): string => $record->parent_id
                            ? '↳ ' . $state
                            : $state,
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name_ar')
                    ->label('Name (Arabic)')
                    ->searchable(),

                TextColumn::make('parent.name_en')
                    ->label('Parent')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('parent_id')
                    ->label('Level')
                    ->state(fn (Category $record): string => $record->parent_id ? 'Subcategory' : 'Parent category')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Parent category' ? 'primary' : 'gray'),

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
            ->filters([])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make()
                    ->modalDescription('Deleting a category also deletes its sub-categories and removes them from shows.'),
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
