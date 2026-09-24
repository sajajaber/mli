<x-layouts.public :title="'News — Media Link International'">
    @php
        $isArabic = app()->getLocale() === 'ar';
        $initialType = $type ?? '';
    @endphp

    <main
        class="mli-news-page"
        x-data="{
            activeType: @js($initialType),
            setType(type) {
                this.activeType = type;
                const url = new URL(window.location.href);

                if (type) {
                    url.searchParams.set('type', type);
                } else {
                    url.searchParams.delete('type');
                }

                window.history.replaceState({}, '', url);
            }
        }"
    >
        <section class="mli-shows-header mli-news-header">
            <div class="container">
                <div class="mli-shows-header__meta">
                    <span>{{ $isArabic ? 'غرفة أخبار MLI' : 'MLI / NEWSROOM' }}</span>
                    <span>{{ $news->count() }} {{ $isArabic ? 'خبراً' : 'STORIES' }}</span>
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
                <div class="mli-shows-filters">
                    <span class="mli-shows-filters__label">
                        {{ $isArabic ? 'تصفية حسب النوع' : 'Filter by type' }}
                    </span>

                    <div class="mli-shows-filters__links" role="tablist">
                        <button
                            type="button"
                            class="{{ !$initialType ? 'is-active' : '' }}"
                            :class="{ 'is-active': activeType === '' }"
                            @click="setType('')"
                        >
                            {{ $isArabic ? 'الكل' : 'All' }}
                        </button>

                        <button
                            type="button"
                            class="{{ $initialType === 'mli_news' ? 'is-active' : '' }}"
                            :class="{ 'is-active': activeType === 'mli_news' }"
                            @click="setType('mli_news')"
                        >
                            {{ $isArabic ? 'أخبار MLI' : 'MLI News' }}
                        </button>

                        <button
                            type="button"
                            class="{{ $initialType === 'media_news' ? 'is-active' : '' }}"
                            :class="{ 'is-active': activeType === 'media_news' }"
                            @click="setType('media_news')"
                        >
                            {{ $isArabic ? 'أخبار الإعلام' : 'Media News' }}
                        </button>
                    </div>
                </div>

                <div class="mli-shows-library__bar">
                    <span>{{ $isArabic ? 'جميع الأخبار' : 'All stories' }}</span>
                    <span x-text="activeType ? $el.closest('section').querySelectorAll('.mli-news-show-card[data-type=' + activeType + ']').length + ' / ' + {{ $news->count() }} : {{ $news->count() }}"></span>
                </div>

                <div class="mli-shows-grid mli-news-grid--shows-style">
                    @forelse($news as $index => $item)
                        <a
                            href="{{ route('news.show', $item->slug) }}"
                            class="mli-show mli-news-show-card"
                            data-type="{{ $item->news_type }}"
                            data-reveal="up"
                            style="--reveal-delay: {{ min($index % 6, 5) * 70 }}ms"
                            x-show="!activeType || activeType === '{{ $item->news_type }}'"
                            x-transition:enter="mli-news-filter-enter"
                            x-transition:enter-start="mli-news-filter-enter-start"
                            x-transition:enter-end="mli-news-filter-enter-end"
                            x-transition:leave="mli-news-filter-leave"
                            x-transition:leave-start="mli-news-filter-leave-start"
                            x-transition:leave-end="mli-news-filter-leave-end"
                            aria-label="{{ $isArabic ? 'قراءة ' : 'Read ' }}{{ $isArabic ? $item->title_ar : $item->title_en }}"
                        >
                            <span class="mli-show__image">
                                @if($item->featured_image_url)
                                    <img
                                        src="{{ $item->featured_image_url }}"
                                        alt="{{ $item->featured_image_alt ?? ($isArabic ? $item->title_ar : $item->title_en) }}"
                                        loading="lazy"
                                    >
                                @endif

                                <span class="mli-show__open" aria-hidden="true">↗</span>
                            </span>

                            <span class="mli-show__details">
                                <span>
                                    <h2>{{ $isArabic ? $item->title_ar : $item->title_en }}</h2>
                                    <p>
                                        {{ $item->news_type === 'media_news'
                                            ? ($isArabic ? 'أخبار الإعلام' : 'Media News')
                                            : ($isArabic ? 'أخبار MLI' : 'MLI News') }}
                                        ·
                                        {{ optional($item->published_at)->format('d.m.Y') }}
                                    </p>
                                </span>

                                <span class="mli-show__dash" aria-hidden="true"></span>
                            </span>
                        </a>
                    @empty
                        <div class="mli-news-empty">
                            <p>{{ $isArabic ? 'لا توجد أخبار منشورة حالياً.' : 'There are no published stories yet.' }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
</x-layouts.public>