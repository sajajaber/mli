<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shows', function (Blueprint $table): void {
            // Leave existing records untouched; the admin form supplies a default for new records.
            $table->string('content_type', 24)->nullable()->after('id')->index();

            $table->string('origin')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->unsignedSmallInteger('production_year')->nullable();
            $table->unsignedSmallInteger('season_number')->nullable();
            $table->unsignedSmallInteger('episode_count')->nullable();
            $table->string('broadcast_format', 8)->nullable();
            $table->string('watch_url')->nullable();

            $table->json('languages')->nullable();
            $table->json('subtitles')->nullable();
            $table->json('availability')->nullable();
            $table->json('service_types')->nullable();
            $table->json('cast')->nullable();
            $table->json('directors')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('shows', function (Blueprint $table): void {
            $table->dropIndex(['content_type']);
            $table->dropColumn([
                'content_type',
                'origin',
                'country_of_origin',
                'production_year',
                'season_number',
                'episode_count',
                'broadcast_format',
                'watch_url',
                'languages',
                'subtitles',
                'availability',
                'service_types',
                'cast',
                'directors',
            ]);
        });
    }
};
