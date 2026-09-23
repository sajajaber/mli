<?php

namespace App\Filament\Resources\SiteContents\Pages;

use App\Filament\Resources\SiteContents\SiteContentResource;
use App\Models\SiteContent;
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

            return SiteContent::create($data);
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
