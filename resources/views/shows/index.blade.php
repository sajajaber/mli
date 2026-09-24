<x-layouts.public :title="'Shows — Media Link International'">
    <main class="mli-shows-page">
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
                        {{ app()->getLocale() === 'ar' ? 'قصص تُشاهد.' : 'Stories worth watching.' }}
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
                <div class="mli-shows-library__bar">
                    <span>{{ app()->getLocale() === 'ar' ? 'جميع البرامج' : 'All shows' }}</span>
                    <span>{{ str_pad($shows->firstItem() ?? 0, 2, '0', STR_PAD_LEFT) }}—{{ str_pad($shows->lastItem() ?? 0, 2, '0', STR_PAD_LEFT) }}</span>
                </div>

                <div class="mli-shows-grid">
                    @foreach($shows as $index => $show)
                        <a
                            href="{{ route('shows.index') }}"
                            class="mli-show"
                            data-reveal="up"
                            style="--reveal-delay: {{ min($index % 6, 5) * 70 }}ms"
                        >
                            <div class="mli-show__image">
                                @if($show->cover_image_url)
                                    <img
                                        src="{{ $show->cover_image_url }}"
                                        alt="{{ $show->cover_image_alt ?? '' }}"
                                        loading="lazy"
                                    >
                                @endif

                                <span class="mli-show__number">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>

                                <span class="mli-show__open">↗</span>
                            </div>

                            <div class="mli-show__details">
                                <div>
                                    <h2>{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}</h2>

                                    @if($show->category)
                                        <p>{{ app()->getLocale() === 'ar' ? $show->category->name_ar : $show->category->name_en }}</p>
                                    @endif
                                </div>

                                <span class="mli-show__dash"></span>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($shows->hasPages())
                    <div class="mli-shows-pagination">
                        {{ $shows->links() }}
                    </div>
                @endif
            </div>
        </section>
    </main>
</x-layouts.public>