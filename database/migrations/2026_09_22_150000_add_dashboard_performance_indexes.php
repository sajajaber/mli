<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->index(['status', 'published_at']);
            $table->index('created_at');
        });

        Schema::table('news', function (Blueprint $table) {
            $table->index(['status', 'published_at']);
            $table->index('created_at');
        });

        Schema::table('site_contents', function (Blueprint $table) {
            $table->index(['status', 'published_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->dropIndex(['shows_status_published_at_index']);
            $table->dropIndex(['shows_created_at_index']);
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex(['news_status_published_at_index']);
            $table->dropIndex(['news_created_at_index']);
        });

        Schema::table('site_contents', function (Blueprint $table) {
            $table->dropIndex(['site_contents_status_published_at_index']);
            $table->dropIndex(['site_contents_created_at_index']);
        });
    }
};
