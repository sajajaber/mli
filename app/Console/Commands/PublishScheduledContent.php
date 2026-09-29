<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Models\Show;
use App\Models\SiteContent;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class PublishScheduledContent extends Command
{
    protected $signature = 'content:publish-scheduled';

    protected $description = 'Publish shows, news, and site content whose scheduled publish time has arrived';

    public function handle(): int
    {
        $now = Carbon::now('UTC');
        $totalPublished = 0;

        foreach ([Show::class, News::class, SiteContent::class] as $modelClass) {
            $due = $modelClass::query()
                ->where('status', 'scheduled')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', $now->toDateTimeString())
                ->get();

            foreach ($due as $item) {
                if ($item instanceof SiteContent) {
                    SiteContent::query()
                        ->where('key', $item->key)
                        ->where('status', 'published')
                        ->where('id', '!=', $item->getKey())
                        ->update(['status' => 'draft']);
                }

                $updated = $modelClass::query()
                    ->whereKey($item->getKey())
                    ->where('status', 'scheduled')
                    ->update(['status' => 'published']);

                if ($updated !== 1) {
                    continue;
                }

                $totalPublished++;

                Log::info('Auto-published scheduled content.', [
                    'model' => $modelClass,
                    'id' => $item->getKey(),
                    'title_en' => $item->title_en ?? null,
                    'scheduled_for_utc' => $item->published_at?->copy()->utc()->toDateTimeString(),
                    'checked_at_utc' => $now->toDateTimeString(),
                ]);
            }
        }

        if ($totalPublished > 0) {
            $this->info("Published {$totalPublished} item(s).");
        } else {
            $this->info('No scheduled content due for publishing.');
        }

        return self::SUCCESS;
    }
}
