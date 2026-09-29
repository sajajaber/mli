<?php

namespace App\Filament\Resources\FooterSettings\Pages;

use App\Filament\Resources\FooterSettings\FooterSettingResource;
use Filament\Resources\Pages\EditRecord;

class EditFooterSetting extends EditRecord
{
    protected static string $resource = FooterSettingResource::class;

    public function mount(int | string | null $record = null): void
    {
        $this->record = \App\Models\FooterSetting::query()->firstOrFail();

        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    protected function getRedirectUrl(): string
    {
        return FooterSettingResource::getUrl('edit');
    }
}
