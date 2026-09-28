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
use Illuminate\Support\Facades\DB;
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
            ->recordActions([
                Action::make('moveEarlier')
                    ->label('Swap with previous')
                    ->icon('heroicon-o-chevron-up')
                    ->color('gray')
                    ->disabled(fn (HeroAdvertisement $record): bool => ! static::hasAdjacent($record, -1))
                    ->action(fn (HeroAdvertisement $record) => static::swapWithAdjacent($record, -1))
                    ->successNotificationTitle('Hero order updated'),

                Action::make('moveLater')
                    ->label('Swap with next')
                    ->icon('heroicon-o-chevron-down')
                    ->color('gray')
                    ->disabled(fn (HeroAdvertisement $record): bool => ! static::hasAdjacent($record, 1))
                    ->action(fn (HeroAdvertisement $record) => static::swapWithAdjacent($record, 1))
                    ->successNotificationTitle('Hero order updated'),

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

    protected static function hasAdjacent(HeroAdvertisement $record, int $direction): bool
    {
        $ordered = HeroAdvertisement::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id');

        $index = $ordered->search($record->getKey());

        return $index !== false
            && isset($ordered[$index + $direction]);
    }

    protected static function swapWithAdjacent(HeroAdvertisement $record, int $direction): void
    {
        $ordered = HeroAdvertisement::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'sort_order']);

        $index = $ordered->search(fn (HeroAdvertisement $item): bool => $item->is($record));

        if ($index === false) {
            return;
        }

        $adjacent = $ordered->get($index + $direction);

        if (! $adjacent) {
            return;
        }

        $recordOrder = (int) $record->sort_order;
        $adjacentOrder = (int) $adjacent->sort_order;
        $temporaryOrder = (int) $ordered->max('sort_order') + 1;

        DB::transaction(function () use ($record, $adjacent, $recordOrder, $adjacentOrder, $temporaryOrder): void {
            $record->update(['sort_order' => $temporaryOrder]);
            $adjacent->update(['sort_order' => $recordOrder]);
            $record->update(['sort_order' => $adjacentOrder]);
        });
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
