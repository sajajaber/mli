<x-layouts.public :title="'Shows — Media Link International'">
    @php
        $showData = $shows->map(fn ($show) => [
            'title' => app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en,
            'category' => $show->category
                ? (app()->getLocale() === 'ar' ? $show->category->name_ar : $show->category->name_en)
                : '',
            'description' => app()->getLocale() === 'ar' ? $show->description_ar : $show->description_en,
            'image' => $show->cover_image_url,
            'alt' => $show->cover_image_alt ?? (app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en),
            'vimeo' => $show->vimeo_url,
        ])->values();
    @endphp

    <main
        class="mli-shows-page"
        x-data="{
            shows: @js($showData),
            selectedShow: null,
            openShow(index) {
                this.selectedShow = this.shows[index];
                document.body.classList.add('modal-open');
            },
            closeShow() {
                this.selectedShow = null;
                document.body.classList.remove('modal-open');
            }
        }"
        @keydown.escape.window="closeShow()"
    >
        <section class="mli-shows-header">
            <div class="container">
                <div class="mli-shows-header__meta">
                    <span>{{ app()->getLocale() === 'ar' ? 'مكتبة MLI' : 'MLI / CONTENT LIBRARY' }}</span>
                    <span>{{ $shows->total() }} {{ app()->getLocale() === 'ar' ? 'برنامجاً' : 'SHOWS' }}</span>
                </div>

                <div class="mli-shows-header__main">
                    <p class="mli-shows-header__eyebrow">
                        {{ app()->getLocale() === 'ar' ? 'البرامج' : 'The shows' }}
                    </p>

                    <h1>
                        {{ app()->getLocale() === 'ar' ? 'مكتبة البرامج' : 'Explore Our Catalogue' }}
                    </h1>

                    <p class="mli-shows-header__copy">
                        {{ app()->getLocale() === 'ar'
                            ? 'مجموعة من البرامج والمحتوى الذي تنتجه وتوزعه MLI.'
                            : 'A collection of programs and content produced and distributed by MLI.' }}
                    </p>
                </div>
            </div>
        </section>

        <section class="mli-shows-library">
            <div class="container">
                <div class="mli-shows-filters">
                    <span class="mli-shows-filters__label">
                        {{ app()->getLocale() === 'ar' ? 'تصفية حسب النوع' : 'Filter by category' }}
                    </span>

                    <div class="mli-shows-filters__links">
                        <a
                            href="{{ route('shows.index') }}"
                            class="{{ !$categorySlug ? 'is-active' : '' }}"
                        >
                            {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}
                        </a>

                        @foreach($categories as $category)
                            <a
                                href="{{ route('shows.index', ['category' => $category->slug]) }}"
                                class="{{ $categorySlug === $category->slug ? 'is-active' : '' }}"
                            >
                                {{ app()->getLocale() === 'ar' ? $category->name_ar : $category->name_en }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="mli-shows-library__bar">
                    <span>{{ app()->getLocale() === 'ar' ? 'جميع البرامج' : 'All shows' }}</span>
                    <span>{{ $shows->count() }} / {{ $shows->total() }}</span>
                </div>

                <div class="mli-shows-grid">
                    @foreach($shows as $index => $show)
                        <button
                            type="button"
                            class="mli-show"
                            data-reveal="up"
                            style="--reveal-delay: {{ min($index % 6, 5) * 70 }}ms"
                            @click="openShow({{ $index }})"
                            aria-label="{{ app()->getLocale() === 'ar' ? 'عرض تفاصيل ' : 'View details for ' }}{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}"
                        >
                            <span class="mli-show__image">
                                @if($show->cover_image_url)
                                    <img
                                        src="{{ $show->cover_image_url }}"
                                        alt="{{ $show->cover_image_alt ?? '' }}"
                                        loading="lazy"
                                    >
                                @endif

                                <span class="mli-show__wash" aria-hidden="true"></span>

                                <span class="mli-show__copy">
                                    <strong>{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}</strong>

                                    @if($show->category)
                                        <small>{{ app()->getLocale() === 'ar' ? $show->category->name_ar : $show->category->name_en }}</small>
                                    @endif
                                </span>

                                <span class="mli-show__open" aria-hidden="true">↗</span>
                            </span>
                        </button>
                    @endforeach
                </div>

                @if($shows->hasPages())
                    <div class="mli-shows-pagination">
                        {{ $shows->links() }}
                    </div>
                @endif
            </div>
        </section>

        <div
            x-cloak
            x-show="selectedShow"
            x-transition:enter="mli-show-modal-backdrop-enter"
            x-transition:enter-start="mli-show-modal-backdrop-enter-start"
            x-transition:enter-end="mli-show-modal-backdrop-enter-end"
            x-transition:leave="mli-show-modal-backdrop-leave"
            x-transition:leave-start="mli-show-modal-backdrop-leave-start"
            x-transition:leave-end="mli-show-modal-backdrop-leave-end"
            class="mli-show-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="show-modal-title"
            @click.self="closeShow()"
        >
            <div
                class="mli-show-modal__panel"
                x-transition:enter="mli-show-modal-enter"
                x-transition:enter-start="mli-show-modal-enter-start"
                x-transition:enter-end="mli-show-modal-enter-end"
                x-transition:leave="mli-show-modal-leave"
                x-transition:leave-start="mli-show-modal-leave-start"
                x-transition:leave-end="mli-show-modal-leave-end"
            >
                <button
                    type="button"
                    class="mli-show-modal__close"
                    @click="closeShow()"
                    aria-label="{{ app()->getLocale() === 'ar' ? 'إغلاق' : 'Close' }}"
                >
                    ×
                </button>

                <div class="mli-show-modal__image">
                    <template x-if="selectedShow?.image">
                        <img :src="selectedShow.image" :alt="selectedShow.alt">
                    </template>
                </div>

                <div class="mli-show-modal__content">
                    <span class="mli-show-modal__category" x-text="selectedShow?.category"></span>
                    <h2 id="show-modal-title" x-text="selectedShow?.title"></h2>
                    <p x-text="selectedShow?.description || '{{ app()->getLocale() === 'ar' ? 'لا يوجد وصف متاح لهذا البرنامج.' : 'No description is available for this show.' }}'"></p>

                    <template x-if="selectedShow?.vimeo">
                        <a
                            :href="selectedShow.vimeo"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mli-show-modal__watch"
                        >
                            <span>{{ app()->getLocale() === 'ar' ? 'مشاهدة' : 'Watch' }}</span>
                            <b>↗</b>
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </main>
</x-layouts.public>