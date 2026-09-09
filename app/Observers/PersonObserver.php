<?php

namespace App\Observers;

use App\Models\Person;
use Illuminate\Support\Facades\Storage;

class PersonObserver
{
    public function updating(Person $person): void
    {
        if ($person->isDirty('photo_path')) {
            $original = $person->getOriginal('photo_path');

            if ($original && Storage::disk('public')->exists($original)) {
                Storage::disk('public')->delete($original);
            }
        }
    }

    public function forceDeleted(Person $person): void
    {
        if ($person->photo_path && Storage::disk('public')->exists($person->photo_path)) {
            Storage::disk('public')->delete($person->photo_path);
        }
    }
}
