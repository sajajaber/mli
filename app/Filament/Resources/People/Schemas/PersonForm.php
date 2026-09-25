<?php

namespace App\Filament\Resources\People\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PersonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('photo_path')
                    ->default(null),
                TextInput::make('name')
                    ->required(),
                TextInput::make('name_ar')
                    ->required()
                    ->extraInputAttributes(['dir' => 'rtl']),
                TextInput::make('role_title')
                    ->default(null),
                TextInput::make('role_title_ar')
                    ->required()
                    ->extraInputAttributes(['dir' => 'rtl']),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
