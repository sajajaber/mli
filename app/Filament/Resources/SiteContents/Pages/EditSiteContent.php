<?php

namespace App\Filament\Resources\SiteContents\Pages;

use App\Filament\Resources\SiteContents\SiteContentResource;
use App\Models\SiteContent;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class EditSiteContent extends EditRecord
{
    protected static string $resource = SiteContentResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var SiteContent $record */
        if (($data['status'] ?? null) === 'scheduled') {
            $scheduledAt = SiteContentResource::normalizeScheduledAt($data['published_at'] ?? null);

            if (! $scheduledAt || $scheduledAt->lessThanOrEqualTo(now())) {
                throw ValidationException::withMessages([
                    'published_at' => 'Choose a future publication time in Beirut time.',
                ]);
            }

            $data['published_at'] = $scheduledAt;
        }

        if (($data['status'] ?? null) === 'published') {
            SiteContent::query()
                ->where('key', $record->key)
                ->where('status', 'published')
                ->where('id', '!=', $record->getKey())
                ->update(['status' => 'draft']);

            $data['published_at'] = $data['published_at'] ?? now();
        }

        if (($data['status'] ?? null) === 'draft') {
            $data['published_at'] = null;
        }

        $record->update($data);

        Notification::make()
            ->success()
            ->title('Saved')
            ->body(
                $record->status === 'published'
                    ? 'Your changes are now live on the website.'
                    : 'Your changes were saved.'
            )
            ->send();

        return $record;
    }
}
