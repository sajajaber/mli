<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_contents', function (Blueprint $table) {
            $table->unsignedInteger('version')->default(1)->after('key');
        });

        Schema::table('site_contents', function (Blueprint $table) {
            $table->dropUnique(['key']);
            $table->index(['key', 'version']);
        });
    }

    public function down(): void
    {
        Schema::table('site_contents', function (Blueprint $table) {
            $table->dropIndex(['key', 'version']);
            $table->dropColumn('version');
        });

        Schema::table('site_contents', function (Blueprint $table) {
            $table->unique('key');
        });
    }
};
