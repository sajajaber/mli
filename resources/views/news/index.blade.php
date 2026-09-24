<x-layouts.public :title="'News — Media Link International'">
    @php
        $isArabic = app()->getLocale() === 'ar';

        $newsData = $news->map(fn ($item) => [
            'id' => $item->id,
            'slug' => $item->slug,
            'type' => $item->news_type,
            'title' => $isArabic ? $item->title_ar : $item->title_en,
            'category' => $item->news_type === 'media_news'
                ? ($isArabic ? 'أخبار الإعلام' : 'Media News')
                : ($isArabic ? 'أخبار MLI' : 'MLI News'),
            'date' => optional($item->published_at)->format('d.m.Y'),
            'image' => $item->featured_image_url,
            'alt' => $item->featured_image_alt ?? ($isArabic ? $item->title_ar : $item->title_en),
            'url' => route('news.show', $item->slug),
        ])->values();

        $initialType = in_array($type, ['mli_news', 'media_news'], true) ? $type : '';
    @endphp

    <main
        class="mli-news-page"
        x-data="{
            news: @js($newsData),
            activeType: @js($initialType),
            get filteredNews() {
                return this.activeType
                    ? this.news.filter(item => item.type === this.activeType)
                    : this.news;
            },
            setType(type) {
                this.activeType = type;

                const url = new URL(window.location.href);

                if (type) {
                    url.searchParams.set('type', type);
                } else {
                    url.searchParams.delete('type');
                }

                window.history.replaceState({}, '', url);
            },
            syncFromUrl() {
                const type = new URL(window.location.href).searchParams.get('type');
                this.activeType = ['mli_news', 'media_news'].includes(type) ? type : '';
            }
        }"
        x-init="window.addEventListener('popstate', () => syncFromUrl())"
    >
        <section class="mli-shows-header mli-news-header">
            <div class="container">
                <div class="mli-shows-header__meta">
                    <span>{{ $isArabic ? 'غرفة أخبار MLI' : 'MLI / NEWSROOM' }}</span>
                    <span x-text="news.length + ' {{ $isArabic ? 'خبراً' : 'STORIES' }}'"></span>
                </div>

                <div class="mli-shows-header__main">
                    <p class="mli-shows-header__eyebrow">
                        {{ $isArabic ? 'الأخبار' : 'The news' }}
                    </p>

                    <div>
                        <h1>
                            {{ $isArabic ? 'قصص تستحق أن تُعرف.' : 'Stories worth knowing.' }}
                        </h1>
                    </div>

                    <p class="mli-shows-header__copy">
                        {{ $isArabic
                            ? 'آخر أخبار MLI وأخبار الإعلام والاتصال.'
                            : 'The latest from MLI, media, and the world of communication.' }}
                    </p>
                </div>
            </div>
        </section>

        <section class="mli-shows-library mli-news-library">
            <div class="container">
                <div class="mli-shows-filters" role="tablist">
                    <span class="mli-shows-filters__label">
                        {{ $isArabic ? 'تصفية حسب النوع' : 'Filter by type' }}
                    </span>

                    <div class="mli-shows-filters__links">
                        <button
                            type="button"
                            role="tab"
                            :aria-selected="activeType === ''"
                            :class="{ 'is-active': activeType === '' }"
                            @click="setType('')"
                        >
                            {{ $isArabic ? 'الكل' : 'All' }}
                        </button>

                        <button
                            type="button"
                            role="tab"
                            :aria-selected="activeType === 'mli_news'"
                            :class="{ 'is-active': activeType === 'mli_news' }"
                            @click="setType('mli_news')"
                        >
                            {{ $isArabic ? 'أخبار MLI' : 'MLI News' }}
                        </button>

                        <button
                            type="button"
                            role="tab"
                            :aria-selected="activeType === 'media_news'"
                            :class="{ 'is-active': activeType === 'media_news' }"
                            @click="setType('media_news')"
                        >
                            {{ $isArabic ? 'أخبار الإعلام' : 'Media News' }}
                        </button>
                    </div>
                </div>

                <div class="mli-shows-library__bar">
                    <span x-text="activeType === 'mli_news'
                        ? '{{ $isArabic ? 'أخبار MLI' : 'MLI News' }}'
                        : activeType === 'media_news'
                            ? '{{ $isArabic ? 'أخبار الإعلام' : 'Media News' }}'
                            : '{{ $isArabic ? 'جميع الأخبار' : 'All stories' }}'">
                    </span>

                    <span x-text="filteredNews.length + ' / ' + news.length"></span>
                </div>

                <div class="mli-shows-grid mli-news-grid--shows-style">
                    <template x-for="(item, index) in filteredNews" :key="item.id">
                        <a
                            :href="item.url"
                            class="mli-show mli-news-show-card"
                            :style="'--reveal-delay: ' + (Math.min(index % 6, 5) * 70) + 'ms'"
                            x-transition:enter="mli-news-filter-enter"
                            x-transition:enter-start="mli-news-filter-enter-start"
                            x-transition:enter-end="mli-news-filter-enter-end"
                            x-transition:leave="mli-news-filter-leave"
                            x-transition:leave-start="mli-news-filter-leave-start"
                            x-transition:leave-end="mli-news-filter-leave-end"
                            :aria-label="'{{ $isArabic ? 'قراءة ' : 'Read ' }}' + item.title"
                        >
                            <span class="mli-show__image">
                                <template x-if="item.image">
                                    <img :src="item.image" :alt="item.alt" loading="lazy">
                                </template>

                                <span class="mli-show__open" aria-hidden="true">↗</span>
                            </span>

                            <span class="mli-show__details">
                                <span>
                                    <h2 x-text="item.title"></h2>
                                    <p x-text="item.category + ' · ' + (item.date || '')"></p>
                                </span>

                                <span class="mli-show__dash" aria-hidden="true"></span>
                            </span>
                        </a>
                    </template>

                    <div
                        x-show="filteredNews.length === 0"
                        x-cloak
                        class="mli-news-empty"
                    >
                        <p>{{ $isArabic ? 'لا توجد أخبار منشورة حالياً.' : 'There are no published stories in this category.' }}</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</x-layouts.public>
