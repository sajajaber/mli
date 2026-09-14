<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Models\Show;
use App\Models\SiteContent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishScheduledContent extends Command
{
    protected $signature = 'content:publish-scheduled';

    protected $description = 'Publish shows, news, and site content whose scheduled publish time has arrived';

    public function handle(): int
    {
        $now = now();
        $totalPublished = 0;

        foreach ([Show::class, News::class, SiteContent::class] as $modelClass) {
            $due = $modelClass::where('status', 'scheduled')
                ->where('published_at', '<=', $now)
                ->get();

            foreach ($due as $item) {
                if ($item instanceof SiteContent) {
                    SiteContent::query()
                        ->where('key', $item->key)
                        ->where('status', 'published')
                        ->where('id', '!=', $item->getKey())
                        ->update(['status' => 'draft']);
                }

                $item->update(['status' => 'published']);
                $totalPublished++;

                Log::info("Auto-published {$modelClass}", [
                    'id' => $item->id,
                    'title_en' => $item->title_en ?? null,
                    'published_at' => $item->published_at,
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
