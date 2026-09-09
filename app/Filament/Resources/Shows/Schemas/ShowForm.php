<?php

namespace App\Filament\Resources\Shows\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ShowForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'id')
                    ->default(null),
                TextInput::make('title_en')
                    ->required(),
                TextInput::make('title_ar')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description_en')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('description_ar')
                    ->default(null)
                    ->columnSpanFull(),
                FileUpload::make('cover_image_path')
                    ->image(),
                FileUpload::make('cover_image_alt')
                    ->image(),
                TextInput::make('vimeo_url')
                    ->url()
                    ->default(null),
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
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
