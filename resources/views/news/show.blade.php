<x-layouts.public
    :title="(app()->getLocale() === 'ar' ? $article->title_ar : $article->title_en) . ' — Media Link International'"
    :meta-description="app()->getLocale() === 'ar'
        ? ($article->meta_description_ar ?: \Illuminate\Support\Str::limit(strip_tags($article->body_ar ?? ''), 155))
        : ($article->meta_description_en ?: \Illuminate\Support\Str::limit(strip_tags($article->body_en ?? ''), 155))"
>
    @php
        $isArabic = app()->getLocale() === 'ar';
        $title = $isArabic ? $article->title_ar : $article->title_en;
        $body = $isArabic ? $article->body_ar : $article->body_en;
        $typeLabel = $article->news_type === 'media_news'
            ? ($isArabic ? 'أخبار الإعلام' : 'Media News')
            : ($isArabic ? 'أخبار MLI' : 'MLI News');
    @endphp

    <main class="mli-news-article">
        <article>
            <header class="mli-news-article__header">
                <div class="container">
                    <a href="{{ route('news.index') }}" class="mli-news-article__back">
                        <span aria-hidden="true">←</span>
                        {{ $isArabic ? 'العودة إلى الأخبار' : 'Back to news' }}
                    </a>

                    <div class="mli-news-article__meta">
                        <span>{{ $typeLabel }}</span>
                        <span>{{ optional($article->published_at)->format('d.m.Y') }}</span>
                    </div>

                    <h1 data-reveal="up">{{ $title }}</h1>
                </div>
            </header>

            @if($article->featured_image_url)
                <div class="mli-news-article__hero">
                    <div class="container">
                        <div class="mli-news-article__image" data-reveal="up">
                            <img
                                src="{{ $article->featured_image_url }}"
                                alt="{{ $article->featured_image_alt ?? $title }}"
                            >
                        </div>
                    </div>
                </div>
            @endif

            <div class="mli-news-article__body">
                <div class="container">
                    <div class="mli-news-article__body-inner">
                        <div class="mli-news-article__share">
                            <span>{{ $isArabic ? 'قصة' : 'Story' }}</span>
                            <span class="mli-news-article__line"></span>
                        </div>

                        <div class="mli-news-article__content">
                            @if($body)
                                {!! $body !!}
                            @else
                                <p>{{ $isArabic ? 'لا يوجد محتوى متاح لهذا الخبر.' : 'No article content is available.' }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </article>

        @if($related->isNotEmpty())
            <section class="mli-news-related">
                <div class="container">
                    <div class="mli-news-library__bar">
                        <span>{{ $isArabic ? 'قد يعجبك أيضاً' : 'More stories' }}</span>
                        <a href="{{ route('news.index', ['type' => $article->news_type]) }}">
                            {{ $isArabic ? 'عرض الكل' : 'View all' }} <span aria-hidden="true">↗</span>
                        </a>
                    </div>

                    <div class="mli-news-related__grid">
                        @foreach($related as $item)
                            <a href="{{ route('news.show', $item->slug) }}" class="mli-news-card">
                                <div class="mli-news-card__image">
                                    @if($item->featured_image_url)
                                        <img
                                            src="{{ $item->featured_image_url }}"
                                            alt="{{ $item->featured_image_alt ?? ($isArabic ? $item->title_ar : $item->title_en) }}"
                                            loading="lazy"
                                        >
                                    @endif
                                    <span class="mli-news-card__arrow" aria-hidden="true">↗</span>
                                </div>

                                <div class="mli-news-card__body">
                                    <div class="mli-news-card__meta">
                                        <span>{{ $article->news_type === 'media_news' ? ($isArabic ? 'أخبار الإعلام' : 'Media News') : ($isArabic ? 'أخبار MLI' : 'MLI News') }}</span>
                                        <span>{{ optional($item->published_at)->format('d.m.Y') }}</span>
                                    </div>

                                    <h2>{{ $isArabic ? $item->title_ar : $item->title_en }}</h2>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>
</x-layouts.public>
