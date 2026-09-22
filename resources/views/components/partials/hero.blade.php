@props(['shows'])

<section class="hero" aria-labelledby="hero-title">
    <div class="hero__glow hero__glow--one"></div>
    <div class="hero__glow hero__glow--two"></div>

    <div class="hero__inner">
        <div class="hero__copy" data-reveal="up">
            <p class="eyebrow">{{ app()->getLocale() === 'ar' ? 'ميديا لينك إنترناشونال' : 'Media Link International' }}</p>
            <h1 id="hero-title">
                @if(app()->getLocale() === 'ar')
                    محتوى عربي.<br><em>بصوت عالمي.</em>
                @else
                    Arabic content.<br><em>Global reach.</em>
                @endif
            </h1>
            <p class="hero__description">
                @if(app()->getLocale() === 'ar')
                    ننتج ونوزّع محتوى عربيًا أصيلًا يصل إلى الجماهير والأسواق في المنطقة وحول العالم.
                @else
                    We produce and distribute authentic Arabic content for audiences and markets across the region and around the world.
                @endif
            </p>
            <div class="hero__actions">
                <a href="{{ route('shows.index') }}" class="button button--gold">
                    {{ app()->getLocale() === 'ar' ? 'استكشف أعمالنا' : 'Explore our work' }}
                    <span aria-hidden="true">↗</span>
                </a>
                <a href="#about" class="button button--ghost">
                    {{ app()->getLocale() === 'ar' ? 'تعرف علينا' : 'Discover MLI' }}
                </a>
            </div>
        </div>

        <div class="hero__visual" data-reveal="scale">
            @foreach($shows->take(5) as $index => $show)
                <a href="{{ route('shows.index') }}" class="hero-card hero-card--{{ $index + 1 }}" aria-label="{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}">
                    @if($show->cover_image_url)
                        <img src="{{ $show->cover_image_url }}" alt="{{ $show->cover_image_alt ?? (app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en) }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                    @endif
                    <span class="hero-card__shade"></span>
                    <span class="hero-card__label">{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="hero__bottom">
        <span>{{ app()->getLocale() === 'ar' ? 'اكتشف قصصًا جديدة' : 'Discover new stories' }}</span>
        <a href="#content" aria-label="{{ app()->getLocale() === 'ar' ? 'انتقل إلى المحتوى' : 'Scroll to content' }}"><span class="scroll-line"></span>↓</a>
    </div>
</section>