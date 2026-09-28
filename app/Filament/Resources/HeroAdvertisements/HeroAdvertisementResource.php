<?php

namespace App\Filament\Resources\HeroAdvertisements;

use App\Filament\Resources\HeroAdvertisements\Pages\CreateHeroAdvertisement;
use App\Filament\Resources\HeroAdvertisements\Pages\EditHeroAdvertisement;
use App\Filament\Resources\HeroAdvertisements\Pages\ListHeroAdvertisements;
use App\Models\HeroAdvertisement;
use BackedEnum;
use UnitEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HeroAdvertisementResource extends Resource
{
    protected static ?string $model = HeroAdvertisement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?string $navigationLabel = 'Hero Advertisements';

    protected static UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Hero Advertisement';

    protected static ?string $pluralModelLabel = 'Hero Advertisements';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Hero Advertisement')
                ->description('Upload the artwork used in the homepage hero. Set a unique position for each image, or use drag-and-drop on the list to change the order.')
                ->columnSpanFull()
                ->columns(2)
                ->components([
                    FileUpload::make('image_path')
                        ->label('Hero Image')
                        ->image()
                        ->disk('public')
                        ->directory('hero')
                        ->imageEditor()
                        ->maxSize(12288)
                        ->saveUploadedFileUsing(
                            fn ($file) => app(\App\Services\ImageProcessingService::class)
                                ->processAndStore(
                                    $file,
                                    'hero',
                                    maxWidth: 2400,
                                    quality: 88
                                )
                        )
                        ->helperText('Use a wide, high-resolution image composed specifically for the homepage hero.')
                        ->required()
                        ->columnSpanFull(),

                    TextInput::make('image_alt')
                        ->label('Image Alt Text')
                        ->maxLength(255)
                        ->helperText('Describe the hero artwork for accessibility.')
                        ->columnSpanFull(),

                    TextInput::make('sort_order')
                        ->label('Position')
                        ->numeric()
                        ->integer()
                        ->minValue(1)
                        ->default(fn () => (int) HeroAdvertisement::max('sort_order') + 1)
                        ->unique(ignoreRecord: true)
                        ->helperText('Each hero image must have a unique position. Lower numbers appear first.')
                        ->required(),

                    Toggle::make('is_active')
                        ->label('Active in Hero')
                        ->default(true)
                        ->helperText('Only active hero advertisements are displayed on the homepage.')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Hero Image')
                    ->disk('public')
                    ->height(72),

                TextColumn::make('image_alt')
                    ->label('Alt Text')
                    ->limit(55)
                    ->toggleable(),

                TextColumn::make('sort_order')
                    ->label('Position')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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
            'index' => ListHeroAdvertisements::route('/'),
            'create' => CreateHeroAdvertisement::route('/create'),
            'edit' => EditHeroAdvertisement::route('/{record}/edit'),
        ];
    }
}
