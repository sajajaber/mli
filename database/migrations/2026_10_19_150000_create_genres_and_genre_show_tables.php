<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('slug');
            $table->timestamps();

            $table->unique(['category_id', 'name_en']);
            $table->unique(['category_id', 'name_ar']);
            $table->unique(['category_id', 'slug']);
        });

        Schema::create('genre_show', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->foreignId('show_id')->constrained()->cascadeOnDelete();

            $table->unique(['genre_id', 'show_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('genre_show');
        Schema::dropIfExists('genres');
    }
};
