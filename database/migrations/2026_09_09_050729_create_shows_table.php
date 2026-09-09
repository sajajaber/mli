<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title_en');
            $table->string('title_ar');
            $table->string('slug')->unique();

            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();

            $table->string('cover_image_path')->nullable();
            $table->string('cover_image_alt')->nullable();
            $table->string('vimeo_url')->nullable();

            $table->enum('status', ['draft', 'scheduled', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->string('meta_title_en')->nullable();
            $table->string('meta_title_ar')->nullable();
            $table->string('meta_description_en')->nullable();
            $table->string('meta_description_ar')->nullable();

            $table->json('ai_flags')->nullable();
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shows');
    }
};
