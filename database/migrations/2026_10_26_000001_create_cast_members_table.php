<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cast_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->constrained()->cascadeOnDelete();
            $table->string('photo_path')->nullable();
            $table->string('actor_name_en');
            $table->string('actor_name_ar')->nullable();
            $table->string('character_name_en')->nullable();
            $table->string('character_name_ar')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['show_id', 'sort_order']);
        });

        // Preserve existing cast names by converting them into photo-ready records.
        if (Schema::hasColumn('shows', 'cast')) {
            DB::table('shows')
                ->select(['id', 'cast'])
                ->whereNotNull('cast')
                ->orderBy('id')
                ->chunk(100, function ($shows): void {
                    foreach ($shows as $show) {
                        $names = is_array($show->cast)
                            ? $show->cast
                            : json_decode((string) $show->cast, true);

                        if (! is_array($names)) {
                            continue;
                        }

                        $sortOrder = 0;

                        foreach ($names as $name) {
                            if (! is_string($name) || trim($name) === '') {
                                continue;
                            }

                            DB::table('cast_members')->insert([
                                'show_id' => $show->id,
                                'actor_name_en' => trim($name),
                                'photo_path' => null,
                                'sort_order' => $sortOrder++,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cast_members');
    }
};
