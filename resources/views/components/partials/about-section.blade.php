@props(['content', 'stats'])

<section id="about" class="section section--navy">
    <div class="container about-layout">
        <div class="about-copy" data-reveal="left">
            <p class="eyebrow">{{ app()->getLocale() === 'ar' ? 'من نحن' : 'Who we are' }}</p>
            <h2>{{ app()->getLocale() === 'ar' ? ($content->title_ar ?? 'من نحن') : ($content->title_en ?? 'About MLI') }}</h2>
            <div class="rich-copy">
                {!! app()->getLocale() === 'ar' ? ($content->content_ar ?? '') : ($content->content_en ?? '') !!}
            </div>
        </div>

        <div class="stats-grid" data-reveal="right">
            <div class="stat"><strong>20<span>+</span></strong><small>{{ app()->getLocale() === 'ar' ? 'سنوات من الخبرة' : 'Years of experience' }}</small></div>
            <div class="stat"><strong>{{ $stats['shows'] }}<span>+</span></strong><small>{{ app()->getLocale() === 'ar' ? 'عمل موزع' : 'Titles distributed' }}</small></div>
            <div class="stat"><strong>{{ $stats['clients'] }}<span>+</span></strong><small>{{ app()->getLocale() === 'ar' ? 'عملاء وشركاء' : 'Clients & partners' }}</small></div>
            <div class="stat"><strong>3</strong><small>{{ app()->getLocale() === 'ar' ? 'مناطق توزيع' : 'Distribution regions' }}</small></div>
        </div>
    </div>
</section>