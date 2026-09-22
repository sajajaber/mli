@props(['shows'])

<section class="mli-opening" aria-labelledby="hero-title">
    <div class="mli-opening__grain" aria-hidden="true"></div>
    <div class="mli-opening__line mli-opening__line--top" aria-hidden="true"></div>
    <div class="mli-opening__line mli-opening__line--side" aria-hidden="true"></div>

    <div class="mli-opening__meta">
        <span>MEDIA LINK INTERNATIONAL</span>
        <span>BEIRUT / 33.8938° N</span>
    </div>

    <div class="mli-opening__number" aria-hidden="true">01</div>

    <div class="mli-opening__inner">
        <div class="mli-opening__copy" data-reveal="left">
            <p class="mli-opening__eyebrow">
                <i></i>
                {{ app()->getLocale() === 'ar' ? 'إشارة من بيروت' : 'A signal from Beirut' }}
            </p>

            <h1 id="hero-title">
                @if(app()->getLocale() === 'ar')
                    نحرك<br><em>الحكايات.</em>
                @else
                    We move<br><em>stories.</em>
                @endif
            </h1>

            <p class="mli-opening__statement">
                @if(app()->getLocale() === 'ar')
                    محتوى عربي يصل إلى العالم — من شاشة إلى أخرى، ومن قصة إلى جمهور جديد.
                @else
                    Arabic stories moving from one screen to another, and from one audience to the next.
                @endif
            </p>

            <a href="{{ route('shows.index') }}" class="mli-opening__enter">
                <span>{{ app()->getLocale() === 'ar' ? 'ادخل إلى المكتبة' : 'Enter the library' }}</span>
                <b>↗</b>
            </a>
        </div>

        <div class="mli-opening__feature" data-reveal="scale">
            @php($featured = $shows->first())
            @if($featured && $featured->cover_image_url)
                <a href="{{ route('shows.index') }}" class="mli-opening__poster">
                    <img src="{{ $featured->cover_image_url }}" alt="{{ $featured->cover_image_alt ?? (app()->getLocale() === 'ar' ? $featured->title_ar : $featured->title_en) }}">
                    <span class="mli-opening__poster-wash"></span>
                    <span class="mli-opening__poster-label">FEATURED / 001</span>
                    <span class="mli-opening__poster-title">{{ app()->getLocale() === 'ar' ? $featured->title_ar : $featured->title_en }}</span>
                    <span class="mli-opening__poster-arrow">↗</span>
                </a>
            @else
                <div class="mli-opening__poster mli-opening__poster--empty">
                    <span>MLI / 001</span>
                </div>
            @endif

            <div class="mli-opening__reel">
                @foreach($shows->skip(1)->take(4) as $index => $show)
                    <a href="{{ route('shows.index') }}" title="{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}">
                        <span>0{{ $index + 2 }}</span>
                        @if($show->cover_image_url)
                            <img src="{{ $show->cover_image_url }}" alt="" loading="lazy">
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mli-opening__footer">
        <span>ARABIC CONTENT / GLOBAL REACH</span>
        <span class="mli-opening__scroll">SCROLL TO EXPLORE <b>↓</b></span>
        <span>MLI / 2026</span>
    </div>
</section>