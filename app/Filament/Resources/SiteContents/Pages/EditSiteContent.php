<?php

namespace App\Filament\Resources\SiteContents\Pages;

use App\Filament\Resources\SiteContents\SiteContentResource;
use App\Models\SiteContent;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditSiteContent extends EditRecord
{
    protected static string $resource = SiteContentResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var SiteContent $record */
        if ($record->status === 'published') {
            $data['key'] = $record->key;
            $data['version'] = $record->nextVersion();
            $data['status'] = 'draft';
            $data['published_at'] = null;

            $draft = SiteContent::create($data);

            Notification::make()
                ->success()
                ->title('Draft version created')
                ->body('Your changes were saved as a new draft. Publish it to replace the live version.')
                ->send();

            return $draft;
        }

        if (($data['status'] ?? null) === 'scheduled') {
            $scheduledAt = SiteContentResource::normalizeScheduledAt($data['published_at'] ?? null);

            if (! $scheduledAt || $scheduledAt->lessThanOrEqualTo(now())) {
                throw \Illuminate\Validation\ValidationException::withMessages([
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

        $record->update($data);

        return $record;
    }
}
