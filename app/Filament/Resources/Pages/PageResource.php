<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Models\Page;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Pages';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Page Info')
                ->columns(2)
                ->components([
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('e.g. about-us, contact-us — this sets the page URL.'),

                    TextInput::make('title_en')
                        ->label('Title (English)')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('title_ar')
                        ->label('Title (Arabic)')
                        ->required()
                        ->maxLength(255)
                        ->extraInputAttributes(['dir' => 'rtl']),
                ]),

            Section::make('Content')
                ->components([
                    RichEditor::make('content_en')
                        ->label('Content (English)')
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

                    RichEditor::make('content_ar')
                        ->label('Content (Arabic)')
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

                    \Filament\Schemas\Components\Actions::make([
                        \Filament\Actions\Action::make('ai_translate_content')
                            ->label('AI-generate Arabic content from English')
                            ->icon(Heroicon::OutlinedSparkles)
                            ->action(function (callable $get, callable $set) {
                                // TODO: call Gemini translation service here
                            }),
                    ])->columnSpanFull(),
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
                TextColumn::make('title_en')->label('Title')->searchable()->sortable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'published' => 'success',
                    }),
                TextColumn::make('published_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'scheduled' => 'Scheduled',
                    'published' => 'Published',
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
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
