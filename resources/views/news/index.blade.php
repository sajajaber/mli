<x-layouts.public :title="'News — Media Link International'">
    @php
        $isArabic = app()->getLocale() === 'ar';
    @endphp

    <main class="mli-news-page">
        <section class="mli-shows-header mli-news-header">
            <div class="container">
                <div class="mli-shows-header__meta">
                    <span>{{ $isArabic ? 'غرفة أخبار MLI' : 'MLI / NEWSROOM' }}</span>
                    <span>{{ $news->total() }} {{ $isArabic ? 'خبراً' : 'STORIES' }}</span>
                </div>

                <div class="mli-shows-header__main">
                    <p class="mli-shows-header__eyebrow">
                        {{ $isArabic ? 'الأخبار' : 'The news' }}
                    </p>

                    <div>
                        <h1>
                            {{ $isArabic ? 'قصص تستحق أن تُروى.' : 'Stories worth knowing.' }}
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

                    <div class="mli-shows-filters__links">
                        <a
                            href="{{ route('news.index') }}"
                            class="{{ !$type ? 'is-active' : '' }}"
                        >
                            {{ $isArabic ? 'الكل' : 'All' }}
                        </a>

                        <a
                            href="{{ route('news.index', ['type' => 'mli_news']) }}"
                            class="{{ $type === 'mli_news' ? 'is-active' : '' }}"
                        >
                            {{ $isArabic ? 'أخبار MLI' : 'MLI News' }}
                        </a>

                        <a
                            href="{{ route('news.index', ['type' => 'media_news']) }}"
                            class="{{ $type === 'media_news' ? 'is-active' : '' }}"
                        >
                            {{ $isArabic ? 'أخبار الإعلام' : 'Media News' }}
                        </a>
                    </div>
                </div>

                <div class="mli-shows-library__bar">
                    <span>{{ $isArabic ? 'جميع الأخبار' : 'All stories' }}</span>
                    <span>{{ $news->count() }} / {{ $news->total() }}</span>
                </div>

                <div class="mli-shows-grid mli-news-grid--shows-style">
                    @forelse($news as $index => $item)
                        <a
                            href="{{ route('news.show', $item->slug) }}"
                            class="mli-show mli-news-show-card"
                            data-reveal="up"
                            style="--reveal-delay: {{ min($index % 6, 5) * 70 }}ms"
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

                @if($news->hasPages())
                    <div class="mli-shows-pagination">
                        {{ $news->links() }}
                    </div>
                @endif
            </div>
        </section>
    </main>
</x-layouts.public>