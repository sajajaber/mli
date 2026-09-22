@props(['shows'])

<section id="content" class="section section--light">
    <div class="container">
        <div class="section-heading" data-reveal="up">
            <div>
                <p class="eyebrow eyebrow--dark">{{ app()->getLocale() === 'ar' ? 'المكتبة' : 'The library' }}</p>
                <h2>{{ app()->getLocale() === 'ar' ? 'قصص تستحق المشاهدة.' : 'Stories worth watching.' }}</h2>
            </div>
            <a href="{{ route('shows.index') }}" class="text-link">
                {{ app()->getLocale() === 'ar' ? 'عرض كل البرامج' : 'View all shows' }} <span>↗</span>
            </a>
        </div>

        <div class="show-grid">
            @foreach($shows->take(8) as $index => $show)
                <a href="{{ route('shows.index') }}" class="show-card" data-reveal="up" style="--reveal-delay: {{ min($index, 5) * 70 }}ms">
                    <div class="show-card__image">
                        @if($show->cover_image_url)
                            <img src="{{ $show->cover_image_url }}" alt="{{ $show->cover_image_alt ?? '' }}" loading="lazy">
                        @endif
                        <span class="show-card__arrow">↗</span>
                    </div>
                    <div class="show-card__meta">
                        <h3>{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}</h3>
                        @if($show->category)
                            <span>{{ app()->getLocale() === 'ar' ? $show->category->name_ar : $show->category->name_en }}</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>