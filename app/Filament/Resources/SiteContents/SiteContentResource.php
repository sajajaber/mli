<?php

namespace App\Filament\Resources\SiteContents;

use App\Filament\Concerns\HasPublishWorkflow;
use App\Filament\Resources\SiteContents\Pages\EditSiteContent;
use App\Filament\Resources\SiteContents\Pages\ListSiteContents;
use App\Models\SiteContent;
use BackedEnum;
use UnitEnum;
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
use Illuminate\Database\Eloquent\Model;

class SiteContentResource extends Resource
{
    use HasPublishWorkflow;

    protected static ?string $model = SiteContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Website Content';

    protected static UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Website Content';

    protected static ?string $pluralModelLabel = 'Website Content';

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
                ->description('Published content stays live while you prepare a new draft. Publishing a draft replaces the current live version.')
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // Site content is versioned by key. The admin list shows only the latest
        // version for each section so editors do not see every historical row.
        return parent::getEloquentQuery()
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('site_contents')
                    ->groupBy('key');
            });
    }

    protected static function beforePublish(Model $record): void
    {
        SiteContent::query()
            ->where('key', $record->key)
            ->where('status', 'published')
            ->where('id', '!=', $record->getKey())
            ->update(['status' => 'draft']);
    }

    protected static function publishNotificationBody(Model $record): string
    {
        return 'This site content is now live on the website.';
    }

    protected static function scheduleNotificationBody(Model $record): string
    {
        return 'This content will replace the current live version automatically at the scheduled time.';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('title_en')->label('Content')->searchable()->sortable(),
                TextColumn::make('key')->label('Section')->searchable(),
                TextColumn::make('version')->label('Version'),
                static::statusColumn(),
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
                static::publishAction(),
                static::scheduleAction(),
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
