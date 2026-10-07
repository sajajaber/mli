<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The first migration attempt may have added this column before
        // failing while changing the old unique index, so make this step
        // safe to retry.
        if (! Schema::hasColumn('site_contents', 'version')) {
            Schema::table('site_contents', function (Blueprint $table) {
                $table->unsignedInteger('version')->default(1)->after('key');
            });
        }

        // Remove any non-primary unique index that covers only the key column.
        // Laravel's schema inspection keeps this migration portable across
        // MySQL/MariaDB and SQLite.
        $indexes = collect(Schema::getIndexes('site_contents'))
            ->filter(fn (array $index) => $index['columns'] === ['key']
                && $index['unique']
                && ! $index['primary']);

        foreach ($indexes as $index) {
            Schema::table('site_contents', function (Blueprint $table) use ($index) {
                $table->dropUnique($index['name']);
            });
        }

        if (! Schema::hasIndex('site_contents', 'site_contents_key_version_unique')) {
            Schema::table('site_contents', function (Blueprint $table) {
                $table->unique(['key', 'version']);
            });
        }
    }

    public function down(): void
    {
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
};
