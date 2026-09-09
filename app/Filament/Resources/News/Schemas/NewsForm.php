<?php

namespace App\Filament\Resources\News\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title_en')
                    ->required(),
                TextInput::make('title_ar')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('body_en')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('body_ar')
                    ->default(null)
                    ->columnSpanFull(),
                FileUpload::make('featured_image_path')
                    ->image(),
                FileUpload::make('featured_image_alt')
                    ->image(),
                Select::make('news_type')
                    ->options(['media_news' => 'Media news', 'mli_news' => 'Mli news'])
                    ->default('mli_news')
                    ->required(),
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
