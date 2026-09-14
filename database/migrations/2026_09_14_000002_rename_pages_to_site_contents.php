<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

        if (Schema::hasTable('site_contents')) {
            DB::table('site_contents')->where('key', 'about-us')->update(['key' => 'about_us']);
            DB::table('site_contents')->where('key', 'contact-us')->update(['key' => 'contact_us']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('site_contents')) {
            DB::table('site_contents')->where('key', 'about_us')->update(['key' => 'about-us']);
            DB::table('site_contents')->where('key', 'contact_us')->update(['key' => 'contact-us']);
        }

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
