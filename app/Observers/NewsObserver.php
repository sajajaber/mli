<?php

namespace App\Observers;

use App\Models\News;
use Illuminate\Support\Facades\Storage;

class NewsObserver
{
    public function updating(News $news): void
    {
        if ($news->isDirty('featured_image_path')) {
            $original = $news->getOriginal('featured_image_path');

            if ($original && Storage::disk('public')->exists($original)) {
                Storage::disk('public')->delete($original);
            }
        }
    }

    public function forceDeleted(News $news): void
    {
        if ($news->featured_image_path && Storage::disk('public')->exists($news->featured_image_path)) {
            Storage::disk('public')->delete($news->featured_image_path);
        }
    }
}
