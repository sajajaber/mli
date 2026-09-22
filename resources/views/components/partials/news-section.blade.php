@props(['mediaNews', 'mliNews'])

<section id="news" x-data="{ openModal: null, items: [
@foreach ($mliNews->concat($mediaNews) as $item)
{ id: {{ $item->id }}, title: @js(app()->getLocale() === 'ar' ? $item->title_ar : $item->title_en), body: @js(app()->getLocale() === 'ar' ? $item->body_ar : $item->body_en), image: @js($item->featured_image_url), imageAlt: @js($item->featured_image_alt) },
@endforeach
]}" class="section section--light">
    <div class="container">
        <div class="section-heading" data-reveal="up">
            <div>
                <p class="eyebrow eyebrow--dark">{{ app()->getLocale() === 'ar' ? 'آخر المستجدات' : 'Latest' }}</p>
                <h2>{{ app()->getLocale() === 'ar' ? 'من عالم MLI.' : 'From the world of MLI.' }}</h2>
            </div>
        </div>

        <div class="news-grid">
            <div class="news-column" data-reveal="left">
                <div class="news-column__head"><span>{{ app()->getLocale() === 'ar' ? 'أخبار MLI' : 'MLI News' }}</span><i></i></div>
                <div class="news-list">
                    <template x-for="item in items.slice(0, {{ $mliNews->count() }})" :key="item.id">
                        <button @click="openModal = item.id" class="news-item">
                            <span class="news-item__image"><img :src="item.image" :alt="item.imageAlt"></span>
                            <span class="news-item__body"><strong x-text="item.title"></strong><span>{{ app()->getLocale() === 'ar' ? 'اقرأ القصة' : 'Read story' }} ↗</span></span>
                        </button>
                    </template>
                    @if($mliNews->isEmpty()) <p class="empty-state">{{ app()->getLocale() === 'ar' ? 'لا توجد أخبار حاليًا.' : 'No news yet.' }}</p> @endif
                </div>
            </div>

            <div class="news-column" data-reveal="right">
                <div class="news-column__head news-column__head--gold"><span>{{ app()->getLocale() === 'ar' ? 'أخبار الإعلام' : 'Media News' }}</span><i></i></div>
                <div class="news-list">
                    <template x-for="item in items.slice({{ $mliNews->count() }})" :key="item.id">
                        <button @click="openModal = item.id" class="news-item">
                            <span class="news-item__image"><img :src="item.image" :alt="item.imageAlt"></span>
                            <span class="news-item__body"><strong x-text="item.title"></strong><span>{{ app()->getLocale() === 'ar' ? 'اقرأ القصة' : 'Read story' }} ↗</span></span>
                        </button>
                    </template>
                    @if($mediaNews->isEmpty()) <p class="empty-state">{{ app()->getLocale() === 'ar' ? 'لا توجد أخبار حاليًا.' : 'No news yet.' }}</p> @endif
                </div>
            </div>
        </div>
    </div>

    <x-partials.content-modal />
</section>