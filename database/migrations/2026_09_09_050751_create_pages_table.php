<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique(); // e.g. about-us, contact-us

            $table->string('title_en');
            $table->string('title_ar');

            $table->longText('content_en')->nullable();
            $table->longText('content_ar')->nullable();

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
        Schema::dropIfExists('pages');
    }
};
