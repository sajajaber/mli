<?php

namespace App\Filament\Resources\SiteContents\Widgets;

use App\Models\FooterSetting;
use Filament\Widgets\Widget;

class FooterSettingsRow extends Widget
{
    protected string $view = 'filament.resources.site-contents.widgets.footer-settings-row';

    protected int|string|array $columnSpan = 'full';

    public ?FooterSetting $footer = null;

    public function mount(): void
    {
        $this->footer = FooterSetting::query()->first();
    }
}
