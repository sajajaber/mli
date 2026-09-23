@props(['shows'])

@php
    $heroShows = $shows->values();
@endphp

<section
    class="mli-opening"
    aria-labelledby="hero-title"
    x-data="{
        shows: @js($heroShows->map(fn ($show) => [
            'title' => app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en,
            'image' => $show->cover_image_url,
            'alt' => $show->cover_image_alt ?? (app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en),
            'category' => $show->category
                ? (app()->getLocale() === 'ar' ? $show->category->name_ar : $show->category->name_en)
                : '',
        ])->values()),
        index: 0,
        timer: null,
        interval: 5500,
        start() {
            this.stop();
            if (this.shows.length > 1) {
                this.timer = setInterval(() => this.next(), this.interval);
            }
        },
        stop() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        },
        next() {
            if (!this.shows.length) return;
            this.index = (this.index + 1) % this.shows.length;
            this.restart();
        },
        prev() {
            if (!this.shows.length) return;
            this.index = (this.index - 1 + this.shows.length) % this.shows.length;
            this.restart();
        },
        goTo(index) {
            this.index = index;
            this.restart();
        },
        restart() {
            this.start();
        }
    }"
    x-init="start()"
    @mouseenter="stop()"
    @mouseleave="start()"
    @focusin="stop()"
    @focusout="start()"
    @keydown.left.prevent="prev()"
    @keydown.right.prevent="next()"
    tabindex="0"
>
    <div class="mli-opening__grain" aria-hidden="true"></div>

    <div class="mli-opening__number" aria-hidden="true">01</div>

    <div class="mli-opening__inner">
        <div class="mli-opening__copy" data-reveal="left">
            <p class="mli-opening__eyebrow">
                <i></i>
                {{ app()->getLocale() === 'ar' ? 'تنوع. جودة. توافر.' : 'Variety. Quality. Availability.' }}
            </p>

            <h1 id="hero-title">
                @if(app()->getLocale() === 'ar')
                    <span class="mli-opening__word">تنوع.</span> <span class="mli-opening__word">جودة.</span> <span class="mli-opening__word">توافر.</span>
                @else
                    <span class="mli-opening__word">Variety.</span> <span class="mli-opening__word">Quality.</span> <span class="mli-opening__word">Availability.</span>
                @endif
            </h1>

            <p class="mli-opening__statement">
                @if(app()->getLocale() === 'ar')
                    مكتبة مبنية على ثلاثة أركان: تنوع الخيارات، جودة المحتوى، وتوافره للجمهور.
                @else
                    A catalogue built around three things: breadth of choice, a commitment to quality, and content ready when audiences need it.
                @endif
            </p>

            <a href="{{ route('shows.index') }}" class="mli-opening__enter">
                <span>{{ app()->getLocale() === 'ar' ? 'اكتشف المكتبة' : 'Explore the library' }}</span>
                <b>↗</b>
            </a>
        </div>

        <div class="mli-opening__feature" data-reveal="scale">
            <a href="{{ route('shows.index') }}" class="mli-opening__poster" :class="{ 'mli-opening__poster--empty': !shows.length || !shows[index].image }">
                <template x-if="shows.length && shows[index].image">
                    <img
                        :src="shows[index].image"
                        :alt="shows[index].alt"
                        class="mli-opening__poster-image"
                    >
                </template>

                <span class="mli-opening__poster-wash"></span>
                <span class="mli-opening__poster-label">
                    SHOW / <span x-text="String(index + 1).padStart(2, '0')"></span>
                </span>

                <span class="mli-opening__poster-copy">
                    <small x-text="shows[index]?.category || '{{ app()->getLocale() === 'ar' ? 'محتوى MLI' : 'MLI CONTENT' }}'"></small>
                    <strong x-text="shows[index]?.title || '{{ app()->getLocale() === 'ar' ? 'لا توجد برامج منشورة' : 'No published shows yet' }}'"></strong>
                </span>

                <span class="mli-opening__poster-arrow">↗</span>
            </a>

            <div class="mli-opening__controls" x-show="shows.length > 1">
                <button type="button" @click="prev()" aria-label="{{ app()->getLocale() === 'ar' ? 'البرنامج السابق' : 'Previous show' }}">←</button>
                <span>
                    <b x-text="String(index + 1).padStart(2, '0')"></b>
                    /
                    <span x-text="String(shows.length).padStart(2, '0')"></span>
                </span>
                <button type="button" @click="next()" aria-label="{{ app()->getLocale() === 'ar' ? 'البرنامج التالي' : 'Next show' }}">→</button>
            </div>

            <div class="mli-opening__reel" x-show="shows.length > 1">
                <template x-for="(show, slideIndex) in shows.slice(0, 6)" :key="slideIndex">
                    <button
                        type="button"
                        class="mli-opening__thumb"
                        :class="{ 'is-active': index === slideIndex }"
                        @click="goTo(slideIndex)"
                        :aria-label="show.title"
                        :aria-current="index === slideIndex ? 'true' : 'false'"
                    >
                        <span x-text="String(slideIndex + 1).padStart(2, '0')"></span>
                        <template x-if="show.image">
                            <img :src="show.image" alt="" loading="lazy">
                        </template>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <div class="mli-opening__footer">
        <span>ARABIC CONTENT / GLOBAL REACH</span>
        <span class="mli-opening__scroll">SCROLL TO EXPLORE <b>↓</b></span>
        <span>MLI / 2026</span>
    </div>
</section>
