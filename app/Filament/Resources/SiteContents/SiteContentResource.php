<?php

namespace App\Filament\Resources\SiteContents;

use App\Filament\Resources\SiteContents\Pages\EditSiteContent;
use App\Filament\Resources\SiteContents\Pages\ListSiteContents;
use App\Models\SiteContent;
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

class SiteContentResource extends Resource
{
    protected static ?string $model = SiteContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Site Content';

    protected static ?string $modelLabel = 'Site Content';

    protected static ?string $pluralModelLabel = 'Site Content';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Content')
                ->description('Edit predefined website content. The website structure and section order are managed by the developer.')
                ->components([
                    TextInput::make('title_en')
                        ->label('Title (English)')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('title_ar')
                        ->label('Title (Arabic)')
                        ->required()
                        ->maxLength(255)
                        ->extraInputAttributes(['dir' => 'rtl']),

                    RichEditor::make('content_en')
                        ->label('Content (English)')
                        ->toolbarButtons([
                            'bold', 'italic', 'bulletList', 'orderedList', 'link',
                            'blockquote', 'undo', 'redo',
                        ])
                        ->columnSpanFull(),

                    RichEditor::make('content_ar')
                        ->label('Content (Arabic)')
                        ->extraInputAttributes(['dir' => 'rtl'])
                        ->toolbarButtons([
                            'bold', 'italic', 'bulletList', 'orderedList', 'link',
                            'blockquote', 'undo', 'redo',
                        ])
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
                        ->required()
                        ->live(),

                    DateTimePicker::make('published_at')
                        ->label('Publish At')
                        ->native(false)
                        ->visible(fn (callable $get) => $get('status') === 'scheduled')
                        ->required(fn (callable $get) => $get('status') === 'scheduled'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('title_en')->label('Content')->searchable()->sortable(),
                TextColumn::make('key')->label('Section')->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteContents::route('/'),
            'edit' => EditSiteContent::route('/{record}/edit'),
        ];
    }
}
