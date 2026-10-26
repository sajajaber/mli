<?php

namespace App\Filament\Resources\Shows;

use App\Filament\Concerns\HasAutoSlug;
use App\Filament\Concerns\HasPublishWorkflow;
use App\Filament\Concerns\ValidatesOnBlur;
use App\Filament\Resources\Shows\Pages\CreateShow;
use App\Filament\Resources\Shows\Pages\EditShow;
use App\Filament\Resources\Shows\Pages\ListShows;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Show;
use BackedEnum;
use Closure;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use UnitEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ShowResource extends Resource
{
    use HasAutoSlug;
    use HasPublishWorkflow;
    use ValidatesOnBlur;

    protected static ?string $model = Show::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFilm;

    protected static ?string $navigationLabel = 'Shows';

    protected static UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(12)
                ->columnSpanFull()
                ->schema([
                    Grid::make(1)
                        ->columnSpan(['default' => 1, 'xl' => 9])
                        ->schema([
                            Section::make('Show Information')
                                ->description('Set the bilingual title and public descriptions for this show.')
                                ->columns(2)
                                ->components([
                                    Select::make('content_type')
                                        ->label('Content Type')
                                        ->options([
                                            'documentary' => 'Documentary',
                                            'series' => 'Series',
                                            'general' => 'General',
                                        ])
                                        ->default('documentary')
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function (Set $set, ?string $state): void {
                                            if ($state !== 'series') {
                                                $set('genres', []);
                                            }
                                        })
                                        ->columnSpanFull(),

                                    TextInput::make('title_en')
                                        ->label('Title (English)')
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(
                                            ignoreRecord: true,
                                            modifyRuleUsing: fn(\Illuminate\Validation\Rules\Unique $rule) => $rule->withoutTrashed(),
                                        )
                                        ->validationMessages([
                                            'unique' => 'A show with this English title already exists.',
                                        ])
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(static::fillSlugFromTitle())
                                        ->afterStateUpdated(static::validateOnBlur()),

                                    TextInput::make('title_ar')
                                        ->label('Title (Arabic)')
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(
                                            ignoreRecord: true,
                                            modifyRuleUsing: fn(\Illuminate\Validation\Rules\Unique $rule) => $rule->withoutTrashed(),
                                        )
                                        ->validationMessages([
                                            'unique' => 'A show with this Arabic title already exists.',
                                        ])
                                        ->extraInputAttributes([
                                            'dir' => 'rtl',
                                        ])
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(static::validateOnBlur()),

                                    Textarea::make('description_en')
                                        ->label('Description (English)')
                                        ->rows(7)
                                        ->columnSpanFull()
                                        ->live(onBlur: true),

                                    Textarea::make('description_ar')
                                        ->label('Description (Arabic)')
                                        ->rows(7)
                                        ->extraInputAttributes([
                                            'dir' => 'rtl',
                                        ])
                                        ->columnSpanFull()
                                        ->live(onBlur: true),
                                ]),

                            Section::make('Classification & Media')
                                ->description('Set the categories, genres, URL, cover image, and accessibility information.')
                                ->columns(2)
                                ->components([
                                    Select::make('categories')
                                        ->label('Categories')
                                        ->relationship(
                                            name: 'categories',
                                            titleAttribute: 'name_en',
                                            modifyQueryUsing: fn (Builder $query) => $query
                                                ->with('parent:id,name_en,parent_id')
                                                ->orderBy('parent_id')
                                                ->orderBy('name_en'),
                                        )
                                        ->getOptionLabelFromRecordUsing(fn (Category $record): string => $record->parent
                                            ? "{$record->parent->name_en} — {$record->name_en}"
                                            : $record->name_en)
                                        ->multiple()
                                        ->searchable(['name_en', 'name_ar'])
                                        ->preload()
                                        ->helperText('Choose from the listed categories or type to narrow the list. A show can have any number of categories, or none.'),

                                    Select::make('genres')
                                        ->label('Genres')
                                        ->relationship(
                                            name: 'genres',
                                            titleAttribute: 'name_en',
                                            modifyQueryUsing: fn (Builder $query) => $query
                                                ->whereHas('category', fn (Builder $categoryQuery) => $categoryQuery
                                                    ->whereNull('parent_id')
                                                    ->where('slug', 'series'))
                                                ->orderBy('name_en'),
                                        )
                                        ->getOptionLabelFromRecordUsing(fn (Genre $record): string => $record->name_en)
                                        ->multiple()
                                        ->searchable(['name_en', 'name_ar'])
                                        ->preload()
                                        ->createOptionForm([
                                            TextInput::make('name_en')
                                                ->label('Genre Name (English)')
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('name_ar')
                                                ->label('Genre Name (Arabic)')
                                                ->required()
                                                ->maxLength(255)
                                                ->extraInputAttributes(['dir' => 'rtl']),
                                        ])
                                        ->createOptionUsing(function (array $data): int {
                                            $seriesCategoryId = Category::query()
                                                ->whereNull('parent_id')
                                                ->where('slug', 'series')
                                                ->value('id');

                                            if (! $seriesCategoryId) {
                                                throw ValidationException::withMessages([
                                                    'name_en' => 'Create the top-level Series category before adding genres.',
                                                ]);
                                            }

                                            $baseSlug = Str::slug(trim($data['name_en'])) ?: 'genre';
                                            $slug = $baseSlug;
                                            $suffix = 2;

                                            while (Genre::query()->where('slug', $slug)->exists()) {
                                                $slug = "{$baseSlug}-{$suffix}";
                                                $suffix++;
                                            }

                                            return Genre::query()->create([
                                                'category_id' => $seriesCategoryId,
                                                'name_en' => trim($data['name_en']),
                                                'name_ar' => trim($data['name_ar']),
                                                'slug' => $slug,
                                            ])->getKey();
                                        })
                                        ->visible(fn (Get $get): bool => $get('content_type') === 'series')
                                        ->rules([
                                            fn (): Closure => function (string $attribute, $value, Closure $fail): void {
                                                $genreIds = array_values(array_unique(array_filter((array) $value)));

                                                if ($genreIds === []) {
                                                    return;
                                                }

                                                $seriesCategoryId = Category::query()
                                                    ->whereNull('parent_id')
                                                    ->where('slug', 'series')
                                                    ->value('id');

                                                if (! $seriesCategoryId || Genre::query()
                                                    ->whereIn('id', $genreIds)
                                                    ->where('category_id', $seriesCategoryId)
                                                    ->count() !== count($genreIds)) {
                                                    $fail('Only genres belonging to the top-level Series category can be selected.');
                                                }
                                            },
                                        ])
                                        ->helperText('Series only — choose genres such as Action, Drama, or Comedy. Type to search.'),

                                    TextInput::make('slug')
                                        ->label('Slug')
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(ignoreRecord: true)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(static::validateOnBlur())
                                        ->helperText('Auto-filled from the English title.'),

                                    TextInput::make('vimeo_url')
                                        ->label('Vimeo Trailer URL')
                                        ->url()
                                        ->rule('regex:/^https:\/\/(?:www\.)?vimeo\.com\/(?:.*\/)?[0-9]+(?:\/[A-Za-z0-9_-]+)?(?:\?.*)?$/i')
                                        ->helperText('Use a Vimeo video URL such as https://vimeo.com/123456789.')
                                        ->maxLength(255)
                                        ->placeholder('https://vimeo.com/123456789')
                                        ->columnSpanFull(),

                                    FileUpload::make('cover_image_path')
                                        ->label('Cover Image')
                                        ->image()
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                        ->disk('public')
                                        ->directory('shows')
                                        ->imageEditor()
                                        ->maxSize(8192)
                                        ->saveUploadedFileUsing(
                                            fn($file) => app(
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
                                        ->helperText('Describe the image for accessibility & SEO.')
                                        ->columnSpanFull(),
                                ]),

                            Section::make('Catalogue Attributes')
                                ->description('Shared production and availability information.')
                                ->columns(2)
                                ->components([
                                    TextInput::make('production_year')
                                        ->label('Year of Production')
                                        ->numeric()
                                        ->minValue(1800)
                                        ->maxValue((int) date('Y') + 2)
                                        ->step(1),

                                    TextInput::make('season_number')
                                        ->label('Season')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(100)
                                        ->step(1)
                                        ->helperText('Optional. Use one show record per season.'),

                                    TextInput::make('episode_count')
                                        ->label('Number of Episodes')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(9999)
                                        ->step(1),

                                    TagsInput::make('languages')
                                        ->label('Languages')
                                        ->placeholder('Add a language and press Enter')
                                        ->helperText('Add every available language.'),

                                    TagsInput::make('subtitles')
                                        ->label('Subtitles')
                                        ->placeholder('Add a subtitle language and press Enter')
                                        ->helperText('Leave empty if subtitles are unavailable.'),

                                    TagsInput::make('availability')
                                        ->label('Availability')
                                        ->placeholder('e.g. Worldwide, MENA')
                                        ->helperText('Add one or more territories, such as Worldwide or MENA.'),

                                    TextInput::make('watch_url')
                                        ->label('Watch Here URL')
                                        ->url()
                                        ->maxLength(255)
                                        ->placeholder('https://...')
                                        ->helperText('Direct viewing link. The Vimeo URL remains available for a trailer.')
                                        ->columnSpanFull(),
                                ]),

                            Section::make('Documentary Details')
                                ->description('Rights origin, country of origin, and broadcast format.')
                                ->columns(2)
                                ->visible(fn (Get $get): bool => $get('content_type') === 'documentary')
                                ->components([
                                    TextInput::make('origin')
                                        ->label('Rights / Content Origin')
                                        ->maxLength(255)
                                        ->helperText('Describe the source or rights origin, not the country where it was produced.'),

                                    TextInput::make('country_of_origin')
                                        ->label('Country of Origin')
                                        ->maxLength(255),

                                    Select::make('broadcast_format')
                                        ->label('Broadcast Format')
                                        ->options([
                                            '4K' => '4K',
                                            'HD' => 'HD',
                                        ])
                                        ->placeholder('Choose format'),
                                ]),

                            Section::make('Series Details')
                                ->description('Record MLI’s involvement separately from the creative genres.')
                                ->columns(2)
                                ->visible(fn (Get $get): bool => $get('content_type') === 'series')
                                ->components([
                                    Select::make('service_types')
                                        ->label('MLI Involvement')
                                        ->options([
                                            'production' => 'Production',
                                            'distribution' => 'Distribution',
                                            'collaboration' => 'Collaboration',
                                        ])
                                        ->multiple()
                                        ->searchable()
                                        ->helperText('Choose Production, Distribution, Collaboration, or a combination.'),

                                    Repeater::make('castMembers')
                                        ->label('Cast Members')
                                        ->relationship()
                                        ->schema([
                                            FileUpload::make('photo_path')
                                                ->label('Actor Photo')
                                                ->image()
                                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                                ->disk('public')
                                                ->directory('cast')
                                                ->imageEditor()
                                                ->maxSize(8192)
                                                ->saveUploadedFileUsing(fn ($file) => app(\\App\\Services\\ImageProcessingService::class)
                                                    ->processAndStore($file, 'cast', maxWidth: 900, quality: 85))
                                                ->helperText('Upload the actor’s real portrait. It will be displayed in a consistent portrait crop.')
                                                ->columnSpanFull(),

                                            TextInput::make('actor_name_en')
                                                ->label('Actor Name (English)')
                                                ->required()
                                                ->maxLength(255),

                                            TextInput::make('actor_name_ar')
                                                ->label('Actor Name (Arabic)')
                                                ->maxLength(255)
                                                ->extraInputAttributes(['dir' => 'rtl']),

                                            TextInput::make('character_name_en')
                                                ->label('Character Name (English)')
                                                ->maxLength(255),

                                            TextInput::make('character_name_ar')
                                                ->label('Character Name (Arabic)')
                                                ->maxLength(255)
                                                ->extraInputAttributes(['dir' => 'rtl']),
                                        ])
                                        ->columns(2)
                                        ->orderColumn('sort_order')
                                        ->itemLabel(fn (array $state): ?string => $state['actor_name_en'] ?? 'New Cast Member')
                                        ->addActionLabel('Add Cast Member')
                                        ->collapsible()
                                        ->columnSpanFull()
                                        ->helperText('Add one entry per actor, upload their real photo, and drag entries to set the public display order.'),

                                    TagsInput::make('directors')
                                        ->label('Director(s)')
                                        ->placeholder('Add a director and press Enter'),
                                ]),
                        ]),

                    Section::make('Publishing')
                        ->columnSpan(['default' => 1, 'xl' => 3])
                        ->description('Control visibility and release timing for this title.')
                        ->columns(1)
                        ->components([
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'draft' => 'Draft',
                                    'scheduled' => 'Scheduled',
                                    'published' => 'Published',
                                ])
                                ->default('draft')
                                ->required()
                                ->live(),

                            Toggle::make('is_new_release')
                                ->label('New Release')
                                ->helperText('Show this title in the homepage New Releases section.')
                                ->default(false),

                            DateTimePicker::make('published_at')
                                ->label('Publish At')
                                ->native(false)
                                ->timezone('Asia/Beirut')
                                ->minDate(today('Asia/Beirut'))
                                ->visible(
                                    fn(callable $get) =>
                                    $get('status') === 'scheduled'
                                )
                                ->required(
                                    fn(callable $get) =>
                                    $get('status') === 'scheduled'
                                ),

                        ]),
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

                TextColumn::make('content_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'documentary' => 'Documentary',
                        'series' => 'Series',
                        'general' => 'General',
                        default => 'Not set',
                    }),

                TextColumn::make('categories.name_en')
                    ->label('Categories')
                    ->badge()
                    ->listWithLineBreaks()
                    ->toggleable(),

                TextColumn::make('genres.name_en')
                    ->label('Genres')
                    ->badge()
                    ->color('gray')
                    ->listWithLineBreaks()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_new_release')
                    ->label('New Release')
                    ->boolean(),

                static::statusColumn(),

                TextColumn::make('published_at')
                    ->dateTime()
                    ->timezone('Asia/Beirut')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('sort_order')
                    ->label('Homepage Order')
                    ->sortable()
                    ->formatStateUsing(
                        fn ($state, Show $record) =>
                            $record->is_new_release && $record->status === 'published'
                                ? $state
                                : null
                    )
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->reorderRecordsTriggerAction(
                fn (\Filament\Actions\Action $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? 'Done reordering' : 'Reorder shows')
                    ->icon($isReordering ? Heroicon::Check : Heroicon::ArrowsUpDown),
            )
            ->poll('10s')
            ->filters([
                SelectFilter::make('content_type')
                    ->label('Content Type')
                    ->options([
                        'documentary' => 'Documentary',
                        'series' => 'Series',
                        'general' => 'General',
                    ]),

                SelectFilter::make('is_new_release')
                    ->label('New Release')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'published' => 'Published',
                    ]),

                SelectFilter::make('categories')
                    ->relationship('categories', 'name_en')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label('Category'),

                SelectFilter::make('genres')
                    ->relationship(
                        'genres',
                        'name_en',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->whereHas('category', fn (Builder $categoryQuery) => $categoryQuery
                                ->whereNull('parent_id')
                                ->where('slug', 'series')),
                    )
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label('Genre'),
            ])
            ->recordActions([
                static::publishAction(),
                static::scheduleAction(),
                static::unpublishAction(),
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
