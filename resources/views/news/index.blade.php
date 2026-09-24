<x-layouts.public
    :title="app()->getLocale() === 'ar' ? 'الأخبار — ميديا لينك إنترناشونال' : 'News — Media Link International'"
    :meta-description="app()->getLocale() === 'ar'
        ? 'آخر أخبار ميديا لينك إنترناشونال وأخبار الإعلام.'
        : 'The latest news from Media Link International and the wider media world.'"
>
    @php
        $isArabic = app()->getLocale() === 'ar';

        $title = fn ($item) => $isArabic ? $item->title_ar : $item->title_en;
        $body = fn ($item) => $isArabic ? $item->body_ar : $item->body_en;
        $typeLabel = fn ($item) => $item->news_type === 'media_news'
            ? ($isArabic ? 'أخبار الإعلام' : 'Media News')
            : ($isArabic ? 'أخبار MLI' : 'MLI News');
    @endphp

    <main class="mli-news-page">
        <section class="mli-news-page__header">
            <div class="container">
                <div class="mli-news-page__meta">
                    <span>{{ $isArabic ? 'MLI / غرفة الأخبار' : 'MLI / NEWSROOM' }}</span>
                    <span>{{ $news->total() }} {{ $isArabic ? 'خبراً' : 'STORIES' }}</span>
                </div>

                <div class="mli-news-page__heading">
                    <div>
                        <p class="mli-news-page__eyebrow">
                            {{ $isArabic ? 'آخر المستجدات' : 'Latest updates' }}
                        </p>

                        <h1>
                            {{ $isArabic ? 'قصص من عالم MLI.' : 'Stories from the world of MLI.' }}
                        </h1>
                    </div>

                    <p class="mli-news-page__intro">
                        {{ $isArabic
                            ? 'أخبارنا، مشاريعنا، وما يحدث في عالم الإعلام والاتصال.'
                            : 'Our work, our news, and the stories shaping media and communication.' }}
                    </p>
                </div>

                <nav class="mli-news-page__filters" aria-label="{{ $isArabic ? 'تصفية الأخبار' : 'Filter news' }}">
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
                </nav>
            </div>
        </section>

        @if($featured)
            <section class="mli-news-featured">
                <div class="container">
                    <a
                        href="{{ route('news.show', $featured->slug) }}"
                        class="mli-news-featured__link"
                        data-reveal="up"
                    >
                        <div class="mli-news-featured__image">
                            @if($featured->featured_image_url)
                                <img
                                    src="{{ $featured->featured_image_url }}"
                                    alt="{{ $featured->featured_image_alt ?? $title($featured) }}"
                                >
                            @endif
                        </div>

                        <div class="mli-news-featured__content">
                            <div class="mli-news-card__meta">
                                <span>{{ $typeLabel($featured) }}</span>
                                <span>{{ optional($featured->published_at)->format('d.m.Y') }}</span>
                            </div>

                            <h2>{{ $title($featured) }}</h2>

                            @if($body($featured))
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($body($featured)), 180) }}</p>
                            @endif

                            <span class="mli-news-featured__read">
                                {{ $isArabic ? 'قراءة القصة' : 'Read story' }}
                                <b aria-hidden="true">↗</b>
                            </span>
                        </div>
                    </a>
                </div>
            </section>
        @endif

        <section class="mli-news-library">
            <div class="container">
                <div class="mli-news-library__bar">
                    <span>{{ $isArabic ? 'أحدث الأخبار' : 'Latest stories' }}</span>
                    <span>{{ $news->count() }} / {{ $news->total() }}</span>
                </div>

                <div class="mli-news-grid">
                    @forelse($news as $index => $item)
                        <a
                            href="{{ route('news.show', $item->slug) }}"
                            class="mli-news-card"
                            data-reveal="up"
                            style="--reveal-delay: {{ min($index % 6, 5) * 70 }}ms"
                        >
                            <div class="mli-news-card__image">
                                @if($item->featured_image_url)
                                    <img
                                        src="{{ $item->featured_image_url }}"
                                        alt="{{ $item->featured_image_alt ?? $title($item) }}"
                                        loading="lazy"
                                    >
                                @endif

                                <span class="mli-news-card__arrow" aria-hidden="true">↗</span>
                            </div>

                            <div class="mli-news-card__body">
                                <div class="mli-news-card__meta">
                                    <span>{{ $typeLabel($item) }}</span>
                                    <span>{{ optional($item->published_at)->format('d.m.Y') }}</span>
                                </div>

                                <h2>{{ $title($item) }}</h2>

                                @if($body($item))
                                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($body($item)), 120) }}</p>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="mli-news-empty">
                            <p>{{ $isArabic ? 'لا توجد أخبار منشورة حالياً.' : 'There are no published stories yet.' }}</p>
                        </div>
                    @endforelse
                </div>

                @if($news->hasPages())
                    <div class="mli-news-pagination">
                        {{ $news->links() }}
                    </div>
                @endif
            </div>
        </section>
    </main>
</x-layouts.public>
