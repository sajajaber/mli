<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Genre;
use App\Models\News;
use App\Models\Show;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    /**
     * Seed illustrative content for local demos only.
     * Records are first-or-created, so existing content is not overwritten.
     * Image paths are empty until approved artwork is uploaded in the admin panel.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $documentary = Category::firstOrCreate(
            ['slug' => 'documentary'],
            ['name_en' => 'Documentary', 'name_ar' => 'وثائقي']
        );
        $series = Category::firstOrCreate(
            ['slug' => 'series'],
            ['name_en' => 'Series', 'name_ar' => 'مسلسلات']
        );

        $categories = [
            'documentary' => $documentary,
            'series' => $series,
            'nature' => Category::firstOrCreate(
                ['slug' => 'nature-environment'],
                ['parent_id' => $documentary->id, 'name_en' => 'Nature & Environment', 'name_ar' => 'الطبيعة والبيئة']
            ),
            'culture' => Category::firstOrCreate(
                ['slug' => 'culture-people'],
                ['parent_id' => $documentary->id, 'name_en' => 'Culture & People', 'name_ar' => 'الثقافة والناس']
            ),
            'travel' => Category::firstOrCreate(
                ['slug' => 'travel-places'],
                ['parent_id' => $documentary->id, 'name_en' => 'Travel & Places', 'name_ar' => 'السفر والأماكن']
            ),
        ];

        $genreDefinitions = [
            'drama' => ['Drama', 'دراما'],
            'family' => ['Family', 'عائلي'],
            'romance' => ['Romance', 'رومانسي'],
            'thriller' => ['Thriller', 'تشويق'],
            'mystery' => ['Mystery', 'غموض'],
        ];
        $genres = [];

        foreach ($genreDefinitions as $slug => [$nameEn, $nameAr]) {
            $genres[$slug] = Genre::firstOrCreate(
                ['category_id' => $series->id, 'slug' => $slug],
                ['name_en' => $nameEn, 'name_ar' => $nameAr]
            );
        }

        $shows = [
            [
                'slug' => 'voices-of-the-coast', 'title_en' => 'Voices of the Coast', 'title_ar' => 'أصوات الساحل',
                'description_en' => 'Along the eastern Mediterranean, fishing families and young conservationists are finding new ways to protect a shared shoreline. This documentary follows their daily work and the traditions that continue to shape life by the sea.',
                'description_ar' => 'على امتداد الساحل الشرقي للبحر المتوسط، تبحث عائلات الصيادين والمهتمون بالبيئة عن طرق جديدة لحماية الشاطئ. يتابع هذا الوثائقي عملهم اليومي والتقاليد التي لا تزال تشكّل الحياة قرب البحر.',
                'content_type' => 'documentary', 'category_slug' => 'nature', 'origin' => 'Regional documentary acquisition',
                'country_of_origin' => 'Lebanon', 'production_year' => 2025, 'season_number' => null, 'episode_count' => 4,
                'broadcast_format' => '4K', 'languages' => ['Arabic'], 'subtitles' => ['English', 'French'],
                'availability' => ['MENA', 'Worldwide'], 'service_types' => null, 'cast' => null,
                'directors' => ['Mira Saad'], 'is_new_release' => true, 'sort_order' => 1,
                'published_days_ago' => 2, 'genre_slugs' => [], 'cast_members' => [],
            ],
            [
                'slug' => 'the-olive-season', 'title_en' => 'The Olive Season', 'title_ar' => 'موسم الزيتون',
                'description_en' => 'Every autumn, three families return to the same groves to harvest olives and share stories passed between generations. Through a changing village, the film explores memory, land and the quiet resilience of rural life.',
                'description_ar' => 'في كل خريف، تعود ثلاث عائلات إلى البساتين نفسها لقطف الزيتون وتبادل الحكايات بين الأجيال. ومن خلال قرية تتغيّر مع الزمن، يستكشف الفيلم الذاكرة والأرض وقدرة الحياة الريفية على الاستمرار.',
                'content_type' => 'documentary', 'category_slug' => 'culture', 'origin' => 'Independent factual programming',
                'country_of_origin' => 'Lebanon', 'production_year' => 2023, 'season_number' => null, 'episode_count' => 3,
                'broadcast_format' => 'HD', 'languages' => ['Arabic'], 'subtitles' => ['English'],
                'availability' => ['MENA'], 'service_types' => null, 'cast' => null,
                'directors' => ['Karim Nassar'], 'is_new_release' => false, 'sort_order' => 2,
                'published_days_ago' => 8, 'genre_slugs' => [], 'cast_members' => [],
            ],
            [
                'slug' => 'across-the-levant', 'title_en' => 'Across the Levant', 'title_ar' => 'عبر بلاد الشام',
                'description_en' => 'Historic towns, mountain roads and bustling markets reveal the small rituals that give each place its character. Every episode meets the people keeping local craft, food and music alive for a new generation.',
                'description_ar' => 'تكشف المدن التاريخية والطرق الجبلية والأسواق النابضة العادات الصغيرة التي تمنح كل مكان طابعه الخاص. وفي كل حلقة، نلتقي بأشخاص يحافظون على الحرف والمطبخ والموسيقى المحلية وينقلونها إلى جيل جديد.',
                'content_type' => 'documentary', 'category_slug' => 'travel', 'origin' => 'Regional travel and culture catalogue',
                'country_of_origin' => 'Lebanon', 'production_year' => 2024, 'season_number' => null, 'episode_count' => 6,
                'broadcast_format' => 'HD', 'languages' => ['Arabic', 'English'], 'subtitles' => ['Arabic', 'English'],
                'availability' => ['Worldwide'], 'service_types' => null, 'cast' => null,
                'directors' => ['Layla Faris'], 'is_new_release' => false, 'sort_order' => 3,
                'published_days_ago' => 14, 'genre_slugs' => [], 'cast_members' => [],
            ],
            [
                'slug' => 'the-cedar-house', 'title_en' => 'The Cedar House', 'title_ar' => 'بيت الأرز',
                'description_en' => 'When a family returns to its old mountain home, a long-hidden letter unsettles the balance between three generations. The series blends family drama and gentle humour with a question of what we owe to the places we leave behind.',
                'description_ar' => 'عندما تعود عائلة إلى منزلها الجبلي القديم، تقلب رسالة مخبّأة منذ سنوات التوازن بين ثلاثة أجيال. يمزج المسلسل الدراما العائلية بالفكاهة الهادئة، ويطرح سؤالاً حول ما نتركه وراءنا وما يبقى معنا.',
                'content_type' => 'series', 'category_slug' => 'series', 'origin' => null, 'country_of_origin' => null,
                'production_year' => 2025, 'season_number' => 1, 'episode_count' => 12, 'broadcast_format' => null,
                'languages' => ['Arabic'], 'subtitles' => ['English'], 'availability' => ['MENA'],
                'service_types' => ['distribution', 'collaboration'], 'cast' => ['Maya Haddad', 'Omar Khalil', 'Rana Mansour'],
                'directors' => ['Nadine Khoury'], 'is_new_release' => true, 'sort_order' => 4,
                'published_days_ago' => 1, 'genre_slugs' => ['drama', 'family'],
                'cast_members' => [
                    ['actor_name_en' => 'Maya Haddad', 'actor_name_ar' => 'مايا حداد', 'character_name_en' => 'Lina Mansour', 'character_name_ar' => 'لينا منصور'],
                    ['actor_name_en' => 'Omar Khalil', 'actor_name_ar' => 'عمر خليل', 'character_name_en' => 'Nadim Mansour', 'character_name_ar' => 'نديم منصور'],
                    ['actor_name_en' => 'Rana Mansour', 'actor_name_ar' => 'رنا منصور', 'character_name_en' => 'Salma Mansour', 'character_name_ar' => 'سلمى منصور'],
                ],
            ],
            [
                'slug' => 'between-two-cities', 'title_en' => 'Between Two Cities', 'title_ar' => 'بين مدينتين',
                'description_en' => 'Two childhood friends build separate lives in Beirut and Amman until a shared project brings them together again. As old affection meets new ambition, both must decide what home means when life keeps moving.',
                'description_ar' => 'يبني صديقان منذ الطفولة حياتين مختلفتين في بيروت وعمّان، إلى أن يجمعهما مشروع مشترك من جديد. وبين مشاعر قديمة وطموحات جديدة، يواجه كل منهما سؤالاً عن معنى الوطن حين لا تتوقف الحياة عن التغيّر.',
                'content_type' => 'series', 'category_slug' => 'series', 'origin' => null, 'country_of_origin' => null,
                'production_year' => 2024, 'season_number' => 2, 'episode_count' => 10, 'broadcast_format' => null,
                'languages' => ['Arabic'], 'subtitles' => ['English', 'French'], 'availability' => ['MENA', 'Worldwide'],
                'service_types' => ['production'], 'cast' => ['Tarek Salim', 'Dima Barakat', 'Sami Younes'],
                'directors' => ['Fadi Rahal'], 'is_new_release' => false, 'sort_order' => 5,
                'published_days_ago' => 6, 'genre_slugs' => ['drama', 'romance'],
                'cast_members' => [
                    ['actor_name_en' => 'Tarek Salim', 'actor_name_ar' => 'طارق سليم', 'character_name_en' => 'Yazan', 'character_name_ar' => 'يزن'],
                    ['actor_name_en' => 'Dima Barakat', 'actor_name_ar' => 'ديما بركات', 'character_name_en' => 'Nour', 'character_name_ar' => 'نور'],
                    ['actor_name_en' => 'Sami Younes', 'actor_name_ar' => 'سامي يونس', 'character_name_en' => 'Rami', 'character_name_ar' => 'رامي'],
                ],
            ],
            [
                'slug' => 'the-ninth-hour', 'title_en' => 'The Ninth Hour', 'title_ar' => 'الساعة التاسعة',
                'description_en' => 'A late-night radio host receives a call that appears to predict a citywide blackout. Each episode follows a different witness as a small mystery grows into a puzzle no one can solve alone.',
                'description_ar' => 'يتلقى مذيع إذاعي في وقت متأخر اتصالاً يبدو وكأنه يتنبأ بانقطاع الكهرباء عن المدينة. وتتابع كل حلقة شاهداً مختلفاً، فيما يتحول لغز صغير إلى أحجية لا يستطيع أحد حلّها بمفرده.',
                'content_type' => 'series', 'category_slug' => 'series', 'origin' => null, 'country_of_origin' => null,
                'production_year' => 2025, 'season_number' => 1, 'episode_count' => 8, 'broadcast_format' => null,
                'languages' => ['Arabic'], 'subtitles' => ['English'], 'availability' => ['MENA'],
                'service_types' => ['distribution'], 'cast' => ['Jad Rahme', 'Nour Azzam', 'Hadi Yacoub'],
                'directors' => ['Rami Darwish'], 'is_new_release' => false, 'sort_order' => 6,
                'published_days_ago' => 11, 'genre_slugs' => ['thriller', 'mystery'],
                'cast_members' => [
                    ['actor_name_en' => 'Jad Rahme', 'actor_name_ar' => 'جاد رحمة', 'character_name_en' => 'Kareem', 'character_name_ar' => 'كريم'],
                    ['actor_name_en' => 'Nour Azzam', 'actor_name_ar' => 'نور عزام', 'character_name_en' => 'Maya', 'character_name_ar' => 'مايا'],
                    ['actor_name_en' => 'Hadi Yacoub', 'actor_name_ar' => 'هادي يعقوب', 'character_name_en' => 'Ralph', 'character_name_ar' => 'رالف'],
                ],
            ],
        ];

        foreach ($shows as $showData) {
            $category = $categories[$showData['category_slug']];
            $genreSlugs = $showData['genre_slugs'];
            $castMembers = $showData['cast_members'];
            $daysAgo = $showData['published_days_ago'];
            unset($showData['category_slug'], $showData['genre_slugs'], $showData['cast_members'], $showData['published_days_ago']);

            $showData['category_id'] = $category->id;
            $showData['status'] = 'published';
            $showData['published_at'] = now()->subDays($daysAgo);
            $showData['cover_image_path'] = null;
            $showData['cover_image_alt'] = $showData['title_en'];
            $showData['vimeo_url'] = null;
            $showData['watch_url'] = null;
            $showData['meta_title_en'] = $showData['title_en'];
            $showData['meta_title_ar'] = $showData['title_ar'];
            $showData['meta_description_en'] = Str::limit(strip_tags($showData['description_en']), 155);
            $showData['meta_description_ar'] = Str::limit(strip_tags($showData['description_ar']), 155);

            $show = Show::firstOrCreate(['slug' => $showData['slug']], $showData);

            // Don't touch relationships on an existing record with a matching slug.
            if (! $show->wasRecentlyCreated) {
                continue;
            }

            $show->categories()->syncWithoutDetaching([$category->id]);

            foreach ($genreSlugs as $genreSlug) {
                $show->genres()->syncWithoutDetaching([$genres[$genreSlug]->id]);
            }

            foreach ($castMembers as $order => $member) {
                $show->castMembers()->create([
                    ...$member,
                    'photo_path' => null,
                    'sort_order' => $order,
                ]);
            }
        }

        $newsItems = [
            [
                'slug' => 'demo-a-new-chapter-for-regional-storytelling',
                'title_en' => 'A New Chapter for Regional Storytelling',
                'title_ar' => 'فصل جديد في حكايات المنطقة',
                'news_type' => 'mli_news', 'days_ago' => 3,
                'body_en' => '<p>Audiences are looking for stories that travel across borders without losing the details that make them distinctive. From factual programmes rooted in local communities to character-led drama, a strong catalogue gives broadcasters room to speak to different viewers in their own way.</p><h2>Stories with a sense of place</h2><p>This sample newsroom feature explores how thoughtful curation, clear rights information and careful localisation can help regional stories reach a wider audience.</p>',
                'body_ar' => '<p>يبحث الجمهور عن حكايات قادرة على عبور الحدود من دون أن تفقد التفاصيل التي تمنحها هويتها. ومن البرامج الوثائقية المرتبطة بالمجتمعات المحلية إلى الدراما التي تقودها الشخصيات، تمنح المكتبة المتنوعة جهات البث مساحة للتواصل مع مشاهدين مختلفين.</p><h2>حكايات تحمل هوية المكان</h2><p>تتناول هذه المادة التجريبية أهمية اختيار المحتوى بعناية ووضوح معلومات الحقوق ومواءمة المواد للجمهور المستهدف.</p>',
            ],
            [
                'slug' => 'demo-inside-the-work-behind-every-programme',
                'title_en' => 'Inside the Work Behind Every Programme',
                'title_ar' => 'وراء كل برنامج حكاية',
                'news_type' => 'mli_news', 'days_ago' => 9,
                'body_en' => '<p>A finished programme is the result of many careful decisions: how a story is introduced, which details are preserved in translation, and how a title is presented to different audiences. The work continues through metadata, delivery specifications and the way content is organised for partners.</p><p>This editorial sample looks at the practical craft behind making a programme easier to discover, evaluate and schedule.</p>',
                'body_ar' => '<p>البرنامج الذي يصل إلى الشاشة هو نتيجة قرارات دقيقة كثيرة: كيف تُقدَّم الحكاية، وما التفاصيل التي يجب الحفاظ عليها في الترجمة، وكيف يُعرض العمل أمام جماهير مختلفة. ويستمر العمل عبر البيانات الوصفية ومواصفات التسليم وتنظيم المحتوى للشركاء.</p><p>تسلّط هذه المادة التجريبية الضوء على التفاصيل التي تجعل البرنامج أسهل في الاكتشاف والتقييم والبرمجة.</p>',
            ],
            [
                'slug' => 'demo-building-a-library-for-every-screen',
                'title_en' => 'Building a Library for Every Screen',
                'title_ar' => 'مكتبة محتوى لكل شاشة',
                'news_type' => 'mli_news', 'days_ago' => 16,
                'body_en' => '<p>A well-structured content library does more than collect titles. It helps teams compare formats, track language and subtitle versions, understand territories and identify programmes that fit a channel’s audience.</p><p>This sample feature looks at the organisational choices that turn a catalogue into a useful tool for acquisition and scheduling teams.</p>',
                'body_ar' => '<p>لا تقتصر مكتبة المحتوى المنظمة على جمع العناوين، بل تساعد الفرق على مقارنة الصيغ ومتابعة اللغات ونسخ الترجمة وفهم مناطق التوزيع واختيار البرامج المناسبة لجمهور القناة.</p><p>تستعرض هذه المادة التجريبية بعض الخيارات التي تجعل الكتالوج أداة عملية لفرق الشراء والبرمجة.</p>',
            ],
            [
                'slug' => 'demo-why-factual-stories-keep-finding-an-audience',
                'title_en' => 'Why Factual Stories Keep Finding an Audience',
                'title_ar' => 'لماذا تجد القصص الواقعية جمهورها؟',
                'news_type' => 'media_news', 'days_ago' => 5,
                'body_en' => '<p>Documentaries and factual series can offer viewers both a sense of discovery and a connection to everyday lives. Strong access, a clear point of view and careful pacing often matter as much as the subject itself.</p><p>This sample media article considers editorial choices that can make factual programming compelling across platforms and territories.</p>',
                'body_ar' => '<p>تمنح البرامج الوثائقية والواقعية المشاهد فرصة الاكتشاف والاقتراب من تفاصيل الحياة اليومية. وغالباً ما تكون سهولة الوصول إلى القصة ووضوح وجهة النظر والإيقاع المدروس على أهمية الموضوع نفسه.</p><p>تتناول هذه المادة الإعلامية التجريبية الخيارات التحريرية التي تجعل المحتوى الواقعي جذاباً عبر المنصات ومناطق التوزيع المختلفة.</p>',
            ],
            [
                'slug' => 'demo-the-changing-language-of-content-distribution',
                'title_en' => 'The Changing Language of Content Distribution',
                'title_ar' => 'لغة توزيع المحتوى في تغيّر مستمر',
                'news_type' => 'media_news', 'days_ago' => 13,
                'body_en' => '<p>Content distribution increasingly depends on matching the right version of a programme with the right service, territory and audience. Clear metadata, reliable delivery and an understanding of local viewing habits can make a catalogue easier to work with.</p><p>This sample media briefing summarises practical considerations without tying them to a specific company announcement or market forecast.</p>',
                'body_ar' => '<p>يعتمد توزيع المحتوى بصورة متزايدة على اختيار النسخة المناسبة من البرنامج لكل خدمة ومنطقة وجمهور. ويمكن للبيانات الواضحة والتسليم الموثوق وفهم عادات المشاهدة المحلية أن تجعل التعامل مع المكتبة أكثر سهولة.</p><p>تلخّص هذه الإحاطة الإعلامية التجريبية هذه الاعتبارات العملية من دون ربطها بإعلان محدد أو توقعات سوقية بعينها.</p>',
            ],
            [
                'slug' => 'demo-what-viewers-look-for-in-a-new-series',
                'title_en' => 'What Viewers Look for in a New Series',
                'title_ar' => 'ما الذي يبحث عنه المشاهد في مسلسل جديد؟',
                'news_type' => 'media_news', 'days_ago' => 21,
                'body_en' => '<p>A distinctive premise can attract attention, but audiences stay for characters they understand, relationships that evolve and a world that feels consistent. These elements also help a series retain its identity when presented across languages and markets.</p><p>This sample article explores storytelling fundamentals useful when evaluating scripted content for a catalogue.</p>',
                'body_ar' => '<p>قد تجذب الفكرة المميزة الانتباه، لكن الجمهور يستمر في المتابعة من أجل شخصيات يفهمها وعلاقات تتطور وعالم يبدو متماسكاً. وتساعد هذه العناصر المسلسل على الحفاظ على هويته عند عرضه بلغات وأسواق مختلفة.</p><p>تستكشف هذه المادة التجريبية بعض الأسس السردية المفيدة عند تقييم الأعمال الدرامية وإضافتها إلى المكتبة.</p>',
            ],
        ];

        foreach ($newsItems as $newsData) {
            $daysAgo = $newsData['days_ago'];
            unset($newsData['days_ago']);
            $newsData['status'] = 'published';
            $newsData['published_at'] = now()->subDays($daysAgo);
            $newsData['featured_image_path'] = null;
            $newsData['featured_image_alt'] = $newsData['title_en'];
            $newsData['meta_title_en'] = $newsData['title_en'];
            $newsData['meta_title_ar'] = $newsData['title_ar'];
            $newsData['meta_description_en'] = Str::limit(strip_tags($newsData['body_en']), 155);
            $newsData['meta_description_ar'] = Str::limit(strip_tags($newsData['body_ar']), 155);

            News::firstOrCreate(['slug' => $newsData['slug']], $newsData);
        }
    }
}
