<?php

namespace App\Filament\Resources\FooterSettings;

use App\Filament\Resources\FooterSettings\Pages\EditFooterSetting;
use App\Filament\Resources\FooterSettings\Pages\ListFooterSettings;
use App\Models\FooterSetting;
use BackedEnum;
use UnitEnum;
use Filament\Actions\EditAction;
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

    protected static ?string $navigationLabel = 'Footer & Contact';

    protected static UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Footer Settings';

    protected static ?string $pluralModelLabel = 'Footer Settings';

    protected static bool $shouldRegisterNavigation = true;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Brand')
                ->description('Control the footer identity and bilingual brand messaging.')
                ->columns(2)
                ->components([
                    TextInput::make('tagline')
                        ->label('Tagline (English)')
                        ->maxLength(255),

                    TextInput::make('tagline_ar')
                        ->label('Tagline (Arabic)')
                        ->maxLength(255)
                        ->extraInputAttributes(['dir' => 'rtl']),

                    Textarea::make('description')
                        ->label('Description (English)')
                        ->rows(5)
                        ->columnSpanFull(),

                    Textarea::make('description_ar')
                        ->label('Description (Arabic)')
                        ->rows(5)
                        ->extraInputAttributes(['dir' => 'rtl'])
                        ->columnSpanFull(),
                ]),

            Section::make('Contact')
                ->description('Public contact details displayed throughout the footer.')
                ->columns(2)
                ->components([
                    TextInput::make('phone_primary')
                        ->label('Primary phone')
                        ->maxLength(255),

                    TextInput::make('phone_secondary')
                        ->label('Secondary phone')
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255)
                        ->columnSpanFull(),
                ]),

            Section::make('Office')
                ->description('Office location and map destination.')
                ->columns(2)
                ->components([
                    Textarea::make('office_address')
                        ->label('Office address')
                        ->rows(5)
                        ->helperText('Use a new line for each address line.')
                        ->columnSpan(1),

                    TextInput::make('map_url')
                        ->label('Google Maps URL')
                        ->maxLength(2048)
                        ->helperText('Paste the full Google Maps link.')
                        ->url()
                        ->columnSpan(1),
                ]),

            Section::make('Social & Legal')
                ->description('Social profiles and the privacy policy link.')
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
                EditAction::make(),
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
