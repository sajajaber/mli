<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Models\Client;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Clients';

    protected static UnitEnum|string|null $navigationGroup = 'People & Partners';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Client Information')
                ->description('Manage the client name and logo used on the public website.')
                ->columnSpan(['default' => 1, 'xl' => 11])
                ->columns(2)
                ->components([
                    FileUpload::make('logo_path')
                        ->label('Logo')
                        ->image()
                        ->disk('public')
                        ->directory('clients')
                        ->maxSize(8192)
                        ->saveUploadedFileUsing(fn($file) => app(\App\Services\ImageProcessingService::class)
                            ->processAndStore($file, 'clients', maxWidth: 500, quality: 90, preserveTransparency: true))
                        ->columnSpanFull(),

                    TextInput::make('name')
                        ->label('Client Name')
                        ->required()
                        ->maxLength(255),

                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->disk('public'),

                TextColumn::make('name')->searchable()->sortable(),

                TextColumn::make('sort_order')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->reorderRecordsTriggerAction(
                fn (Action $action) => $action
                    ->label('Reorder Clients')
                    ->icon('heroicon-o-arrows-up-down')
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
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }
}
