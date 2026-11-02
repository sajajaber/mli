<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (['shows', 'news'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('slug')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['shows', 'news'] as $tableName) {
            DB::table($tableName)
                ->whereNull('slug')
                ->orderBy('id')
                ->chunkById(500, function ($records) use ($tableName): void {
                    foreach ($records as $record) {
                        $baseSlug = "deleted-item-{$record->id}";
                        $slug = $baseSlug;
                        $suffix = 2;

                        while (DB::table($tableName)->where('slug', $slug)->exists()) {
                            $slug = "{$baseSlug}-{$suffix}";
                            $suffix++;
                        }

                        DB::table($tableName)
                            ->where('id', $record->id)
                            ->update(['slug' => $slug]);
                    }
                });

            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('slug')->nullable(false)->change();
            });
        }
    }
};
