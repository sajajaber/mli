<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('media_services')->exists()) {
            return;
        }

        $now = now();

        DB::table('media_services')->insert([
            [
                'title_en' => 'Distribution',
                'title_ar' => 'التوزيع',
                'content_en' => null,
                'content_ar' => null,
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title_en' => 'Production',
                'title_ar' => 'الإنتاج',
                'content_en' => null,
                'content_ar' => null,
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title_en' => 'Library Content Licensing',
                'title_ar' => 'ترخيص محتوى المكتبة',
                'content_en' => null,
                'content_ar' => null,
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title_en' => 'Media Services',
                'title_ar' => 'الخدمات الإعلامية',
                'content_en' => null,
                'content_ar' => null,
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('media_services')
            ->whereIn('title_en', [
                'Distribution',
                'Production',
                'Library Content Licensing',
                'Media Services',
            ])
            ->delete();
    }
};
