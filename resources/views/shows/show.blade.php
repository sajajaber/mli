<x-layouts.public
    :title="(app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en) . ' — Media Link International'"
    :meta-description="app()->getLocale() === 'ar'
        ? ($show->meta_description_ar ?? $show->description_ar)
        : ($show->meta_description_en ?? $show->description_en)">
    @php
        $isArabic = app()->getLocale() === 'ar';
        $title = $isArabic ? ($show->title_ar ?: $show->title_en) : $show->title_en;
        $contentTypeLabel = match ($show->content_type) {
            'series' => $isArabic ? 'مسلسل' : 'Series',
            'documentary' => $isArabic ? 'وثائقي' : 'Documentary',
            'general' => $isArabic ? 'عام' : 'General',
            default => $isArabic ? 'برنامج' : 'Programme',
        };
        $hasLanguages = is_array($show->languages) && count($show->languages) > 0;
        $hasSubtitles = is_array($show->subtitles) && count($show->subtitles) > 0;
        $hasAvailability = is_array($show->availability) && count($show->availability) > 0;
        $hasServiceTypes = is_array($show->service_types) && count($show->service_types) > 0;
        $hasDirectors = is_array($show->directors) && count($show->directors) > 0;
        $hasDocumentaryDetails = $show->content_type === 'documentary'
            && ($show->origin || $show->country_of_origin || $show->broadcast_format);
        $hasSeriesDetails = $show->content_type === 'series' && ($hasServiceTypes || $hasDirectors);
        $hasDetails = $hasDocumentaryDetails || $hasSeriesDetails || $hasLanguages || $hasSubtitles || $hasAvailability;
    @endphp

    <div class="mli-program-page">
        <div class="mli-program-container container">
            <div class="mli-program-page-topbar">
                <a href="{{ route('shows.index') }}" class="mli-program-back">
                    <span class="mli-program-back__arrow" aria-hidden="true">{{ $isArabic ? '→' : '←' }}</span>
                    <span>{{ $isArabic ? 'العودة إلى مكتبة البرامج' : 'Back to the library' }}</span>
                </a>

                <a href="{{ route('contact.create', ['show' => $show->slug]) }}" class="mli-program-action mli-program-action--contact mli-program-top-contact">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="14" rx="1.5"></rect>
                        <path d="m3.5 6.5 8.5 7 8.5-7"></path>
                    </svg>
                    <span>{{ $isArabic ? 'تواصل معنا' : 'Contact us' }}</span>
                </a>
            </div>

            <section class="mli-program-overview" aria-labelledby="mli-program-title">
                <div class="mli-program-poster-column">
                    <div class="mli-program-poster">
                        @if($show->cover_image_url)
                            <img
                                src="{{ $show->cover_image_url }}"
                                alt="{{ $show->cover_image_alt ?: $title }}"
                                fetchpriority="high">
                        @else
                            <div class="mli-program-poster__placeholder">
                                <span class="mli-program-poster__monogram">MLI</span>
                                <span>{{ $isArabic ? 'صورة البرنامج غير متوفرة' : 'Artwork coming soon' }}</span>
                            </div>
                        @endif
                        <span class="mli-program-poster__corner" aria-hidden="true"></span>
                    </div>
                    <div class="mli-program-poster-caption">
                        <span>{{ $isArabic ? 'مكتبة المحتوى' : 'MLI · CONTENT LIBRARY' }}</span>
                        <span>{{ $isArabic ? 'تنوع · جودة · توافر' : 'VARIETY · QUALITY · AVAILABILITY' }}</span>
                    </div>
                </div>

                <div class="mli-program-copy">
                    <div class="mli-program-eyebrows">
                        <span class="mli-program-type">{{ $contentTypeLabel }}</span>
                        @if($show->is_new_release)
                            <span class="mli-program-release">
                                <span aria-hidden="true"></span>
                                {{ $isArabic ? 'إصدار جديد' : 'New release' }}
                            </span>
                        @endif
                    </div>

                    <p class="mli-program-kicker">{{ $isArabic ? 'اكتشف البرنامج' : 'DISCOVER THE PROGRAMME' }}</p>
                    <h1 id="mli-program-title" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">{{ $title }}</h1>

                    @if($show->effective_categories->isNotEmpty() || ($show->content_type === 'series' && $show->genres->isNotEmpty()))
                        <div class="mli-program-taxonomy">
                            @foreach($show->effective_categories as $category)
                                <a href="{{ route('shows.index', ['category' => $category->slug]) }}">
                                    {{ $isArabic ? $category->name_ar : $category->name_en }}
                                </a>
                            @endforeach
                            @if($show->content_type === 'series' && $show->genres->isNotEmpty())
                                <span class="mli-program-taxonomy__divider" aria-hidden="true"></span>
                                @foreach($show->genres as $genre)
                                    <span class="mli-program-taxonomy__genre">
                                        {{ $isArabic ? $genre->name_ar : $genre->name_en }}
                                    </span>
                                @endforeach
                            @endif
                        </div>
                    @endif

                    @if($show->description_en || $show->description_ar)
                        <div class="mli-program-description" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
                            {!! nl2br(e($isArabic
                                ? ($show->description_ar ?: $show->description_en)
                                : ($show->description_en ?: $show->description_ar))) !!}
                        </div>
                    @endif

                    @if($show->production_year || $show->season_number || $show->episode_count)
                        <div class="mli-program-facts">
                            @if($show->production_year)
                                <div class="mli-program-fact">
                                    <span>{{ $isArabic ? 'سنة الإنتاج' : 'PRODUCTION YEAR' }}</span>
                                    <strong>{{ $show->production_year }}</strong>
                                </div>
                            @endif
                            @if($show->season_number)
                                <div class="mli-program-fact">
                                    <span>{{ $isArabic ? 'الموسم' : 'SEASON' }}</span>
                                    <strong>{{ $show->season_number }}</strong>
                                </div>
                            @endif
                            @if($show->episode_count)
                                <div class="mli-program-fact">
                                    <span>{{ $isArabic ? 'عدد الحلقات' : 'EPISODES' }}</span>
                                    <strong>{{ $show->episode_count }}</strong>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="mli-program-actions">
                        @if($show->watch_url)
                            <a href="{{ $show->watch_url }}" target="_blank" rel="noopener noreferrer" class="mli-program-action mli-program-action--primary">
                                <span>{{ $isArabic ? 'شاهد الآن' : 'Watch here' }}</span>
                                <span aria-hidden="true">↗</span>
                            </a>
                        @endif
                        @if($show->vimeo_url)
                            <a href="{{ $show->vimeo_url }}" target="_blank" rel="noopener noreferrer" class="mli-program-action mli-program-action--secondary">
                                <span>{{ $isArabic ? 'مشاهدة العرض الدعائي' : 'Watch trailer' }}</span>
                                <span aria-hidden="true">↗</span>
                            </a>
                        @endif
                    </div>
                </div>
            </section>

            @if($hasDetails)
                <section class="mli-program-details" aria-labelledby="mli-program-details-title">
                    <div class="mli-program-section-heading">
                        <div>
                            <span>{{ $isArabic ? 'معلومات البرنامج' : 'PROGRAMME INFORMATION' }}</span>
                            <h2 id="mli-program-details-title">{{ $isArabic ? 'التفاصيل' : 'At a glance' }}</h2>
                        </div>
                        <span class="mli-program-section-heading__rule" aria-hidden="true"></span>
                    </div>

                    <dl class="mli-program-details__grid">
                        @if($show->content_type === 'documentary' && $show->origin)
                            <div class="mli-program-detail">
                                <dt>{{ $isArabic ? 'مصدر المحتوى / الحقوق' : 'Content / rights origin' }}</dt>
                                <dd>{{ $show->origin }}</dd>
                            </div>
                        @endif
                        @if($show->content_type === 'documentary' && $show->country_of_origin)
                            <div class="mli-program-detail">
                                <dt>{{ $isArabic ? 'بلد المنشأ' : 'Country of origin' }}</dt>
                                <dd>{{ $show->country_of_origin }}</dd>
                            </div>
                        @endif
                        @if($show->content_type === 'documentary' && $show->broadcast_format)
                            <div class="mli-program-detail">
                                <dt>{{ $isArabic ? 'صيغة البث' : 'Broadcast format' }}</dt>
                                <dd>{{ $show->broadcast_format }}</dd>
                            </div>
                        @endif
                        @if($hasLanguages)
                            <div class="mli-program-detail">
                                <dt>{{ $isArabic ? 'اللغات' : 'Languages' }}</dt>
                                <dd>{{ implode(' · ', $show->languages) }}</dd>
                            </div>
                        @endif
                        @if($hasSubtitles)
                            <div class="mli-program-detail">
                                <dt>{{ $isArabic ? 'الترجمة' : 'Subtitles' }}</dt>
                                <dd>{{ implode(' · ', $show->subtitles) }}</dd>
                            </div>
                        @endif
                        @if($hasAvailability)
                            <div class="mli-program-detail">
                                <dt>{{ $isArabic ? 'التوفّر' : 'Availability' }}</dt>
                                <dd>{{ implode(' · ', $show->availability) }}</dd>
                            </div>
                        @endif
                        @if($show->content_type === 'series' && $hasServiceTypes)
                            <div class="mli-program-detail">
                                <dt>{{ $isArabic ? 'دور MLI' : 'MLI involvement' }}</dt>
                                <dd>{{ collect($show->service_types)->map(fn ($type) => match ($type) {
                                    'production' => $isArabic ? 'إنتاج' : 'Production',
                                    'distribution' => $isArabic ? 'توزيع' : 'Distribution',
                                    'collaboration' => $isArabic ? 'تعاون' : 'Collaboration',
                                    default => $type,
                                })->implode(' · ') }}</dd>
                            </div>
                        @endif
                        @if($show->content_type === 'series' && $hasDirectors)
                            <div class="mli-program-detail">
                                <dt>{{ $isArabic ? 'المخرجون' : 'Director(s)' }}</dt>
                                <dd>{{ implode(' · ', $show->directors) }}</dd>
                            </div>
                        @endif
                    </dl>
                </section>
            @endif
        </div>

        @if($show->content_type === 'series' && $show->castMembers->isNotEmpty())
            <section class="mli-program-cast" aria-labelledby="mli-program-cast-title">
                <div class="container">
                    <div class="mli-program-section-heading">
                        <div>
                            <span>{{ $isArabic ? 'وجوه الحكاية' : 'MEET THE CAST' }}</span>
                            <h2 id="mli-program-cast-title">{{ $isArabic ? 'طاقم التمثيل' : 'The cast' }}</h2>
                        </div>
                        <span class="mli-program-section-heading__rule" aria-hidden="true"></span>
                    </div>

                    <div class="mli-program-cast__grid">
                        @foreach($show->castMembers as $castMember)
                            @php
                                $actorName = $isArabic
                                    ? ($castMember->actor_name_ar ?: $castMember->actor_name_en)
                                    : $castMember->actor_name_en;
                                $characterName = $isArabic
                                    ? ($castMember->character_name_ar ?: $castMember->character_name_en)
                                    : ($castMember->character_name_en ?: $castMember->character_name_ar);
                            @endphp
                            <article class="mli-program-cast__card">
                                <div class="mli-program-cast__portrait">
                                    @if($castMember->photo_url)
                                        <img src="{{ $castMember->photo_url }}" alt="{{ $actorName }}" loading="lazy">
                                    @else
                                        <div class="mli-program-cast__placeholder" role="img" aria-label="{{ $isArabic ? 'صورة الممثل غير متوفرة' : 'Actor photo unavailable' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                                                <circle cx="32" cy="22" r="11" />
                                                <path d="M10 56c1.6-12.5 9.7-19 22-19s20.4 6.5 22 19" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="mli-program-cast__copy">
                                    <h3>{{ $actorName }}</h3>
                                    @if(filled($characterName))
                                        <p>{{ $characterName }}</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @elseif($show->content_type === 'series' && is_array($show->cast) && count($show->cast))
            <section class="mli-program-legacy-cast">
                <div class="container">
                    <div class="mli-program-section-heading">
                        <div>
                            <span>{{ $isArabic ? 'وجوه الحكاية' : 'MEET THE CAST' }}</span>
                            <h2>{{ $isArabic ? 'طاقم التمثيل' : 'The cast' }}</h2>
                        </div>
                    </div>
                    <p class="mli-program-legacy-cast__names">{{ implode(' · ', $show->cast) }}</p>
                </div>
            </section>
        @endif

        @if($relatedShows->isNotEmpty())
            <section class="mli-program-related">
                <div class="container">
                    <div class="mli-program-section-heading">
                        <div>
                            <span>{{ $isArabic ? 'من مكتبة MLI' : 'MORE FROM THE LIBRARY' }}</span>
                            <h2>{{ $isArabic ? 'اكتشف المزيد.' : 'Explore more.' }}</h2>
                        </div>
                        <a class="mli-program-related__all" href="{{ route('shows.index') }}">
                            {{ $isArabic ? 'كل البرامج' : 'View all shows' }} <span aria-hidden="true">↗</span>
                        </a>
                    </div>

                    <div class="mli-program-related__grid">
                        @foreach($relatedShows as $related)
                            <a href="{{ route('shows.show', ['slug' => $related->slug]) }}" class="mli-program-related__card">
                                <div class="mli-program-related__image">
                                    @if($related->cover_image_url)
                                        <img src="{{ $related->cover_image_url }}" alt="{{ $related->cover_image_alt ?: ($isArabic ? $related->title_ar : $related->title_en) }}" loading="lazy">
                                    @else
                                        <span>MLI</span>
                                    @endif
                                    <span class="mli-program-related__wash" aria-hidden="true"></span>
                                    <span class="mli-program-related__arrow" aria-hidden="true">↗</span>
                                </div>
                                <div class="mli-program-related__copy">
                                    <strong>{{ $isArabic ? $related->title_ar : $related->title_en }}</strong>
                                    @if($related->effective_categories->isNotEmpty())
                                        <small>{{ $related->effective_categories->map(fn ($category) => $isArabic ? $category->name_ar : $category->name_en)->implode(' · ') }}</small>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>
</x-layouts.public>
