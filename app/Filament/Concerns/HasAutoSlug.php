<?php

namespace App\Filament\Concerns;

use Closure;
use Illuminate\Support\Str;

trait HasAutoSlug
{
    protected static function fillSlugFromTitle(
        string $titleField = 'title_en',
        string $slugField = 'slug',
    ): Closure {
        return function (
            ?string $state,
            callable $get,
            callable $set,
            ?string $operation = null
        ) use ($titleField, $slugField): void {
            if ($operation !== 'create' || filled($get($slugField))) {
                return;
            }

            $set($slugField, Str::slug($state ?? ''));
        };
    }
}
