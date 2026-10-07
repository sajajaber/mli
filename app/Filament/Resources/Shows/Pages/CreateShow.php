<?php

namespace App\Filament\Resources\Shows\Pages;

use App\Filament\Resources\Shows\ShowResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateShow extends CreateRecord
{
    protected static string $resource = ShowResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['status'] ?? null) === 'scheduled') {
            $scheduledAt = ShowResource::normalizeScheduledAt($data['published_at'] ?? null);

            if (! $scheduledAt || $scheduledAt->lessThanOrEqualTo(now())) {
                throw ValidationException::withMessages([
                    'published_at' => 'Choose a future publication time in Beirut time.',
                ]);
            }

            $data['published_at'] = $scheduledAt;
        }

        if (($data['status'] ?? null) === 'published' && blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return ShowResource::getUrl('index');
    }
}
