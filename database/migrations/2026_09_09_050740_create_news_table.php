<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();

            $table->string('title_en');
            $table->string('title_ar');
            $table->string('slug')->unique();

            $table->longText('body_en')->nullable();
            $table->longText('body_ar')->nullable();

            $table->string('featured_image_path')->nullable();
            $table->string('featured_image_alt')->nullable();
            $table->enum('news_type', ['media_news', 'mli_news'])->default('mli_news');

            $table->enum('status', ['draft', 'scheduled', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->string('meta_title_en')->nullable();
            $table->string('meta_title_ar')->nullable();
            $table->string('meta_description_en')->nullable();
            $table->string('meta_description_ar')->nullable();

            $table->json('ai_flags')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
