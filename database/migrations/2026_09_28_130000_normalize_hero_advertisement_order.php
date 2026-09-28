<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $ids = DB::table('hero_advertisements')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id');

        foreach ($ids as $index => $id) {
            DB::table('hero_advertisements')
                ->where('id', $id)
                ->update([
                    'sort_order' => $index + 1,
                ]);
        }
    }

    public function down(): void
    {
        // The ordering is intentionally managed by the admin reorder control.
    }
};
