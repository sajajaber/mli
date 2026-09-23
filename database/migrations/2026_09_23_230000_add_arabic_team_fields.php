<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('people', 'name_ar')) {
            Schema::table('people', function (Blueprint $table) {
                $table->string('name_ar')->nullable()->after('name');
            });
        }

        if (! Schema::hasColumn('people', 'role_title_ar')) {
            Schema::table('people', function (Blueprint $table) {
                $table->string('role_title_ar')->nullable()->after('role_title');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('people', 'role_title_ar')) {
            Schema::table('people', function (Blueprint $table) {
                $table->dropColumn('role_title_ar');
            });
        }

        if (Schema::hasColumn('people', 'name_ar')) {
            Schema::table('people', function (Blueprint $table) {
                $table->dropColumn('name_ar');
            });
        }
    }
};
