<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_contents')
            ->whereIn('key', [
                'media_service_1',
                'media_service_2',
                'media_service_3',
                'media_service_4',
            ])
            ->delete();
    }

    public function down(): void
    {
        // The old migration is responsible for creating the original
        // placeholder records. They should not be recreated automatically.
    }
};
