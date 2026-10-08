<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Collapse legacy versioned records to one record per content key.
        // When the newest legacy record is the draft created by the old
        // versioning workflow, keep its edits and make them live.
        $keys = DB::table('site_contents')
            ->select('key')
            ->groupBy('key')
            ->pluck('key');

        foreach ($keys as $key) {
            $rows = DB::table('site_contents')
                ->where('key', $key)
                ->orderByDesc('id')
                ->get();

            if ($rows->count() < 2) {
                continue;
            }

            $latest = $rows->first();
            $published = $rows->firstWhere('status', 'published');

            if ($latest->status === 'draft' && $published && $latest->id !== $published->id) {
                DB::table('site_contents')
                    ->where('id', $published->id)
                    ->update([
                        'title_en' => $latest->title_en,
                        'title_ar' => $latest->title_ar,
                        'content_en' => $latest->content_en,
                        'content_ar' => $latest->content_ar,
                        'meta_title_en' => $latest->meta_title_en,
                        'meta_title_ar' => $latest->meta_title_ar,
                        'meta_description_en' => $latest->meta_description_en,
                        'meta_description_ar' => $latest->meta_description_ar,
                        'published_at' => now(),
                        'updated_at' => now(),
                    ]);

                $keepId = $published->id;
            } else {
                $keepId = $latest->id;
            }

            DB::table('site_contents')
                ->where('key', $key)
                ->where('id', '!=', $keepId)
                ->delete();
        }

        if (Schema::hasIndex('site_contents', 'site_contents_key_version_unique')) {
            Schema::table('site_contents', function (Blueprint $table) {
                $table->dropUnique('site_contents_key_version_unique');
            });
        }

        if (Schema::hasColumn('site_contents', 'version')) {
            Schema::table('site_contents', function (Blueprint $table) {
                $table->dropColumn('version');
            });
        }

        if (! Schema::hasIndex('site_contents', 'site_contents_key_unique')) {
            Schema::table('site_contents', function (Blueprint $table) {
                $table->unique('key', 'site_contents_key_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('site_contents', 'site_contents_key_unique')) {
            Schema::table('site_contents', function (Blueprint $table) {
                $table->dropUnique('site_contents_key_unique');
            });
        }

        if (! Schema::hasColumn('site_contents', 'version')) {
            Schema::table('site_contents', function (Blueprint $table) {
                $table->unsignedInteger('version')->default(1)->after('key');
            });
        }

        if (! Schema::hasIndex('site_contents', 'site_contents_key_version_unique')) {
            Schema::table('site_contents', function (Blueprint $table) {
                $table->unique(['key', 'version'], 'site_contents_key_version_unique');
            });
        }
    }
};
