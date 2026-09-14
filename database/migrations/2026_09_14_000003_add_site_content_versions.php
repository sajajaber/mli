<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_contents', function (Blueprint $table) {
            $table->unsignedInteger('version')->default(1)->after('key');
        });

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

        Schema::table('site_contents', function (Blueprint $table) {
            $table->unique(['key', 'version']);
        });
    }

    public function down(): void
    {
        Schema::table('site_contents', function (Blueprint $table) {
            $table->dropUnique(['key', 'version']);
            $table->dropColumn('version');
        });

        Schema::table('site_contents', function (Blueprint $table) {
            $table->unique('key');
        });
    }
};
