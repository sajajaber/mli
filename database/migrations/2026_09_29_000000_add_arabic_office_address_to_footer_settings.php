<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('footer_settings', 'office_address_ar')) {
            Schema::table('footer_settings', function (Blueprint $table) {
                $table->text('office_address_ar')->nullable()->after('office_address');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('footer_settings', 'office_address_ar')) {
            Schema::table('footer_settings', function (Blueprint $table) {
                $table->dropColumn('office_address_ar');
            });
        }
    }
};
