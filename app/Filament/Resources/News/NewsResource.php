<?php

namespace App\Filament\Resources\News;

use App\Filament\Resources\News\Pages\CreateNews;
use App\Filament\Resources\News\Pages\EditNews;
use App\Filament\Resources\News\Pages\ListNews;
use App\Models\News;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $navigationLabel = 'News & Articles';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Content')
                ->columns(2)
                ->components([
                    TextInput::make('title_en')
                        ->label('Title (English)')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $state, callable $set, string $operation) {
                            if ($operation === 'create') {
                                $set('slug', Str::slug($state));
                            }
                        }),

                    TextInput::make('title_ar')
                        ->label('Title (Arabic)')
                        ->required()
                        ->maxLength(255)
                        ->extraInputAttributes(['dir' => 'rtl']),

                    RichEditor::make('body_en')
                        ->label('Body (English)')
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

                    RichEditor::make('body_ar')
                        ->label('Body (Arabic)')
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

                    // Placeholder for now — wired to Gemini in a later step.
                    \Filament\Schemas\Components\Actions::make([
                        \Filament\Actions\Action::make('ai_translate_body')
                            ->label('AI-generate Arabic body from English')
                            ->icon(Heroicon::OutlinedSparkles)
                            ->action(function (callable $get, callable $set) {
                                // TODO: call Gemini translation service here
                                // $set('body_ar', $translated);
                            }),
                    ])->columnSpanFull(),
                ]),

            Section::make('Classification & Media')
                ->columns(2)
                ->components([
                    Select::make('news_type')
                        ->label('News Type')
                        ->options([
                            'media_news' => 'Media News',
                            'mli_news' => 'MLI News',
                        ])
                        ->default('mli_news')
                        ->required(),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('Auto-filled from English title.'),

                    FileUpload::make('featured_image_path')
                        ->label('Featured Image')
                        ->image()
                        ->disk('public')
                        ->directory('news')
                        ->imageEditor()
                        ->maxSize(8192)
                        ->saveUploadedFileUsing(fn($file) => app(\App\Services\ImageProcessingService::class)
                            ->processAndStore($file, 'news', maxWidth: 1600, quality: 85))
                        ->columnSpanFull(),

                    TextInput::make('featured_image_alt')
                        ->label('Featured Image Alt Text')
                        ->maxLength(255)
                        ->helperText('Describe the image for accessibility & SEO.')
                        ->columnSpanFull(),
                ]),

            Section::make('Publishing')
                ->columns(2)
                ->components([
                    Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->default('draft')
                        ->required()
                        ->live(),

                    DateTimePicker::make('published_at')
                        ->label('Publish At')
                        ->native(false)
                        ->visible(fn(callable $get) => $get('status') === 'scheduled')
                        ->required(fn(callable $get) => $get('status') === 'scheduled'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image_path')
                    ->label('Image')
                    ->disk('public')
                    ->square(),

                TextColumn::make('title_en')
                    ->label('Title (English)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('news_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'media_news' => 'info',
                        'mli_news' => 'primary',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'media_news' => 'Media News',
                        'mli_news' => 'MLI News',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'published' => 'success',
                    }),

                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'published' => 'Published',
                    ]),
                SelectFilter::make('news_type')
                    ->label('Type')
                    ->options([
                        'media_news' => 'Media News',
                        'mli_news' => 'MLI News',
                    ]),
            ])
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
            'index' => ListNews::route('/'),
            'create' => CreateNews::route('/create'),
            'edit' => EditNews::route('/{record}/edit'),
        ];
    }
}
