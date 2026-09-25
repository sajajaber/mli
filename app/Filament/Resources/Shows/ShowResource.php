<?php

namespace App\Filament\Resources\Shows;

use App\Filament\Concerns\HasAutoSlug;
use App\Filament\Concerns\HasPublishWorkflow;
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
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
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
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (\Illuminate\Validation\Rules\Unique $rule) => $rule->withoutTrashed(),
                        )
                        ->validationMessages([
                            'unique' => 'A show with this English title already exists.',
                        ])
                        ->live(onBlur: true)
                        ->afterStateUpdated(static::fillSlugFromTitle()),

                    TextInput::make('title_ar')
                        ->label('Title (Arabic)')
                        ->required()
                        ->maxLength(255)
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (\Illuminate\Validation\Rules\Unique $rule) => $rule->withoutTrashed(),
                        )
                        ->validationMessages([
                            'unique' => 'A show with this Arabic title already exists.',
                        ])
                        ->extraInputAttributes([
                            'dir' => 'rtl',
                        ])
                        ->live(onBlur: true),

                    Textarea::make('description_en')
                        ->label('Description (English)')
                        ->rows(4)
                        ->columnSpan(1)
                        ->live(onBlur: true),

                    Textarea::make('description_ar')
                        ->label('Description (Arabic)')
                        ->rows(4)
                        ->extraInputAttributes([
                            'dir' => 'rtl',
                        ])
                        ->columnSpan(1)
                        ->live(onBlur: true),
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
                    Toggle::make('is_new_release')
                        ->label('New Release')
                        ->helperText('Show this title in the homepage New Releases section.')
                        ->default(false),

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

                IconColumn::make('is_new_release')
                    ->label('New Release')
                    ->boolean(),

                static::statusColumn(),

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

                SelectFilter::make('category_id')
                    ->relationship(
                        'category',
                        'name_en'
                    )
                    ->label('Category'),
            ])
            ->recordActions([
                static::publishAction(),
                static::scheduleAction(),
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
