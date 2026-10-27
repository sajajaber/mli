<x-layouts.public :title="'Shows - Media Link International'">

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
                @php
                    $activeParentCategory = $categories->first(
                        fn ($category) => $category->slug === $categorySlug
                            || $category->children->contains(fn ($child) => $child->slug === $categorySlug)
                    );
                @endphp

                <div class="mli-shows-category-filters">
                    <div class="mli-shows-filters mli-shows-parent-filter-row">
                        <span class="mli-shows-filters__label">
                            {{ app()->getLocale() === 'ar' ? 'تصفية حسب النوع' : 'Filter by category' }}
                        </span>

                        <div class="mli-shows-filters__links mli-shows-category-bar mli-shows-parent-category-bar">
                            <a
                                href="{{ route('shows.index') }}"
                                class="mli-shows-category-all {{ !$categorySlug ? 'is-active' : '' }}">
                                {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}
                            </a>

                            @foreach($categories as $category)
                            <a
                                href="{{ route('shows.index', ['category' => $category->slug]) }}"
                                class="mli-shows-parent-category {{ $activeParentCategory?->id === $category->id ? 'is-active' : '' }}">
                                {{ app()->getLocale() === 'ar' ? $category->name_ar : $category->name_en }}
                            </a>
                            @endforeach
                        </div>
                    </div>

                    @if($activeParentCategory && $activeParentCategory->children->isNotEmpty())
                    <div class="mli-shows-subcategory-reveal">
                        <div class="mli-shows-filters mli-shows-subcategory-filter-row">
                            <div class="mli-shows-filters__links mli-shows-category-bar mli-shows-subcategory-bar">
                                @foreach($activeParentCategory->children as $subcategory)
                                <a
                                    href="{{ route('shows.index', ['category' => $subcategory->slug]) }}"
                                    class="mli-shows-subcategory {{ $categorySlug === $subcategory->slug ? 'is-active' : '' }}">
                                    {{ app()->getLocale() === 'ar' ? $subcategory->name_ar : $subcategory->name_en }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                @if($genres->isNotEmpty())
                <div class="mli-shows-filters">
                    <span class="mli-shows-filters__label">
                        {{ app()->getLocale() === 'ar' ? 'تصفية حسب النوع الفرعي' : 'Filter by genre' }}
                    </span>

                    <div class="mli-shows-filters__links">
                        <a
                            href="{{ route('shows.index', ['category' => $categorySlug]) }}"
                            class="{{ !$genreSlug ? 'is-active' : '' }}">
                            {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}
                        </a>

                        @foreach($genres as $genre)
                        <a
                            href="{{ route('shows.index', ['category' => $categorySlug, 'genre' => $genre->slug]) }}"
                            class="{{ $genreSlug === $genre->slug ? 'is-active' : '' }}">
                            {{ $genre->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="mli-shows-library__bar">
                    <span>{{ app()->getLocale() === 'ar' ? 'جميع البرامج' : 'All shows' }}</span>
                    <span>{{ $shows->count() }} / {{ $shows->total() }}</span>
                </div>

                <div class="mli-shows-grid">
                    @foreach($shows as $index => $show)
                    <a
                        href="{{ route('shows.show', ['slug' => $show->slug]) }}"
                        class="mli-show"
                        data-page-reveal="up"
                        style="--reveal-delay: {{ min($index % 8, 7) * 60 }}ms"
                        aria-label="{{ app()->getLocale() === 'ar' ? 'عرض ' : 'View ' }}{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}">
                        <span class="mli-show__image">
                            @if($show->cover_image_url)
                            <img
                                src="{{ $show->cover_image_url }}"
                                alt="{{ $show->cover_image_alt ?? '' }}"
                                loading="lazy">
                            @endif

                            <span class="mli-show__wash" aria-hidden="true"></span>

                            <span class="mli-show__copy">
                                <strong>{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}</strong>

                @if($show->effective_categories->isNotEmpty())
                                <small>{{ $show->effective_categories->pluck('name')->implode(' · ') }}</small>
                                @endif
                            </span>

                            <span class="mli-show__open" aria-hidden="true">↗</span>
                        </span>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    let page = document.querySelector('.mli-shows-page');

    if (!page) {
        return;
    }

    let requestController = null;

    const loadShows = async (url, push = true) => {
        const nextUrl = new URL(url, window.location.href);

        if (nextUrl.origin !== window.location.origin || nextUrl.pathname !== '/shows') {
            return;
        }

        // A repeated click on the exact same filter state should not refetch the page.
        if (push) {
            const normalizeUrl = (url) => {
                const normalized = new URL(url.href);
                normalized.hash = '';
                normalized.searchParams.sort();

                return normalized.href;
            };

            if (normalizeUrl(nextUrl) === normalizeUrl(new URL(window.location.href))) {
                return;
            }
        }

        requestController?.abort();
        const controller = new AbortController();
        requestController = controller;

        page.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(nextUrl.href, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                signal: controller.signal,
            });

            if (!response.ok) {
                throw new Error('Unable to load shows.');
            }

            const html = await response.text();
            const parsed = new DOMParser().parseFromString(html, 'text/html');
            const nextPage = parsed.querySelector('.mli-shows-page');

            if (!nextPage) {
                throw new Error('Shows content was not found.');
            }

            const currentSubcategoryRow = page.querySelector('.mli-shows-subcategory-reveal');
            const nextSubcategoryRow = nextPage.querySelector('.mli-shows-subcategory-reveal');

            // The row remains open across parent/child filter changes; don't replay its entrance.
            if (currentSubcategoryRow && nextSubcategoryRow) {
                nextSubcategoryRow.classList.add('is-visible');
            }

            // Wait for this row's own exit animation before removing it from the document.
            if (
                currentSubcategoryRow &&
                !nextSubcategoryRow &&
                !window.matchMedia('(prefers-reduced-motion: reduce)').matches
            ) {
                const waitForExit = new Promise((resolve) => {
                    let finished = false;

                    const onAnimationEnd = (event) => {
                        // Descendant animations bubble; only the row's own hide animation counts.
                        if (
                            event.target !== currentSubcategoryRow ||
                            event.animationName !== 'mli-subcategory-row-hide'
                        ) {
                            return;
                        }

                        finish();
                    };

                    const finish = () => {
                        if (finished) {
                            return;
                        }

                        finished = true;
                        window.clearTimeout(timeout);
                        currentSubcategoryRow.removeEventListener('animationend', onAnimationEnd);
                        controller.signal.removeEventListener('abort', finish);

                        if (controller.signal.aborted) {
                            currentSubcategoryRow.classList.remove('is-leaving');
                        }

                        resolve();
                    };

                    const timeout = window.setTimeout(finish, 360);
                    currentSubcategoryRow.addEventListener('animationend', onAnimationEnd);
                    controller.signal.addEventListener('abort', finish, { once: true });
                    currentSubcategoryRow.classList.add('is-leaving');
                });

                await waitForExit;

                if (controller.signal.aborted) {
                    return;
                }
            }

            const scrollPosition = window.scrollY;

            page.replaceWith(nextPage);
            page = nextPage;

            requestAnimationFrame(() => {
                window.scrollTo(0, scrollPosition);
            });

            if (push) {
                window.history.pushState({ mliShows: true }, '', nextUrl.href);
            }

            if (parsed.title) {
                document.title = parsed.title;
            }

        } catch (error) {
            if (error.name !== 'AbortError') {
                window.location.assign(nextUrl.href);
            }
        } finally {
            if (requestController === controller) {
                const currentPage = document.querySelector('.mli-shows-page');
                currentPage?.removeAttribute('aria-busy');
            }
        }
    };

    document.addEventListener('click', (event) => {
        const link = event.target.closest(
            '.mli-shows-filters__links a, .mli-shows-pagination a'
        );

        if (!link || link.target === '_blank') {
            return;
        }

        if (
            event.button !== 0 ||
            event.metaKey ||
            event.ctrlKey ||
            event.shiftKey ||
            event.altKey
        ) {
            return;
        }

        const linkUrl = new URL(link.href, window.location.href);

        if (
            linkUrl.origin !== window.location.origin ||
            linkUrl.pathname !== '/shows'
        ) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        void loadShows(linkUrl.href);
    });

    window.addEventListener('popstate', () => {
        void loadShows(window.location.href, false);
    });
});
</script>


</x-layouts.public>