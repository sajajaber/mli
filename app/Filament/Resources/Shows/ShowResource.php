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
use UnitEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
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
                                        ])
                                        ->default('documentary')
                                        ->required()
                                        ->live()
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
                                                ->with('parent')
                                                ->orderByRaw('COALESCE((SELECT parent_categories.name_en FROM categories AS parent_categories WHERE parent_categories.id = categories.parent_id), categories.name_en)')
                                                ->orderByRaw('CASE WHEN categories.parent_id IS NULL THEN 0 ELSE 1 END')
                                                ->orderBy('categories.name_en'),
                                        )
                                        ->getOptionLabelFromRecordUsing(fn (Category $record): string => $record->parent
                                            ? "{$record->parent->name_en} — {$record->name_en}"
                                            : $record->name_en)
                                        ->multiple()
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->afterStateUpdated(function (Get $get, Set $set, $state): void {
                                            // Drop any selected genre whose category was just unselected.
                                            $genreIds = $get('genres') ?? [];

                                            if (blank($genreIds)) {
                                                return;
                                            }

                                            $selectedCategoryIds = collect($state ?? [])->filter();
                                            $parentCategoryIds = Category::query()
                                                ->whereIn('id', $selectedCategoryIds)
                                                ->whereNotNull('parent_id')
                                                ->pluck('parent_id');
                                            $allowedGenreCategoryIds = $selectedCategoryIds
                                                ->merge($parentCategoryIds)
                                                ->unique()
                                                ->values();

                                            $set('genres', Genre::query()
                                                ->whereIn('id', $genreIds)
                                                ->whereIn('category_id', $allowedGenreCategoryIds)
                                                ->pluck('id')
                                                ->all());
                                        })
                                        ->helperText('A show can have any number of categories, or none.'),

                                    Select::make('genres')
                                        ->label('Genres')
                                        ->relationship(
                                            name: 'genres',
                                            titleAttribute: 'name_en',
                                            modifyQueryUsing: function (Builder $query, Get $get): Builder {
                                                $categoryIds = collect($get('categories') ?? [])->filter();
                                                $parentIds = Category::query()
                                                    ->whereIn('id', $categoryIds)
                                                    ->whereNotNull('parent_id')
                                                    ->pluck('parent_id');

                                                return $query
                                                    ->with('category')
                                                    ->whereIn('category_id', $categoryIds->merge($parentIds)->unique()->values())
                                                    ->orderBy('category_id')
                                                    ->orderBy('name_en');
                                            },
                                        )
                                        ->getOptionLabelFromRecordUsing(
                                            fn (Genre $record): string => $record->category
                                                ? "{$record->category->name_en} — {$record->name_en}"
                                                : $record->name_en
                                        )
                                        ->multiple()
                                        ->searchable()
                                        ->preload()
                                        ->rules([
                                            fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get): void {
                                                $genreIds = array_filter((array) $value);

                                                if ($genreIds === []) {
                                                    return;
                                                }

                                                $categoryIds = collect((array) ($get('categories') ?? []))->filter();
                                                $parentIds = Category::query()
                                                    ->whereIn('id', $categoryIds)
                                                    ->whereNotNull('parent_id')
                                                    ->pluck('parent_id');
                                                $allowedCategoryIds = $categoryIds->merge($parentIds)->unique()->values();

                                                if ($allowedCategoryIds->isEmpty()) {
                                                    $fail('Every selected genre must belong to one of the selected categories.');
                                                    return;
                                                }

                                                $invalid = Genre::query()
                                                    ->whereIn('id', $genreIds)
                                                    ->whereNotIn('category_id', $allowedCategoryIds)
                                                    ->exists();

                                                if ($invalid || Genre::query()->whereIn('id', $genreIds)->count() !== count(array_unique($genreIds))) {
                                                    $fail('Every selected genre must belong to one of the selected categories.');
                                                }
                                            },
                                        ])
                                        ->helperText('Choose categories first — only their genres are listed.'),

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

                                    TagsInput::make('cast')
                                        ->label('Cast')
                                        ->placeholder('Add a cast member and press Enter'),

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
                    ->preload()
                    ->label('Category'),

                SelectFilter::make('genres')
                    ->relationship('genres', 'name_en')
                    ->multiple()
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
