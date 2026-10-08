<?php

namespace App\Filament\Resources\MediaServices;

use App\Filament\Resources\MediaServices\Pages\CreateMediaService;
use App\Filament\Resources\MediaServices\Pages\EditMediaService;
use App\Filament\Resources\MediaServices\Pages\ListMediaServices;
use App\Models\MediaService;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaServiceResource extends Resource
{
    protected static ?string $model = MediaService::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Media Services';

    protected static UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(12)
                ->columnSpanFull()
                ->schema([
                    Section::make('Service Information')
                        ->description('Manage the bilingual service title and description shown on the public website.')
                        ->columnSpan(['default' => 1, 'xl' => 9])
                        ->columns(2)
                        ->components([
                            TextInput::make('title_en')
                                ->label('Title (English)')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('title_ar')
                                ->label('Title (Arabic)')
                                ->required()
                                ->maxLength(255)
                                ->extraInputAttributes(['dir' => 'rtl']),

                            RichEditor::make('content_en')
                                ->label('Description (English)')
                                ->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'bulletList',
                                    'orderedList',
                                    'link',
                                    'blockquote',
                                    'undo',
                                    'redo',
                                ])
                                ->columnSpanFull(),

                            RichEditor::make('content_ar')
                                ->label('Description (Arabic)')
                                ->extraInputAttributes(['dir' => 'rtl'])
                                ->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'bulletList',
                                    'orderedList',
                                    'link',
                                    'blockquote',
                                    'undo',
                                    'redo',
                                ])
                                ->columnSpanFull(),
                        ]),

                    Section::make('Visibility')
                        ->columnSpan(['default' => 1, 'xl' => 3])
                        ->description('Control whether this service appears on the public website.')
                        ->columns(1)
                        ->components([
                            Toggle::make('is_active')
                                ->label('Active')
                                ->default(true)
                                ->helperText('Inactive services stay in the admin list but are hidden from the public website.'),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_en')
                    ->label('Service (English)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title_ar')
                    ->label('Service (Arabic)')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->reorderRecordsTriggerAction(
                fn (Action $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? 'Done reordering' : 'Reorder media services')
                    ->icon($isReordering ? Heroicon::Check : Heroicon::ArrowsUpDown),
            )
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
            'index' => ListMediaServices::route('/'),
            'create' => CreateMediaService::route('/create'),
            'edit' => EditMediaService::route('/{record}/edit'),
        ];
    }
}
