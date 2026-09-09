<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required(),
                TextInput::make('title_en')
                    ->required(),
                TextInput::make('title_ar')
                    ->required(),
                Textarea::make('content_en')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('content_ar')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(['draft' => 'Draft', 'scheduled' => 'Scheduled', 'published' => 'Published'])
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('published_at'),
                TextInput::make('meta_title_en')
                    ->default(null),
                TextInput::make('meta_title_ar')
                    ->default(null),
                TextInput::make('meta_description_en')
                    ->default(null),
                TextInput::make('meta_description_ar')
                    ->default(null),
                Textarea::make('ai_flags')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
