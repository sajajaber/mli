<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('shows', 'is_new_release')) {
            Schema::table('shows', function (Blueprint $table) {
                $table->boolean('is_new_release')->default(false)->after('status');
                $table->index(['is_new_release', 'status']);
            });
        }

        // Create four empty, admin-editable service slots. No real service
        // content is seeded here; the admin fills these records from Site Content.
        $now = now();

        foreach (range(1, 4) as $number) {
            DB::table('site_contents')->updateOrInsert(
                ['key' => 'media_service_' . $number, 'version' => 1],
                [
                    'title_en' => 'Media Service ' . $number,
                    'title_ar' => 'Media Service ' . $number,
                    'content_en' => null,
                    'content_ar' => null,
                    'status' => 'draft',
                    'published_at' => null,
                    'meta_title_en' => null,
                    'meta_title_ar' => null,
                    'meta_description_en' => null,
                    'meta_description_ar' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('site_contents')
            ->whereIn('key', [
                'media_service_1',
                'media_service_2',
                'media_service_3',
                'media_service_4',
            ])
            ->delete();

        if (Schema::hasColumn('shows', 'is_new_release')) {
            Schema::table('shows', function (Blueprint $table) {
                $table->dropIndex(['is_new_release', 'status']);
                $table->dropColumn('is_new_release');
            });
        }
    }
};
