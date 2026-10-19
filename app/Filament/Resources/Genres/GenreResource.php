<?php

namespace App\Filament\Resources\Genres;

use App\Filament\Resources\Genres\Pages\CreateGenre;
use App\Filament\Resources\Genres\Pages\EditGenre;
use App\Filament\Resources\Genres\Pages\ListGenres;
use App\Models\Category;
use App\Models\Genre;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use UnitEnum;

class GenreResource extends Resource
{
    protected static ?string $model = Genre::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Genres';

    protected static UnitEnum|string|null $navigationGroup = 'Library Setup';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Genre Information')
                ->description('Create a genre and assign it to a top-level category such as Documentary or Series.')
                ->columnSpan(['default' => 1, 'xl' => 11])
                ->columns(2)
                ->components([
                    Select::make('category_id')
                        ->label('Category')
                        ->relationship(
                            'category',
                            'name_en',
                            modifyQueryUsing: fn (Builder $query) => $query->whereNull('parent_id')->orderBy('name_en'),
                        )
                        ->getOptionLabelFromRecordUsing(
                            fn (Category $record): string => "{$record->name_en} / {$record->name_ar}",
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live(),

                    TextInput::make('name_en')
                        ->label('Name (English)')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $state, callable $set, string $operation): void {
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
                        ->helperText('Auto-filled from the English name. The slug is used by the public catalogue filters.')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('category')->withCount('shows')->orderBy('category_id')->orderBy('name_en'))
            ->columns([
                TextColumn::make('name_en')
                    ->label('Genre (English)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name_ar')
                    ->label('Name (Arabic)')
                    ->searchable(),

                TextColumn::make('category.name_en')
                    ->label('Category')
                    ->badge()
                    ->sortable(),

                TextColumn::make('slug')
                    ->searchable(),

                TextColumn::make('shows_count')
                    ->label('Library Items')
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make()
                    ->modalDescription('Deleting a genre removes that genre from every assigned library item.'),
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
            'index' => ListGenres::route('/'),
            'create' => CreateGenre::route('/create'),
            'edit' => EditGenre::route('/{record}/edit'),
        ];
    }
}
