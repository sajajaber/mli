@props(['heroAdvertisements'])

@php
$heroAds = $heroAdvertisements->values();
@endphp

<section
    class="mli-opening"
    aria-label="{{ app()->getLocale() === 'ar' ? 'العروض والبرامج' : 'Featured programmes' }}"
    x-data="{
        ads: @js($heroAds->map(fn ($ad) => [
            'image' => $ad->image_url,
            'alt' => $ad->image_alt ?: (app()->getLocale() === 'ar' ? 'صورة ترويجية من MLI' : 'MLI promotional artwork'),
        ])->values()),
        index: 0,
        timer: null,
        interval: 5500,
        start() {
            this.stop();
            if (this.ads.length > 1) {
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
            if (!this.ads.length) return;
            this.index = (this.index + 1) % this.ads.length;
            this.restart();
        },
        prev() {
            if (!this.ads.length) return;
            this.index = (this.index - 1 + this.ads.length) % this.ads.length;
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
    tabindex="0">
    <div class="mli-opening__grain" aria-hidden="true"></div>

    <div class="mli-opening__inner">
        <div class="mli-opening__feature">
            <a
                :href="'{{ route('shows.index') }}'"
                class="mli-opening__poster" data-depth="0.18" :class="{ 'mli-opening__poster--empty': !ads.length || !ads[index]?.image }">
                <template x-if="ads.length && ads[index]?.image">
                    <img
                        :src="ads[index].image"
                        :alt="ads[index].alt"
                        class="mli-opening__poster-image">
                </template>

            </a>

            <div class="mli-opening__controls" x-show="ads.length > 1" aria-label="{{ app()->getLocale() === 'ar' ? 'التنقل بين الإعلانات' : 'Hero navigation' }}">
                <button type="button" class="mli-opening__control mli-opening__control--prev" @click.stop="prev()" aria-label="{{ app()->getLocale() === 'ar' ? 'الإعلان السابق' : 'Previous advertisement' }}">
                    <span aria-hidden="true">‹</span>
                </button>
                <button type="button" class="mli-opening__control mli-opening__control--next" @click.stop="next()" aria-label="{{ app()->getLocale() === 'ar' ? 'الإعلان التالي' : 'Next advertisement' }}">
                    <span aria-hidden="true">›</span>
                </button>
            </div>

        </div>
    </div>

    <div class="mli-opening__brand-note">
        <span class="mli-opening__brand-note-line" aria-hidden="true"></span>
        <p>{{ app()->getLocale() === 'ar' ? 'تنوع. جودة. توافر.' : 'Variety. Quality. Availability.' }}</p>
        <a href="{{ route('shows.index') }}">
            {{ app()->getLocale() === 'ar' ? 'اكتشف المكتبة' : 'Explore the library' }}
            <b aria-hidden="true">↗</b>
        </a>
    </div>
</section>

{{-- Hero navigation uses Alpine state on the section above. --}}