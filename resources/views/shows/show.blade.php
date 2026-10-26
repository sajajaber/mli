<x-layouts.public
    :title="(app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en) . ' — Media Link International'"
    :meta-description="app()->getLocale() === 'ar'
        ? ($show->meta_description_ar ?? $show->description_ar)
        : ($show->meta_description_en ?? $show->description_en)">
    @php
        $isArabic = app()->getLocale() === 'ar';
        $contentTypeLabel = match ($show->content_type) {
            'series' => $isArabic ? 'مسلسل' : 'Series',
            'documentary' => $isArabic ? 'وثائقي' : 'Documentary',
            'general' => $isArabic ? 'عام' : 'General',
            default => $isArabic ? 'برنامج' : 'Programme',
        };
    @endphp

    <main class="mli-show-page mli-show-detail-page">
        <section class="mli-show-detail">
            <div class="container">
                <a href="{{ route('shows.index') }}" class="mli-show-detail__back">
                    <span class="mli-show-detail__back-icon" aria-hidden="true">←</span>
                    <span>{{ $isArabic ? 'العودة إلى مكتبة البرامج' : 'Back to the library' }}</span>
                </a>

                <div class="mli-show-detail__grid">
                    <div class="mli-show-detail__visual">
                        <div class="mli-show-detail__image {{ $show->cover_image_url ? '' : 'mli-show-detail__image--empty' }}">
                            @if($show->cover_image_url)
                                <img
                                    src="{{ $show->cover_image_url }}"
                                    alt="{{ $show->cover_image_alt ?? ($isArabic ? $show->title_ar : $show->title_en) }}"
                                    fetchpriority="high">
                            @else
                                <div class="mli-show-detail__image-placeholder">
                                    <span>MLI</span>
                                    <small>{{ $isArabic ? 'صورة البرنامج غير متوفرة' : 'Artwork not available' }}</small>
                                </div>
                            @endif
                            <span class="mli-show-detail__image-frame" aria-hidden="true"></span>
                        </div>
                        <div class="mli-show-detail__visual-note">
                            <span>{{ $isArabic ? 'مكتبة المحتوى' : 'MLI / CONTENT LIBRARY' }}</span>
                            <span>{{ $isArabic ? 'تنوع. جودة. توافر.' : 'VARIETY. QUALITY. AVAILABILITY.' }}</span>
                        </div>
                    </div>

                    <div class="mli-show-detail__content">
                        <div class="mli-show-detail__eyebrow-row">
                            <span class="mli-show-detail__type">{{ $contentTypeLabel }}</span>
                            @if($show->is_new_release)
                                <span class="mli-show-detail__release">
                                    <span aria-hidden="true"></span>
                                    {{ $isArabic ? 'إصدار جديد' : 'New release' }}
                                </span>
                            @endif
                        </div>

                        @if($show->effective_categories->isNotEmpty())
                            <div class="mli-show-detail__taxonomy" aria-label="{{ $isArabic ? 'الفئات' : 'Categories' }}">
                                @foreach($show->effective_categories as $category)
                                    <a
                                        href="{{ route('shows.index', ['category' => $category->slug]) }}"
                                        class="mli-show-detail__taxonomy-item">
                                        {{ $isArabic ? $category->name_ar : $category->name_en }}
                                    </a>
                                @endforeach
                                @if($show->content_type === 'series' && $show->genres->isNotEmpty())
                                    <span class="mli-show-detail__taxonomy-divider" aria-hidden="true"></span>
                                    @foreach($show->genres as $genre)
                                        <span class="mli-show-detail__taxonomy-item mli-show-detail__taxonomy-item--genre">
                                            {{ $isArabic ? $genre->name_ar : $genre->name_en }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        @endif

                        <h1>{{ $isArabic ? $show->title_ar : $show->title_en }}</h1>

                        @if($show->description_en || $show->description_ar)
                            <div class="mli-show-detail__description">
                                {!! nl2br(e($isArabic ? ($show->description_ar ?: $show->description_en) : ($show->description_en ?: $show->description_ar))) !!}
                            </div>
                        @endif

                        <div class="mli-show-detail__actions">
                            @if($show->watch_url)
                                <a href="{{ $show->watch_url }}" target="_blank" rel="noopener noreferrer" class="mli-show-detail__action mli-show-detail__action--primary">
                                    <span>{{ $isArabic ? 'شاهد هنا' : 'Watch here' }}</span>
                                    <b aria-hidden="true">↗</b>
                                </a>
                            @endif

                            @if($show->vimeo_url)
                                <a href="{{ $show->vimeo_url }}" target="_blank" rel="noopener noreferrer" class="mli-show-detail__action mli-show-detail__action--secondary">
                                    <span>{{ $isArabic ? 'مشاهدة العرض الدعائي' : 'Watch trailer' }}</span>
                                    <b aria-hidden="true">↗</b>
                                </a>
                            @endif

                            <a
                                href="{{ route('contact.create', ['show' => $show->slug]) }}"
                                class="mli-show-detail__action mli-show-detail__action--contact"
                                aria-label="{{ $isArabic ? 'تواصل معنا حول هذا البرنامج' : 'Contact us about this show' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="14" rx="1.5"></rect>
                                    <path d="m3.5 6.5 8.5 7 8.5-7"></path>
                                </svg>
                                <span>{{ $isArabic ? 'تواصل معنا' : 'Contact us' }}</span>
                            </a>
                        </div>

                        @if($show->origin || $show->country_of_origin || $show->broadcast_format || $show->production_year || $show->season_number || $show->episode_count || !empty($show->languages) || !empty($show->subtitles) || !empty($show->availability) || !empty($show->service_types) || !empty($show->cast) || !empty($show->directors))
                        <section class="mli-show-detail__specs" aria-label="{{ $isArabic ? 'تفاصيل البرنامج' : 'Programme details' }}">
                            <div class="mli-show-detail__specs-heading">
                                <span>{{ $isArabic ? 'التفاصيل' : 'AT A GLANCE' }}</span>
                                <span aria-hidden="true"></span>
                            </div>
                            <div class="mli-show-detail__meta">
                                @if($show->content_type === 'documentary' && $show->origin)
                                    <div class="mli-show-detail__meta-item">
                                        <span>{{ $isArabic ? 'مصدر المحتوى / الحقوق' : 'Content / Rights Origin' }}</span>
                                        <strong>{{ $show->origin }}</strong>
                                    </div>
                                @endif

                                @if($show->content_type === 'documentary' && $show->country_of_origin)
                                    <div class="mli-show-detail__meta-item">
                                        <span>{{ $isArabic ? 'بلد المنشأ' : 'Country of Origin' }}</span>
                                        <strong>{{ $show->country_of_origin }}</strong>
                                    </div>
                                @endif

                                @if($show->content_type === 'documentary' && $show->broadcast_format)
                                    <div class="mli-show-detail__meta-item">
                                        <span>{{ $isArabic ? 'صيغة البث' : 'Broadcast Format' }}</span>
                                        <strong>{{ $show->broadcast_format }}</strong>
                                    </div>
                                @endif

                                @if($show->production_year)
                                    <div class="mli-show-detail__meta-item">
                                        <span>{{ $isArabic ? 'سنة الإنتاج' : 'Year of Production' }}</span>
                                        <strong>{{ $show->production_year }}</strong>
                                    </div>
                                @endif

                                @if($show->season_number)
                                    <div class="mli-show-detail__meta-item">
                                        <span>{{ $isArabic ? 'الموسم' : 'Season' }}</span>
                                        <strong>{{ $show->season_number }}</strong>
                                    </div>
                                @endif

                                @if($show->episode_count)
                                    <div class="mli-show-detail__meta-item">
                                        <span>{{ $isArabic ? 'عدد الحلقات' : 'Episodes' }}</span>
                                        <strong>{{ $show->episode_count }}</strong>
                                    </div>
                                @endif

                                @if(is_array($show->languages) && count($show->languages))
                                    <div class="mli-show-detail__meta-item">
                                        <span>{{ $isArabic ? 'اللغات' : 'Languages' }}</span>
                                        <strong>{{ implode(' · ', $show->languages) }}</strong>
                                    </div>
                                @endif

                                @if(is_array($show->subtitles) && count($show->subtitles))
                                    <div class="mli-show-detail__meta-item">
                                        <span>{{ $isArabic ? 'الترجمة' : 'Subtitles' }}</span>
                                        <strong>{{ implode(' · ', $show->subtitles) }}</strong>
                                    </div>
                                @endif

                                @if(is_array($show->availability) && count($show->availability))
                                    <div class="mli-show-detail__meta-item">
                                        <span>{{ $isArabic ? 'التوفّر' : 'Availability' }}</span>
                                        <strong>{{ implode(' · ', $show->availability) }}</strong>
                                    </div>
                                @endif

                                @if($show->content_type === 'series' && is_array($show->service_types) && count($show->service_types))
                                    <div class="mli-show-detail__meta-item">
                                        <span>{{ $isArabic ? 'دور MLI' : 'MLI Involvement' }}</span>
                                        <strong>{{ collect($show->service_types)->map(fn ($type) => match ($type) {
                                            'production' => $isArabic ? 'إنتاج' : 'Production',
                                            'distribution' => $isArabic ? 'توزيع' : 'Distribution',
                                            'collaboration' => $isArabic ? 'تعاون' : 'Collaboration',
                                            default => $type,
                                        })->implode(' · ') }}</strong>
                                    </div>
                                @endif

                                @if($show->content_type === 'series' && is_array($show->cast) && count($show->cast))
                                    <div class="mli-show-detail__meta-item">
                                        <span>{{ $isArabic ? 'طاقم التمثيل' : 'Cast' }}</span>
                                        <strong>{{ implode(' · ', $show->cast) }}</strong>
                                    </div>
                                @endif

                                @if($show->content_type === 'series' && is_array($show->directors) && count($show->directors))
                                    <div class="mli-show-detail__meta-item">
                                        <span>{{ $isArabic ? 'المخرجون' : 'Director(s)' }}</span>
                                        <strong>{{ implode(' · ', $show->directors) }}</strong>
                                    </div>
                                @endif
                            </div>
                        </section>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        @if($relatedShows->isNotEmpty())
            <section class="mli-show-related">
                <div class="container">
                    <div class="mli-show-related__heading">
                        <div class="mli-show-related__heading-copy">
                            <span>{{ $isArabic ? 'المزيد من البرامج' : 'MORE FROM THE LIBRARY' }}</span>
                            <h2>{{ $isArabic ? 'اكتشف المزيد.' : 'Explore more.' }}</h2>
                        </div>
                        <p>{{ $isArabic ? 'اكتشف عناوين أخرى من مكتبتنا.' : 'Discover more titles selected from the MLI library.' }}</p>
                    </div>

                    <div class="mli-show-related__grid">
                        @foreach($relatedShows as $related)
                            <a href="{{ route('shows.show', ['slug' => $related->slug]) }}" class="mli-show-related__card">
                                <div class="mli-show-related__image">
                                    @if($related->cover_image_url)
                                        <img
                                            src="{{ $related->cover_image_url }}"
                                            alt="{{ $related->cover_image_alt ?? ($isArabic ? $related->title_ar : $related->title_en) }}"
                                            loading="lazy">
                                    @else
                                        <span class="mli-show-related__placeholder">MLI</span>
                                    @endif
                                    <span class="mli-show-related__wash" aria-hidden="true"></span>
                                    <span class="mli-show-related__arrow" aria-hidden="true">↗</span>
                                </div>
                                <div class="mli-show-related__copy">
                                    <strong>{{ $isArabic ? $related->title_ar : $related->title_en }}</strong>
                                    @if($related->effective_categories->isNotEmpty())
                                        <small>{{ $related->effective_categories->pluck('name')->implode(' · ') }}</small>
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
