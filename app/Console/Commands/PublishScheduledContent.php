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
    protected $signature = 'content:publish-scheduled {--debug : Show scheduled timestamps and comparison details}';

    protected $description = 'Publish shows, news, and site content whose scheduled publish time has arrived';

    public function handle(): int
    {
        $now = now();
        $debug = (bool) $this->option('debug');
        $totalChecked = 0;
        $totalPublished = 0;

        foreach ([Show::class, News::class, SiteContent::class] as $modelClass) {
            $scheduledItems = $modelClass::query()
                ->where('status', 'scheduled')
                ->whereNotNull('published_at')
                ->get();

            foreach ($scheduledItems as $item) {
                $totalChecked++;

                $publishedAt = $item->published_at;

                if (! $publishedAt) {
                    continue;
                }

                $publishedAtBeirut = $publishedAt->copy()->timezone(config('app.timezone'));

                if ($debug) {
                    $this->line(sprintf(
                        '%s #%s — scheduled Beirut: %s | current Beirut: %s | due: %s',
                        class_basename($modelClass),
                        $item->getKey(),
                        $publishedAtBeirut->toDateTimeString(),
                        $now->toDateTimeString(),
                        $publishedAtBeirut->lessThanOrEqualTo($now) ? 'yes' : 'no',
                    ));
                }

                if ($publishedAtBeirut->greaterThan($now)) {
                    continue;
                }

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
                    'scheduled_for_beirut' => $publishedAtBeirut->toDateTimeString(),
                    'checked_at_beirut' => $now->toDateTimeString(),
                ]);
            }
        }

        if ($totalPublished > 0) {
            $this->info("Published {$totalPublished} item(s).");
        } else {
            $this->info("Checked {$totalChecked} scheduled item(s); none are due yet.");
        }

        return self::SUCCESS;
    }
}
