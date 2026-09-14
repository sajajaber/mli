<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The first migration attempt may have added this column before
        // failing while changing the old unique index, so make this step
        // safe to retry.
        if (!Schema::hasColumn('site_contents', 'version')) {
            Schema::table('site_contents', function (Blueprint $table) {
                $table->unsignedInteger('version')->default(1)->after('key');
            });
        }

        // The original unique index came from the old `slug` column and may
        // still be named `pages_slug_unique` after the table/column rename.
        // Remove any non-primary unique index on `key` safely before allowing
        // multiple versions of the same section key.
        $indexes = DB::select("SHOW INDEX FROM `site_contents` WHERE `Column_name` = 'key'");

        foreach ($indexes as $index) {
            if ((int) $index->Non_unique === 0 && $index->Key_name !== 'PRIMARY') {
                Schema::table('site_contents', function (Blueprint $table) use ($index) {
                    $table->dropUnique($index->Key_name);
                });
            }
        }

        $versionIndexes = DB::select("SHOW INDEX FROM `site_contents` WHERE `Key_name` = 'site_contents_key_version_unique'");

        if (empty($versionIndexes)) {
            Schema::table('site_contents', function (Blueprint $table) {
                $table->unique(['key', 'version']);
            });
        }
    }

    public function down(): void
    {
        $versionIndexes = DB::select("SHOW INDEX FROM `site_contents` WHERE `Key_name` = 'site_contents_key_version_unique'");

        if (!empty($versionIndexes)) {
            Schema::table('site_contents', function (Blueprint $table) {
                $table->dropUnique('site_contents_key_version_unique');
            });
        }

        if (Schema::hasColumn('site_contents', 'version')) {
            Schema::table('site_contents', function (Blueprint $table) {
                $table->dropColumn('version');
            });
        }

        Schema::table('site_contents', function (Blueprint $table) {
            $table->unique('key');
        });
    }
};
