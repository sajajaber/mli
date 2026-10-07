<?php

namespace App\Filament\Resources\HeroAdvertisements;

use App\Filament\Resources\HeroAdvertisements\Pages\CreateHeroAdvertisement;
use App\Filament\Resources\HeroAdvertisements\Pages\EditHeroAdvertisement;
use App\Filament\Resources\HeroAdvertisements\Pages\ListHeroAdvertisements;
use App\Models\HeroAdvertisement;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use UnitEnum;

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
                ->description('Upload the artwork used in the homepage hero. The order is managed visually from the Hero Advertisements page.')
                ->columnSpanFull()
                ->columns(2)
                ->components([
                    FileUpload::make('image_path')
                        ->label('Hero Image')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
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
                Stack::make([
                    ImageColumn::make('image_path')
                        ->label('Hero Artwork')
                        ->disk('public')
                        ->height(220)
                        ->extraImgAttributes([
                            'class' => 'w-full rounded-xl object-cover',
                        ]),

                    TextColumn::make('image_alt')
                        ->label('Alt Text')
                        ->placeholder('No alt text')
                        ->limit(90)
                        ->wrap()
                        ->color('gray'),

                    TextColumn::make('is_active')
                        ->label('Status')
                        ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
                        ->badge()
                        ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                ]),
            ])
            ->contentGrid([
                'default' => 1,
                'md' => 2,
                'xl' => 3,
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->reorderRecordsTriggerAction(
                fn (Action $action) => $action
                    ->label('Reorder Hero Advertisements')
                    ->icon('heroicon-o-arrows-up-down')
            )
            ->recordActions([
                Action::make('toggleActive')
                    ->label(fn (HeroAdvertisement $record): string => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn (HeroAdvertisement $record): string => $record->is_active
                        ? 'heroicon-o-eye-slash'
                        : 'heroicon-o-eye')
                    ->color(fn (HeroAdvertisement $record): string => $record->is_active ? 'warning' : 'success')
                    ->action(function (HeroAdvertisement $record): void {
                        $record->update([
                            'is_active' => ! $record->is_active,
                        ]);
                    })
                    ->successNotificationTitle('Hero visibility updated'),

                EditAction::make(),
                DeleteAction::make(),
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
