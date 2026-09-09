<?php

namespace App\Console\Commands;

use App\Models\Show;
use App\Models\News;
use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishScheduledContent extends Command
{
    protected $signature = 'content:publish-scheduled';

    protected $description = 'Publish shows, news, and pages whose scheduled publish time has arrived';

    public function handle(): int
    {
        $now = now();
        $totalPublished = 0;

        foreach ([Show::class, News::class, Page::class] as $modelClass) {
            $due = $modelClass::where('status', 'scheduled')
                ->where('published_at', '<=', $now)
                ->get();

            foreach ($due as $item) {
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
