@props(['shows'])

<section class="mli-stage" aria-labelledby="hero-title">
    <div class="mli-stage__grid" aria-hidden="true"></div>
    <div class="mli-stage__orb mli-stage__orb--a"></div>
    <div class="mli-stage__orb mli-stage__orb--b"></div>

    <div class="mli-stage__topline">
        <span>MLI / MEDIA LINK INTERNATIONAL</span>
        <span>CONTENT DISTRIBUTION / 2026</span>
    </div>

    <div class="mli-stage__inner">
        <div class="mli-stage__copy" data-reveal="up">
            <p class="mli-stage__index">01 — THE SIGNAL</p>
            <h1 id="hero-title">
                @if(app()->getLocale() === 'ar')
                    محتوى عربي.<br><span>بصوت عالمي.</span>
                @else
                    Arabic content.<br><span>with global reach.</span>
                @endif
            </h1>
            <p class="mli-stage__intro">
                @if(app()->getLocale() === 'ar')
                    نصنع ونوزّع القصص العربية التي تعبر الحدود.
                @else
                    We create and distribute Arabic stories that travel beyond borders.
                @endif
            </p>
            <a href="{{ route('shows.index') }}" class="mli-stage__link">
                {{ app()->getLocale() === 'ar' ? 'اكتشف أعمالنا' : 'Explore the work' }}
                <span>↗</span>
            </a>
        </div>

        <div class="mli-reel" data-reveal="scale" aria-label="{{ app()->getLocale() === 'ar' ? 'مختارات من برامج MLI' : 'Selected MLI shows' }}">
            <div class="mli-reel__track">
                @foreach($shows->take(5) as $index => $show)
                    <a href="{{ route('shows.index') }}" class="mli-reel__frame mli-reel__frame--{{ $index + 1 }}">
                        @if($show->cover_image_url)
                            <img src="{{ $show->cover_image_url }}" alt="{{ $show->cover_image_alt ?? (app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en) }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                        @endif
                        <span class="mli-reel__frame-number">0{{ $index + 1 }}</span>
                        <span class="mli-reel__frame-title">{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mli-stage__ticker" aria-hidden="true">
        <div class="mli-stage__ticker-track">
            <span>ORIGINAL STORIES</span><b>✦</b><span>ARABIC VOICES</span><b>✦</b><span>GLOBAL DISTRIBUTION</span><b>✦</b><span>ORIGINAL STORIES</span><b>✦</b><span>ARABIC VOICES</span><b>✦</b><span>GLOBAL DISTRIBUTION</span><b>✦</b>
        </div>
    </div>
</section>