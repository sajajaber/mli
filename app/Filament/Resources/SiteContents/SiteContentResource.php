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
use Filament\Actions\Action;
use Filament\Notifications\Notification;
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
        return parent::getEloquentQuery()
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('site_contents')
                    ->groupBy('key');
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('title_en')->label('Content')->searchable()->sortable(),
                TextColumn::make('key')->label('Section')->searchable(),
                TextColumn::make('version')->label('Version'),
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
                Action::make('publish')
                    ->label('Publish')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (SiteContent $record) => $record->status !== 'published')
                    ->action(function (SiteContent $record): void {
                        SiteContent::query()
                            ->where('key', $record->key)
                            ->where('status', 'published')
                            ->where('id', '!=', $record->getKey())
                            ->update(['status' => 'draft']);

                        $record->update([
                            'status' => 'published',
                            'published_at' => now(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Published')
                            ->body('This site content is now live on the website.')
                            ->send();
                    }),

                Action::make('schedule')
                    ->label('Schedule')
                    ->color('warning')
                    ->visible(fn (SiteContent $record) => $record->status !== 'published')
                    ->form([
                        DateTimePicker::make('published_at')
                            ->label('Publish date & time')
                            ->native(false)
                            ->seconds(false)
                            ->required()
                            ->minDate(now()),
                    ])
                    ->action(function (SiteContent $record, array $data): void {
                        $record->update([
                            'status' => 'scheduled',
                            'published_at' => $data['published_at'],
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Publication scheduled')
                            ->body('This content will replace the current live version automatically at the scheduled time.')
                            ->send();
                    }),

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
