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

                        @if($show->effective_categories->isNotEmpty())
                        <span class="mli-show-detail__category">
                            {{ $show->effective_categories->pluck('name')->implode(' · ') }}
                        </span>
                        @endif

                        <div class="flex items-start justify-between gap-4">
                            <h1 class="min-w-0 flex-1">
                                {{ app()->getLocale() === 'ar'
                                    ? $show->title_ar
                                    : $show->title_en }}
                            </h1>

                            <a
                                href="{{ route('contact.create', ['show' => $show->slug]) }}"
                                class="group relative mt-1 inline-flex h-11 w-11 shrink-0 items-center justify-center border border-[#c5a46d] text-[#c5a46d] transition-colors duration-200 hover:bg-[#c5a46d] hover:text-[#2f4156] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#c5a46d]"
                                aria-label="{{ app()->getLocale() === 'ar' ? 'تواصل معنا حول هذا البرنامج' : 'Contact us about this show' }}"
                                title="{{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact us' }}">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="21"
                                    height="21"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="14" rx="1.5"></rect>
                                    <path d="m3.5 6.5 8.5 7 8.5-7"></path>
                                </svg>

                                <span
                                    class="pointer-events-none absolute end-0 top-full z-20 mt-2 translate-y-1 whitespace-nowrap bg-[#2f4156] px-3 py-2 text-xs font-medium text-white opacity-0 shadow-lg transition duration-200 group-hover:translate-y-0 group-hover:opacity-100 group-focus-visible:translate-y-0 group-focus-visible:opacity-100">
                                    {{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact us' }}
                                </span>
                            </a>
                        </div>

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
                            @if($show->effective_categories->isNotEmpty())
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'الفئات' : 'Categories' }}</span>
                                <strong>{{ $show->effective_categories->pluck('name')->implode(' · ') }}</strong>
                            </div>
                            @endif

                            @if($show->genres->isNotEmpty())
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'الأنواع' : 'Genres' }}</span>
                                <strong>{{ $show->genres->pluck('name')->implode(' · ') }}</strong>
                            </div>
                            @endif

                            @if($show->content_type)
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'نوع المحتوى' : 'Content Type' }}</span>
                                <strong>{{ $show->content_type === 'series'
                                    ? (app()->getLocale() === 'ar' ? 'مسلسل' : 'Series')
                                    : (app()->getLocale() === 'ar' ? 'وثائقي' : 'Documentary') }}</strong>
                            </div>
                            @endif

                            @if($show->content_type === 'documentary' && $show->origin)
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'مصدر المحتوى / الحقوق' : 'Content / Rights Origin' }}</span>
                                <strong>{{ $show->origin }}</strong>
                            </div>
                            @endif

                            @if($show->content_type === 'documentary' && $show->country_of_origin)
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'بلد المنشأ' : 'Country of Origin' }}</span>
                                <strong>{{ $show->country_of_origin }}</strong>
                            </div>
                            @endif

                            @if($show->content_type === 'documentary' && $show->broadcast_format)
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'صيغة البث' : 'Broadcast Format' }}</span>
                                <strong>{{ $show->broadcast_format }}</strong>
                            </div>
                            @endif

                            @if($show->production_year)
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'سنة الإنتاج' : 'Year of Production' }}</span>
                                <strong>{{ $show->production_year }}</strong>
                            </div>
                            @endif

                            @if($show->season_number)
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'الموسم' : 'Season' }}</span>
                                <strong>{{ $show->season_number }}</strong>
                            </div>
                            @endif

                            @if($show->episode_count)
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'عدد الحلقات' : 'Number of Episodes' }}</span>
                                <strong>{{ $show->episode_count }}</strong>
                            </div>
                            @endif

                            @if(is_array($show->languages) && count($show->languages))
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'اللغات' : 'Languages' }}</span>
                                <strong>{{ implode(' · ', $show->languages) }}</strong>
                            </div>
                            @endif

                            @if(is_array($show->subtitles) && count($show->subtitles))
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'الترجمة' : 'Subtitles' }}</span>
                                <strong>{{ implode(' · ', $show->subtitles) }}</strong>
                            </div>
                            @endif

                            @if(is_array($show->availability) && count($show->availability))
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'التوفّر' : 'Availability' }}</span>
                                <strong>{{ implode(' · ', $show->availability) }}</strong>
                            </div>
                            @endif

                            @if($show->content_type === 'series' && is_array($show->service_types) && count($show->service_types))
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'دور MLI' : 'MLI Involvement' }}</span>
                                <strong>{{ collect($show->service_types)->map(fn ($type) => match ($type) {
                                    'production' => app()->getLocale() === 'ar' ? 'إنتاج' : 'Production',
                                    'distribution' => app()->getLocale() === 'ar' ? 'توزيع' : 'Distribution',
                                    'collaboration' => app()->getLocale() === 'ar' ? 'تعاون' : 'Collaboration',
                                    default => $type,
                                })->implode(' · ') }}</strong>
                            </div>
                            @endif

                            @if($show->content_type === 'series' && is_array($show->cast) && count($show->cast))
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'طاقم التمثيل' : 'Cast' }}</span>
                                <strong>{{ implode(' · ', $show->cast) }}</strong>
                            </div>
                            @endif

                            @if($show->content_type === 'series' && is_array($show->directors) && count($show->directors))
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'المخرجون' : 'Director(s)' }}</span>
                                <strong>{{ implode(' · ', $show->directors) }}</strong>
                            </div>
                            @endif

                            @if($show->is_new_release)
                            <div class="mli-show-detail__meta-item">
                                <span>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</span>
                                <strong>{{ app()->getLocale() === 'ar' ? 'إصدار جديد' : 'New Release' }}</strong>
                            </div>
                            @endif

                        </div>

                        @if($show->watch_url)
                        <a
                            href="{{ $show->watch_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mli-show-detail__watch">
                            <span>
                                {{ app()->getLocale() === 'ar' ? 'شاهد هنا' : 'Watch here' }}
                            </span>
                            <b aria-hidden="true">↗</b>
                        </a>
                        @endif

                        @if($show->vimeo_url)
                        <a
                            href="{{ $show->vimeo_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mli-show-detail__watch">
                            <span>
                                {{ app()->getLocale() === 'ar' ? 'مشاهدة العرض الدعائي' : 'Watch trailer' }}
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

                            @if($related->categories->isNotEmpty())
                            <small>
                                {{ $related->categories->pluck('name')->implode(' · ') }}
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
