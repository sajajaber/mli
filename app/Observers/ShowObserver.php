<?php

namespace App\Observers;

use App\Models\Show;
use Illuminate\Support\Facades\Storage;

class ShowObserver
{
    public function updating(Show $show): void
    {
        // If the cover image was replaced, delete the old file from disk.
        if ($show->isDirty('cover_image_path')) {
            $original = $show->getOriginal('cover_image_path');

            if ($original && Storage::disk('public')->exists($original)) {
                Storage::disk('public')->delete($original);
            }
        }
    }

    public function forceDeleted(Show $show): void
    {
        // Only clean up the file on permanent deletion, not on soft delete
        // (a soft-deleted show might still be restored later).
        if ($show->cover_image_path && Storage::disk('public')->exists($show->cover_image_path)) {
            Storage::disk('public')->delete($show->cover_image_path);
        }
    }
}
