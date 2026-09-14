<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        SiteContent::firstOrCreate(
            ['key' => 'about_us'],
            [
                'title_en' => 'About Us',
                'title_ar' => 'من نحن',
                'content_en' => 'Media Link International has been producing and distributing Arabic content for over 20 years, reaching broadcasters across the Middle East, North Africa, and Southeast Asia.',
                'content_ar' => 'تعمل ميديا لينك إنترناشونال منذ أكثر من 20 عامًا في إنتاج وتوزيع المحتوى العربي، ووصلت إلى محطات البث في الشرق الأوسط وشمال أفريقيا وجنوب شرق آسيا.',
                'status' => 'published',
            ]
        );

        SiteContent::firstOrCreate(
            ['key' => 'contact_us'],
            [
                'title_en' => 'Contact Us',
                'title_ar' => 'اتصل بنا',
                'content_en' => 'Beirut, Lebanon',
                'content_ar' => 'بيروت، لبنان',
                'status' => 'published',
            ]
        );
    }
}
