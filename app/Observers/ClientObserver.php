<?php

namespace App\Observers;

use App\Models\Client;
use Illuminate\Support\Facades\Storage;

class ClientObserver
{
    public function updating(Client $client): void
    {
        if ($client->isDirty('logo_path')) {
            $original = $client->getOriginal('logo_path');

            if ($original && Storage::disk('public')->exists($original)) {
                Storage::disk('public')->delete($original);
            }
        }
    }

    public function forceDeleted(Client $client): void
    {
        if ($client->logo_path && Storage::disk('public')->exists($client->logo_path)) {
            Storage::disk('public')->delete($client->logo_path);
        }
    }
}
