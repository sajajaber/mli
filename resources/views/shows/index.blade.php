<x-layouts.public :title="'Shows — Media Link International'">
    <main class="library-page">
        <section class="library-page__intro">
            <div class="container">
                <div class="library-page__topline" data-reveal="up">
                    <span>{{ app()->getLocale() === 'ar' ? 'مكتبة MLI' : 'MLI LIBRARY' }}</span>
                    <span>{{ str_pad($shows->total(), 2, '0', STR_PAD_LEFT) }} {{ app()->getLocale() === 'ar' ? 'عنواناً' : 'TITLES' }}</span>
                </div>

                <div class="library-page__heading" data-reveal="up">
                    <div>
                        <p class="library-page__kicker">
                            {{ app()->getLocale() === 'ar' ? 'البرامج' : 'Shows' }}
                        </p>
                        <h1>
                            {{ app()->getLocale() === 'ar' ? 'مجموعة MLI.' : 'The MLI collection.' }}
                        </h1>
                    </div>

                    <p class="library-page__description">
                        {{ app()->getLocale() === 'ar'
                            ? 'تصفح مجموعة البرامج والمحتوى الذي تنتجه وتوزعه MLI.'
                            : 'Browse the programs and content produced and distributed by MLI.' }}
                    </p>
                </div>
            </div>
        </section>

        <section class="library-page__collection">
            <div class="container">
                <div class="library-page__rule" aria-hidden="true"></div>

                <div class="library-page__grid">
                    @foreach($shows as $index => $show)
                        <a href="{{ route('shows.index') }}"
                           class="library-card"
                           data-reveal="up"
                           style="--reveal-delay: {{ min($index % 6, 5) * 70 }}ms">
                            <div class="library-card__media">
                                @if($show->cover_image_url)
                                    <img
                                        src="{{ $show->cover_image_url }}"
                                        alt="{{ $show->cover_image_alt ?? '' }}"
                                        loading="lazy"
                                    >
                                @endif

                                <span class="library-card__index">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>

                                <span class="library-card__arrow" aria-hidden="true">↗</span>
                            </div>

                            <div class="library-card__info">
                                <div>
                                    <h2>{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}</h2>

                                    @if($show->category)
                                        <span>
                                            {{ app()->getLocale() === 'ar' ? $show->category->name_ar : $show->category->name_en }}
                                        </span>
                                    @endif
                                </div>

                                <span class="library-card__line" aria-hidden="true"></span>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($shows->hasPages())
                    <div class="library-pagination">{{ $shows->links() }}</div>
                @endif
            </div>
        </section>
    </main>
</x-layouts.public>