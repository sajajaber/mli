<x-layouts.public :title="'Shows — Media Link International'">
    <section class="library-hero">
        <div class="container">
            <div class="library-hero__top" data-reveal="up">
                <p class="eyebrow">{{ app()->getLocale() === 'ar' ? 'المكتبة' : 'The library' }}</p>
                <span class="library-hero__code">MLI / 01</span>
            </div>

            <div class="library-hero__content" data-reveal="up">
                <h1>
                    {{ app()->getLocale() === 'ar' ? 'كل أعمالنا.' : 'Our complete library.' }}
                </h1>
                <p>
                    {{ app()->getLocale() === 'ar'
                        ? 'اكتشف مجموعة البرامج والمحتوى الذي تنتجه وتوزعه MLI.'
                        : 'Explore the shows and content produced and distributed by MLI.' }}
                </p>
            </div>
        </div>
    </section>

    <section class="library-collection">
        <div class="container">
            <div class="library-collection__head" data-reveal="up">
                <div>
                    <p class="library-collection__eyebrow">
                        {{ app()->getLocale() === 'ar' ? 'المجموعة' : 'The collection' }}
                    </p>
                    <h2>{{ app()->getLocale() === 'ar' ? 'برامجنا.' : 'Our shows.' }}</h2>
                </div>

                <div class="library-collection__count">
                    <strong>{{ $shows->total() }}</strong>
                    <span>{{ app()->getLocale() === 'ar' ? 'عنواناً' : 'titles' }}</span>
                </div>
            </div>

            <div class="show-grid">
                @foreach($shows as $index => $show)
                    <a href="{{ route('shows.index') }}"
                       class="show-card"
                       data-reveal="up"
                       style="--reveal-delay: {{ min($index % 6, 5) * 60 }}ms">
                        <div class="show-card__image">
                            @if($show->cover_image_url)
                                <img
                                    src="{{ $show->cover_image_url }}"
                                    alt="{{ $show->cover_image_alt ?? '' }}"
                                    loading="lazy"
                                >
                            @endif

                            <span class="show-card__shade"></span>
                            <span class="show-card__number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="show-card__arrow" aria-hidden="true">↗</span>

                            <span class="show-card__overlay">
                                <strong>{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}</strong>
                                @if($show->category)
                                    <small>{{ app()->getLocale() === 'ar' ? $show->category->name_ar : $show->category->name_en }}</small>
                                @endif
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($shows->hasPages())
                <div class="library-pagination">{{ $shows->links() }}</div>
            @endif
        </div>
    </section>
</x-layouts.public>