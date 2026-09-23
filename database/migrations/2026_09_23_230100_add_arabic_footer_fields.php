<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('footer_settings', 'tagline_ar')) {
            Schema::table('footer_settings', function (Blueprint $table) {
                $table->string('tagline_ar')->nullable()->after('tagline');
            });
        }

        if (! Schema::hasColumn('footer_settings', 'description_ar')) {
            Schema::table('footer_settings', function (Blueprint $table) {
                $table->text('description_ar')->nullable()->after('description');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('footer_settings', 'description_ar')) {
            Schema::table('footer_settings', function (Blueprint $table) {
                $table->dropColumn('description_ar');
            });
        }

        if (Schema::hasColumn('footer_settings', 'tagline_ar')) {
            Schema::table('footer_settings', function (Blueprint $table) {
                $table->dropColumn('tagline_ar');
            });
        }
    }
};
