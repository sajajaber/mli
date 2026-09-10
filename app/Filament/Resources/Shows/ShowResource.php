<?php

namespace App\Filament\Resources\Shows;

use App\Filament\Resources\Shows\Pages\CreateShow;
use App\Filament\Resources\Shows\Pages\EditShow;
use App\Filament\Resources\Shows\Pages\ListShows;
use App\Models\Show;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class ShowResource extends Resource
{
    protected static ?string $model = Show::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFilm;

    protected static ?string $navigationLabel = 'Shows';

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
                        ->afterStateUpdated(function (
                            string $state,
                            callable $get,
                            callable $set
                        ) {
                            // Auto-fill slug only when empty.
                            if (blank($get('slug'))) {
                                $set('slug', Str::slug($state));
                            }

                            $flags = $get('ai_flags') ?? [];
                            $flags['title_en'] = false;
                            $set('ai_flags', $flags);
                        }),

                    TextInput::make('title_ar')
                        ->label('Title (Arabic)')
                        ->required()
                        ->maxLength(255)
                        ->extraInputAttributes([
                            'dir' => 'rtl',
                        ])
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (
                            callable $get,
                            callable $set
                        ) {
                            $flags = $get('ai_flags') ?? [];
                            $flags['title_ar'] = false;
                            $set('ai_flags', $flags);
                        }),

                    Textarea::make('description_en')
                        ->label('Description (English)')
                        ->rows(4)
                        ->columnSpan(1)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (
                            callable $get,
                            callable $set
                        ) {
                            $flags = $get('ai_flags') ?? [];
                            $flags['description_en'] = false;
                            $set('ai_flags', $flags);
                        }),

                    Textarea::make('description_ar')
                        ->label('Description (Arabic)')
                        ->rows(4)
                        ->extraInputAttributes([
                            'dir' => 'rtl',
                        ])
                        ->columnSpan(1)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (
                            callable $get,
                            callable $set
                        ) {
                            $flags = $get('ai_flags') ?? [];
                            $flags['description_ar'] = false;
                            $set('ai_flags', $flags);
                        }),

                    \Filament\Schemas\Components\Actions::make([
                        \Filament\Actions\Action::make('ai_translate')
                            ->label('AI Translate (EN ⇄ AR)')
                            ->icon(Heroicon::OutlinedSparkles)
                            ->action(function (
                                callable $get,
                                callable $set
                            ) {
                                $titleEn = $get('title_en');
                                $titleAr = $get('title_ar');

                                /*
                                 * Decide translation direction.
                                 *
                                 * If English exists, translate EN → AR.
                                 * Otherwise, translate AR → EN.
                                 */
                                $direction = filled($titleEn)
                                    ? 'en_to_ar'
                                    : 'ar_to_en';

                                if (
                                    blank($titleEn) &&
                                    blank($titleAr)
                                ) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Nothing to translate')
                                        ->body(
                                            'Fill in the English or Arabic title first.'
                                        )
                                        ->warning()
                                        ->send();

                                    return;
                                }

                                try {
                                    /*
                                     * Build the source fields.
                                     */
                                    if ($direction === 'en_to_ar') {
                                        $sourceFields = [
                                            'title' => $titleEn,
                                            'description' => $get(
                                                'description_en'
                                            ),
                                        ];
                                    } else {
                                        $sourceFields = [
                                            'title' => $titleAr,
                                            'description' => $get(
                                                'description_ar'
                                            ),
                                        ];
                                    }

                                    /*
                                     * Call the translation service.
                                     */
                                    $translated = app(
                                        \App\Services\GeminiTranslationService::class
                                    )->translateFields(
                                        $sourceFields,
                                        $direction
                                    );

                                    $flags = $get('ai_flags') ?? [];

                                    /*
                                     * English → Arabic
                                     */
                                    if ($direction === 'en_to_ar') {
                                        if (
                                            isset($translated['title']) &&
                                            filled($translated['title'])
                                        ) {
                                            $set(
                                                'title_ar',
                                                $translated['title']
                                            );

                                            $flags['title_ar'] = true;
                                        }

                                        if (
                                            isset($translated['description']) &&
                                            filled($translated['description'])
                                        ) {
                                            $set(
                                                'description_ar',
                                                $translated['description']
                                            );

                                            $flags['description_ar'] = true;
                                        }
                                    }

                                    /*
                                     * Arabic → English
                                     */
                                    else {
                                        if (
                                            isset($translated['title']) &&
                                            filled($translated['title'])
                                        ) {
                                            $translatedTitle =
                                                $translated['title'];

                                            $set(
                                                'title_en',
                                                $translatedTitle
                                            );

                                            /*
                                             * FIX:
                                             *
                                             * $state does NOT exist inside
                                             * this Action callback.
                                             *
                                             * Use the translated English title
                                             * instead.
                                             */
                                            if (blank($get('slug'))) {
                                                $set(
                                                    'slug',
                                                    Str::slug(
                                                        $translatedTitle
                                                    )
                                                );
                                            }

                                            $flags['title_en'] = true;
                                        }

                                        if (
                                            isset($translated['description']) &&
                                            filled($translated['description'])
                                        ) {
                                            $set(
                                                'description_en',
                                                $translated['description']
                                            );

                                            $flags['description_en'] = true;
                                        }
                                    }

                                    /*
                                     * Update AI flags once.
                                     */
                                    $set('ai_flags', $flags);

                                    \Filament\Notifications\Notification::make()
                                        ->title('Translation generated')
                                        ->success()
                                        ->send();

                                } catch (\Throwable $e) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Translation failed')
                                        ->body($e->getMessage())
                                        ->danger()
                                        ->send();
                                }
                            }),
                    ])->columnSpanFull(),
                ]),

            Section::make('Classification & Media')
                ->columns(2)
                ->components([
                    Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'name_en')
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText(
                            'Auto-filled from English title.'
                        ),

                    TextInput::make('vimeo_url')
                        ->label('Vimeo Trailer URL')
                        ->url()
                        ->maxLength(255)
                        ->placeholder(
                            'https://vimeo.com/123456789'
                        )
                        ->columnSpanFull(),

                    FileUpload::make('cover_image_path')
                        ->label('Cover Image')
                        ->image()
                        ->disk('public')
                        ->directory('shows')
                        ->imageEditor()
                        ->maxSize(8192)
                        ->saveUploadedFileUsing(
                            fn ($file) => app(
                                \App\Services\ImageProcessingService::class
                            )->processAndStore(
                                $file,
                                'shows',
                                maxWidth: 1600,
                                quality: 85
                            )
                        )
                        ->columnSpanFull(),

                    TextInput::make('cover_image_alt')
                        ->label('Cover Image Alt Text')
                        ->maxLength(255)
                        ->helperText(
                            'Describe the image for accessibility & SEO.'
                        )
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
                        ->visible(
                            fn (callable $get) =>
                                $get('status') === 'scheduled'
                        )
                        ->required(
                            fn (callable $get) =>
                                $get('status') === 'scheduled'
                        ),

                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image_path')
                    ->label('Cover')
                    ->disk('public')
                    ->square(),

                TextColumn::make('title_en')
                    ->label('Title (English)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name_en')
                    ->label('Category')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(
                        fn (string $state) => match ($state) {
                            'draft' => 'gray',
                            'scheduled' => 'warning',
                            'published' => 'success',
                        }
                    ),

                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('sort_order')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'published' => 'Published',
                    ]),

                SelectFilter::make('category_id')
                    ->relationship(
                        'category',
                        'name_en'
                    )
                    ->label('Category'),
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
            'index' => ListShows::route('/'),
            'create' => CreateShow::route('/create'),
            'edit' => EditShow::route('/{record}/edit'),
        ];
    }
}
