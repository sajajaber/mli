<?php

namespace App\Filament\Resources\Shows\Pages;

use App\Filament\Resources\Shows\ShowResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditShow extends EditRecord
{
    protected static string $resource = ShowResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['status'] ?? null) === 'scheduled') {
            $scheduledAt = ShowResource::normalizeScheduledAt($data['published_at'] ?? null);

            if (! $scheduledAt || $scheduledAt->lessThanOrEqualTo(now('UTC'))) {
                throw ValidationException::withMessages([
                    'published_at' => 'Choose a future publication time in Beirut time.',
                ]);
            }

            $data['published_at'] = $scheduledAt;
        }

        if (($data['status'] ?? null) === 'published' && blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        if (($data['status'] ?? null) === 'draft') {
            $data['published_at'] = null;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
