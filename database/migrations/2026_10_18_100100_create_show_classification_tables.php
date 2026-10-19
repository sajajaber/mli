<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_show', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            $table->unique(['show_id', 'category_id']);
        });

        // Preserve existing assignments. shows.category_id is intentionally
        // kept (and not modified) during the transition.
        DB::table('shows')
            ->whereNotNull('category_id')
            ->orderBy('id')
            ->select(['id', 'category_id'])
            ->chunkById(500, function ($shows) {
                DB::table('category_show')->insertOrIgnore(
                    $shows->map(fn($show) => [
                        'show_id' => $show->id,
                        'category_id' => $show->category_id,
                    ])->all()
                );
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_show');
    }
};
