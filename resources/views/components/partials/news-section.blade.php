@props(['mediaNews', 'mliNews'])

<section id="news" class="section section--light mli-news-section">
    <div class="container">
        <div class="section-heading" data-reveal="up">
            <div>
                <p class="eyebrow eyebrow--dark">{{ app()->getLocale() === 'ar' ? 'آخر المستجدات' : 'Latest' }}</p>
                <h2>{{ app()->getLocale() === 'ar' ? 'من عالم MLI.' : 'From the world of MLI.' }}</h2>
            </div>
        </div>

        <div class="news-grid">
            <div class="news-column" data-reveal="left">
                <div class="news-column__head">
                    <span>{{ app()->getLocale() === 'ar' ? 'أخبار MLI' : 'MLI News' }}</span><i></i>
                </div>
                <div class="news-list">
                    @forelse($mliNews as $item)
                        <a href="{{ route('news.show', $item->slug) }}" class="news-item">
                            <span class="news-item__image">
                                @if($item->featured_image_url)
                                    <img src="{{ $item->featured_image_url }}" alt="{{ $item->featured_image_alt ?? '' }}" loading="lazy">
                                @endif
                            </span>
                            <span class="news-item__body">
                                <strong>{{ app()->getLocale() === 'ar' ? $item->title_ar : $item->title_en }}</strong>
                                <span>{{ app()->getLocale() === 'ar' ? 'أخبار MLI' : 'MLI News' }} ↗</span>
                            </span>
                        </a>
                    @empty
                        <p class="empty-state">{{ app()->getLocale() === 'ar' ? 'لا توجد أخبار حاليًا.' : 'No MLI news yet.' }}</p>
                    @endforelse
                </div>
            </div>

            <div class="news-column" data-reveal="right">
                <div class="news-column__head news-column__head--gold">
                    <span>{{ app()->getLocale() === 'ar' ? 'أخبار الإعلام' : 'Media News' }}</span><i></i>
                </div>
                <div class="news-list">
                    @forelse($mediaNews as $item)
                        <a href="{{ route('news.show', $item->slug) }}" class="news-item">
                            <span class="news-item__image">
                                @if($item->featured_image_url)
                                    <img src="{{ $item->featured_image_url }}" alt="{{ $item->featured_image_alt ?? '' }}" loading="lazy">
                                @endif
                            </span>
                            <span class="news-item__body">
                                <strong>{{ app()->getLocale() === 'ar' ? $item->title_ar : $item->title_en }}</strong>
                                <span>{{ app()->getLocale() === 'ar' ? 'أخبار الإعلام' : 'Media News' }} ↗</span>
                            </span>
                        </a>
                    @empty
                        <p class="empty-state">{{ app()->getLocale() === 'ar' ? 'لا توجد أخبار حاليًا.' : 'No media news yet.' }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
