<?php

namespace App\Filament\Resources\People;

use App\Filament\Resources\People\Pages\CreatePerson;
use App\Filament\Resources\People\Pages\EditPerson;
use App\Filament\Resources\People\Pages\ListPeople;
use App\Models\Person;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Team';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('photo_path')
                ->label('Photo')
                ->image()
                ->disk('public')
                ->directory('people')
                ->imageEditor()
                ->maxSize(8192)
                ->saveUploadedFileUsing(fn($file) => app(\App\Services\ImageProcessingService::class)
                    ->processAndStore($file, 'people', maxWidth: 800, quality: 85))
                ->columnSpanFull(),

            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('role_title')
                ->label('Role / Title')
                ->maxLength(255),

            TextInput::make('sort_order')
                ->numeric()
                ->default(0),

            Toggle::make('is_active')
                ->label('Active (visible on site)')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_path')
                    ->label('Photo')
                    ->disk('public')
                    ->circular(),

                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('role_title')->label('Role')->searchable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
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
            'index' => ListPeople::route('/'),
            'create' => CreatePerson::route('/create'),
            'edit' => EditPerson::route('/{record}/edit'),
        ];
    }
}
