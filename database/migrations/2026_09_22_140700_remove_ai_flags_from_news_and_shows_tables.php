<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('news', 'ai_flags')) {
            Schema::table('news', function (Blueprint $table) {
                $table->dropColumn('ai_flags');
            });
        }

        if (Schema::hasColumn('shows', 'ai_flags')) {
            Schema::table('shows', function (Blueprint $table) {
                $table->dropColumn('ai_flags');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('news', 'ai_flags')) {
            Schema::table('news', function (Blueprint $table) {
                $table->json('ai_flags')->nullable();
            });
        }

        if (! Schema::hasColumn('shows', 'ai_flags')) {
            Schema::table('shows', function (Blueprint $table) {
                $table->json('ai_flags')->nullable();
            });
        }
    }
};
