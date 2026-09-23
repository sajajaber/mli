<?php

namespace App\Filament\Resources\FooterSettings;

use App\Filament\Resources\FooterSettings\Pages\EditFooterSetting;
use App\Filament\Resources\FooterSettings\Pages\ListFooterSettings;
use App\Models\FooterSetting;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FooterSettingResource extends Resource
{
    protected static ?string $model = FooterSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Footer';

    protected static ?string $modelLabel = 'Footer Settings';

    protected static ?string $pluralModelLabel = 'Footer Settings';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Brand')
                ->description('Control the text shown in the footer.')
                ->components([
                    TextInput::make('tagline')
                        ->label('Tagline')
                        ->maxLength(255),

                    Textarea::make('description')
                        ->label('Description')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),

            Section::make('Contact')
                ->columns(2)
                ->components([
                    TextInput::make('phone_primary')
                        ->label('Phone 1')
                        ->maxLength(255),

                    TextInput::make('phone_secondary')
                        ->label('Phone 2')
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255)
                        ->columnSpanFull(),
                ]),

            Section::make('Office')
                ->components([
                    Textarea::make('office_address')
                        ->label('Address')
                        ->rows(4)
                        ->helperText('Use a new line for each address line.'),

                    TextInput::make('map_url')
                        ->label('Google Maps URL')
                        ->maxLength(2048)
                        ->helperText('Paste the full Google Maps link.')
                        ->url(),
                ]),

            Section::make('Social & Legal')
                ->columns(2)
                ->components([
                    TextInput::make('facebook_url')
                        ->label('Facebook URL')
                        ->url(),

                    TextInput::make('linkedin_url')
                        ->label('LinkedIn URL')
                        ->url(),

                    TextInput::make('privacy_policy_url')
                        ->label('Privacy Policy URL')
                        ->url()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('tagline')
                    ->label('Tagline'),

                TextColumn::make('email')
                    ->label('Email'),

                TextColumn::make('phone_primary')
                    ->label('Phone'),

                TextColumn::make('updated_at')
                    ->label('Last updated')
                    ->since(),
            ])
            ->recordActions([
                \\Filament\\Actions\\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFooterSettings::route('/'),
            'edit' => EditFooterSetting::route('/{record}/edit'),
        ];
    }
}
