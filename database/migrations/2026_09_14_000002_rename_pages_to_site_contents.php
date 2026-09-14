<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pages')) {
            Schema::rename('pages', 'site_contents');
        }

        if (Schema::hasTable('site_contents') && Schema::hasColumn('site_contents', 'slug')) {
            Schema::table('site_contents', function (Blueprint $table) {
                $table->renameColumn('slug', 'key');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('site_contents') && Schema::hasColumn('site_contents', 'key')) {
            Schema::table('site_contents', function (Blueprint $table) {
                $table->renameColumn('key', 'slug');
            });
        }

        if (Schema::hasTable('site_contents')) {
            Schema::rename('site_contents', 'pages');
        }
    }
};
