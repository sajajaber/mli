@props(['shows'])

@php
    $libraryShows = $shows->take(12)->values();
@endphp

<section id="content" class="mli-catalogue mli-catalogue--light">
    <div class="mli-catalogue__intro container" data-reveal="up">
        <div>
            <span class="mli-catalogue__index">02 / THE LIBRARY</span>
            <h2>{{ app()->getLocale() === 'ar' ? 'تنوع. جودة. توافر.' : 'Variety. Quality. Availability.' }}</h2>
        </div>
        <p>{{ app()->getLocale() === 'ar' ? 'ثلاثة مبادئ تشكل مكتبتنا — تنوع واسع، جودة مختارة بعناية، ومحتوى متاح للشركاء والجمهور.' : 'Three principles shape the library — a wide range of titles, carefully selected quality, and content ready for partners and audiences.' }}</p>
    </div>

    @if($libraryShows->isNotEmpty())
        <div class="mli-library-marquee" aria-label="{{ app()->getLocale() === 'ar' ? 'مكتبة البرامج' : 'Show library' }}">
            <div class="mli-library-marquee__viewport">
                <div class="mli-library-marquee__track">
                    @foreach($libraryShows as $index => $show)
                        <a href="{{ route('shows.index') }}" class="mli-library-marquee__item">
                            <span class="mli-library-marquee__number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>

                            <span class="mli-library-marquee__thumb">
                                @if($show->cover_image_url)
                                    <img
                                        src="{{ $show->cover_image_url }}"
                                        alt="{{ $show->cover_image_alt ?? '' }}"
                                        loading="lazy"
                                    >
                                @endif
                            </span>

                            <span class="mli-library-marquee__copy">
                                <strong>{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}</strong>
                                @if($show->category)
                                    <small>{{ app()->getLocale() === 'ar' ? $show->category->name_ar : $show->category->name_en }}</small>
                                @endif
                            </span>

                            <span class="mli-library-marquee__arrow" aria-hidden="true">↗</span>
                            <span class="mli-library-marquee__divider" aria-hidden="true">✦</span>
                        </a>
                    @endforeach

                    @foreach($libraryShows as $index => $show)
                        <a href="{{ route('shows.index') }}" class="mli-library-marquee__item" aria-hidden="true" tabindex="-1">
                            <span class="mli-library-marquee__number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>

                            <span class="mli-library-marquee__thumb">
                                @if($show->cover_image_url)
                                    <img src="{{ $show->cover_image_url }}" alt="" loading="lazy">
                                @endif
                            </span>

                            <span class="mli-library-marquee__copy">
                                <strong>{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}</strong>
                                @if($show->category)
                                    <small>{{ app()->getLocale() === 'ar' ? $show->category->name_ar : $show->category->name_en }}</small>
                                @endif
                            </span>

                            <span class="mli-library-marquee__arrow" aria-hidden="true">↗</span>
                            <span class="mli-library-marquee__divider" aria-hidden="true">✦</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="container">
            <p class="mli-content-empty">
                {{ app()->getLocale() === 'ar' ? 'لا توجد برامج منشورة حاليًا.' : 'No published shows yet.' }}
            </p>
        </div>
    @endif

    <div class="container mli-catalogue__bottom">
        <span>{{ str_pad($libraryShows->count(), 2, '0', STR_PAD_LEFT) }} / {{ app()->getLocale() === 'ar' ? 'برامج مختارة' : 'SELECTED TITLES' }}</span>
        <a href="{{ route('shows.index') }}">{{ app()->getLocale() === 'ar' ? 'شاهد المكتبة كاملة' : 'View the complete library' }} ↗</a>
    </div>
</section>
