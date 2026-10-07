<?php

namespace App\Models\Concerns;

use App\Models\SiteContent;
use Illuminate\Database\Eloquent\Model;

trait PublishesScheduledContent
{
    public static function publishDueScheduledItems(): int
    {
        $items = static::query()
            ->where('status', 'scheduled')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get();

        foreach ($items as $item) {
            if ($item instanceof SiteContent) {
                SiteContent::query()
                    ->where('key', $item->key)
                    ->where('status', 'published')
                    ->where('id', '!=', $item->getKey())
                    ->update(['status' => 'draft']);
            }

            $item->updateQuietly([
                'status' => 'published',
            ]);
        }

        return $items->count();
    }
}
