<?php

namespace App\Filament\Concerns;

use Closure;
use Filament\Schemas\Components\Component;
use Livewire\Component as LivewireComponent;

trait ValidatesOnBlur
{
    protected static function validateOnBlur(): Closure
    {
        return static function (Component $component, LivewireComponent $livewire): void {
            $livewire->validateOnly($component->getStatePath());
        };
    }
}
