<?php

namespace App\Filament\Concerns;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Component;
use Illuminate\Database\Eloquent\Model;

trait HasRecordPreviewActions
{
    /**
     * @param  array<Component>  $schema
     */
    protected static function recordPreviewAction(?array $schema = null): ViewAction
    {
        $action = ViewAction::make()
            ->modalWidth('7xl')
            ->extraModalFooterActions([
                EditAction::make()
                    ->url(fn (Model $record): string => static::getUrl('edit', ['record' => $record])),
            ]);

        if ($schema !== null) {
            $action->schema($schema);
        }

        return $action;
    }

    protected static function recordEditAction(): EditAction
    {
        return EditAction::make()
            ->url(fn (Model $record): string => static::getUrl('edit', ['record' => $record]));
    }
}
