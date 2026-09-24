@props(['releases'])

<section id="new-releases" class="mli-new-releases">
    <div class="container">
        <div class="mli-new-releases__heading" data-reveal="up">
            <div>
                <span class="mli-new-releases__index">02 / NEW RELEASES</span>
                <h2>{{ app()->getLocale() === 'ar' ? 'إصدارات جديدة' : 'New Releases' }}</h2>
            </div>
            <span class="mli-new-releases__count">{{ $releases->count() }}</span>
        </div>

        @if($releases->isNotEmpty())
            <div class="mli-new-releases__grid">
                @foreach($releases as $index => $release)
                    <a href="{{ route('shows.index') }}"
                       class="mli-release-card"
                       data-reveal="up"
                       style="--reveal-delay: {{ min($index, 5) * 70 }}ms">
                        <div class="mli-release-card__image">
                            @if($release->cover_image_url)
                                <img src="{{ $release->cover_image_url }}"
                                     alt="{{ $release->cover_image_alt ?? '' }}"
                                     loading="lazy">
                            @endif
                        </div>
                        <div class="mli-release-card__body">
                            <div>
                                <h3>{{ app()->getLocale() === 'ar' ? $release->title_ar : $release->title_en }}</h3>
                                @if($release->category)
                                    <small>{{ app()->getLocale() === 'ar' ? $release->category->name_ar : $release->category->name_en }}</small>
                                @endif
                            </div>
                            <b>↗</b>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="mli-content-empty" data-reveal="up">
                {{ app()->getLocale() === 'ar' ? 'لا توجد إصدارات جديدة حاليًا.' : 'No new releases yet.' }}
            </div>
        @endif
    </div>
</section>
