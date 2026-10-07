<?php

namespace App\Filament\Resources\News;

use App\Filament\Concerns\HasAutoSlug;
use App\Filament\Concerns\HasPublishWorkflow;
use App\Filament\Concerns\ValidatesOnBlur;
use App\Filament\Resources\News\Pages\CreateNews;
use App\Filament\Resources\News\Pages\EditNews;
use App\Filament\Resources\News\Pages\ListNews;
use App\Models\News;
use BackedEnum;
use UnitEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NewsResource extends Resource
{
    use HasAutoSlug;
    use HasPublishWorkflow;

    protected static ?string $model = News::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $navigationLabel = 'News & Articles';

    protected static UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(12)
                ->columnSpanFull()
                ->schema([
                    Grid::make(1)
                        ->columnSpan(['default' => 1, 'xl' => 9])
                        ->schema([
                            Section::make('Content')
                                ->description('Set the bilingual title and article content.')
                                ->columns(2)
                                ->components([
                                    TextInput::make('title_en')
                                        ->label('Title (English)')
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(
                                            ignoreRecord: true,
                                            modifyRuleUsing: fn(\Illuminate\Validation\Rules\Unique $rule) => $rule->withoutTrashed(),
                                        )
                                        ->validationMessages([
                                            'unique' => 'A news article with this English title already exists.',
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
                                            'unique' => 'A news article with this Arabic title already exists.',
                                        ])
                                        ->extraInputAttributes(['dir' => 'rtl'])
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(static::validateOnBlur()),

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
                                        ->live(onBlur: true)
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
                                        ->live(onBlur: true)
                                        ->columnSpanFull(),
                                ]),

                            Section::make('Classification & Media')
                                ->description('Set the article type, URL, featured image, and accessibility information.')
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
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(static::validateOnBlur())
                                        ->validationMessages([
                                            'unique' => 'The slug has already been taken.',
                                        ])
                                        ->helperText('Auto-filled from English title.'),

                                    FileUpload::make('featured_image_path')
                                        ->label('Featured Image')
                                        ->image()
                                        ->disk('public')
                                        ->directory('news')
                                        ->imageEditor()
                                        ->maxSize(8192)
                                        ->saveUploadedFileUsing(fn ($file) => app(\App\Services\ImageProcessingService::class)
                                            ->processAndStore($file, 'news', maxWidth: 1600, quality: 85))
                                        ->columnSpanFull(),

                                    TextInput::make('featured_image_alt')
                                        ->label('Featured Image Alt Text')
                                        ->maxLength(255)
                                        ->helperText('Describe the image for accessibility & SEO.')
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    Section::make('Publishing')
                        ->columnSpan(['default' => 1, 'xl' => 3])
                        ->description('Control visibility and scheduling.')
                        ->columns(1)
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
                                ->timezone('Asia/Beirut')
                                ->minDate(today('Asia/Beirut'))
                                ->visible(fn (callable $get) => $get('status') === 'scheduled')
                                ->required(fn (callable $get) => $get('status') === 'scheduled'),
                        ]),
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
                    ->color(fn (string $state) => match ($state) {
                        'media_news' => 'info',
                        'mli_news' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'media_news' => 'Media News',
                        'mli_news' => 'MLI News',
                        default => $state,
                    }),

                static::statusColumn(),

                TextColumn::make('published_at')
                    ->dateTime()
                    ->timezone('Asia/Beirut')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('10s')
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
            'index' => ListNews::route('/'),
            'create' => CreateNews::route('/create'),
            'edit' => EditNews::route('/{record}/edit'),
        ];
    }
}
