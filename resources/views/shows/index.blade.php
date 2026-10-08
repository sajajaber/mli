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
                            class="{{ !$categorySlug ? 'is-active' : '' }}">
                            {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}
                        </a>

                        @foreach($categories as $category)
                        <a
                            href="{{ route('shows.index', ['category' => $category->slug]) }}"
                            class="{{ $categorySlug === $category->slug ? 'is-active' : '' }}">
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
                    <a
                        href="{{ route('shows.show', ['slug' => $show->slug]) }}"
                        class="mli-show"
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

                                @if($show->category)
                                <small>{{ app()->getLocale() === 'ar' ? $show->category->name_ar : $show->category->name_en }}</small>
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

        requestController?.abort();
        requestController = new AbortController();

        page.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(nextUrl.href, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                signal: requestController.signal,
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

            page.replaceWith(nextPage);
            page = nextPage;

            if (push) {
                window.history.pushState({ mliShows: true }, '', nextUrl.href);
            }

            if (parsed.title) {
                document.title = parsed.title;
            }

            window.scrollTo({
                top: document.querySelector('.mli-shows-library')?.offsetTop ?? 0,
                behavior: 'smooth',
            });
        } catch (error) {
            if (error.name !== 'AbortError') {
                window.location.assign(nextUrl.href);
            }
        } finally {
            const currentPage = document.querySelector('.mli-shows-page');
            currentPage?.removeAttribute('aria-busy');
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