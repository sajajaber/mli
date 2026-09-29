<?php

namespace App\Filament\Resources\FooterSettings;

use App\Filament\Resources\FooterSettings\Pages\EditFooterSetting;
use App\Models\FooterSetting;
use BackedEnum;
use UnitEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class FooterSettingResource extends Resource
{
    protected static ?string $model = FooterSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Footer & Contact';

    protected static UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Footer Settings';

    protected static ?string $pluralModelLabel = 'Footer Settings';

    protected static bool $shouldRegisterNavigation = true;

    public static function getNavigationUrl(): string
    {
        return static::getUrl('edit');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Brand')
                ->description('Control the footer identity and bilingual brand messaging.')
                ->columnSpanFull()
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
                ->columnSpanFull()
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
                ->columnSpanFull()
                ->columns(2)
                ->components([
                    Textarea::make('office_address')
                        ->label('Office address (English)')
                        ->rows(5),

                    Textarea::make('office_address_ar')
                        ->label('Office address (Arabic)')
                        ->rows(5)
                        ->extraInputAttributes(['dir' => 'rtl']),

                    TextInput::make('map_url')
                        ->label('Google Maps URL')
                        ->maxLength(2048)
                        ->helperText('Paste the full Google Maps link.')
                        ->url(),
                ]),

            Section::make('Social & Legal')
                ->description('Social profiles and the privacy policy link.')
                ->columnSpanFull()
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

    public static function getPages(): array
    {
        return [
            'edit' => EditFooterSetting::route('/edit'),
        ];
    }
}
