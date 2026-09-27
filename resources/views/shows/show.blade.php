<x-layouts.public
    :title="(app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en) . ' — Media Link International'"
    :meta-description="app()->getLocale() === 'ar'
        ? ($show->meta_description_ar ?? $show->description_ar)
        : ($show->meta_description_en ?? $show->description_en)">
    <main class="mli-show-page">

        <section class="mli-show-detail">
            <div class="container">

                <a href="{{ route('shows.index') }}" class="mli-show-detail__back">
                    <span aria-hidden="true">←</span>
                    <span>
                        {{ app()->getLocale() === 'ar' ? 'العودة إلى البرامج' : 'Back to shows' }}
                    </span>
                </a>

                <div class="mli-show-detail__grid">

                    <div class="mli-show-detail__visual">
                        @if($show->cover_image_url)
                        <div class="mli-show-detail__image">
                            <img
                                src="{{ $show->cover_image_url }}"
                                alt="{{ $show->cover_image_alt ?? (app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en) }}">
                        </div>
                        @endif
                    </div>

                    <div class="mli-show-detail__content">

                        @if($show->category)
                        <span class="mli-show-detail__category">
                            {{ app()->getLocale() === 'ar'
                                    ? $show->category->name_ar
                                    : $show->category->name_en }}
                        </span>
                        @endif

                        <h1>
                            {{ app()->getLocale() === 'ar'
                                ? $show->title_ar
                                : $show->title_en }}
                        </h1>

                        @if($show->description_en || $show->description_ar)
                        <div class="mli-show-detail__description">
                            {!! nl2br(e(
                            app()->getLocale() === 'ar'
                            ? $show->description_ar
                            : $show->description_en
                            )) !!}
                        </div>
                        @endif

                        <div class="mli-show-detail__meta">
                            @if($show->category)
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'الفئة' : 'Category' }}</span>
                                <strong>{{ app()->getLocale() === 'ar' ? $show->category->name_ar : $show->category->name_en }}</strong>
                            </div>
                            @endif

                            @if($show->is_new_release)
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</span>
                                <strong>{{ app()->getLocale() === 'ar' ? 'إصدار جديد' : 'New Release' }}</strong>
                            </div>
                            @endif

                            @if($show->published_at)
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'تاريخ النشر' : 'Published' }}</span>
                                <strong>{{ $show->published_at->locale(app()->getLocale())->translatedFormat('F Y') }}</strong>
                            </div>
                            @endif
                        </div>

                        @if($show->vimeo_url)
                        <a
                            href="{{ $show->vimeo_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mli-show-detail__watch">
                            <span>
                                {{ app()->getLocale() === 'ar'
                                        ? 'مشاهدة العرض'
                                        : 'Watch trailer' }}
                            </span>
                            <b aria-hidden="true">↗</b>
                        </a>
                        @endif

                    </div>

                </div>
            </div>
        </section>

        @if($relatedShows->isNotEmpty())
        <section class="mli-show-related">
            <div class="container">

                <div class="mli-show-related__heading">
                    <span>
                        {{ app()->getLocale() === 'ar'
                                ? 'المزيد من البرامج'
                                : 'MORE FROM THE LIBRARY' }}
                    </span>

                    <h2>
                        {{ app()->getLocale() === 'ar'
                                ? 'اكتشف المزيد.'
                                : 'Explore more.' }}
                    </h2>
                </div>

                <div class="mli-show-related__grid">
                    @foreach($relatedShows as $related)
                    <a
                        href="{{ route('shows.show', ['slug' => $related->slug]) }}"
                        class="mli-show-related__card">
                        <div class="mli-show-related__image">
                            @if($related->cover_image_url)
                            <img
                                src="{{ $related->cover_image_url }}"
                                alt="{{ $related->cover_image_alt ?? '' }}"
                                loading="lazy">
                            @endif
                        </div>

                        <div class="mli-show-related__copy">
                            <strong>
                                {{ app()->getLocale() === 'ar'
                                            ? $related->title_ar
                                            : $related->title_en }}
                            </strong>

                            @if($related->category)
                            <small>
                                {{ app()->getLocale() === 'ar'
                                                ? $related->category->name_ar
                                                : $related->category->name_en }}
                            </small>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>

            </div>
        </section>
        @endif

    </main>
</x-layouts.public>