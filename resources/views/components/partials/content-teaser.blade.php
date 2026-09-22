@props(['shows'])

<section id="content" class="mli-work">
    <div class="container">
        <div class="mli-work__heading" data-reveal="up">
            <div>
                <p class="mli-label">02 / THE WORK</p>
                <h2>{{ app()->getLocale() === 'ar' ? 'برامجنا ليست بطاقات.' : 'The work is not a grid.' }}</h2>
            </div>
            <p>{{ app()->getLocale() === 'ar' ? 'كل برنامج هو قصة، وهوية، وجمهور.' : 'Every show is a story, an identity, and an audience.' }}</p>
        </div>

        <div class="mli-work__list">
            @foreach($shows->take(8) as $index => $show)
                <a href="{{ route('shows.index') }}" class="mli-work-item" data-reveal="up" style="--reveal-delay: {{ min($index, 5) * 60 }}ms">
                    <span class="mli-work-item__number">0{{ $index + 1 }}</span>
                    <div class="mli-work-item__image">
                        @if($show->cover_image_url)
                            <img src="{{ $show->cover_image_url }}" alt="{{ $show->cover_image_alt ?? '' }}" loading="lazy">
                        @endif
                    </div>
                    <div class="mli-work-item__title">
                        <h3>{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}</h3>
                        @if($show->category)
                            <span>{{ app()->getLocale() === 'ar' ? $show->category->name_ar : $show->category->name_en }}</span>
                        @endif
                    </div>
                    <span class="mli-work-item__arrow">↗</span>
                </a>
            @endforeach
        </div>

        <div class="mli-work__footer">
            <span>MLI / 08 PROGRAMS</span>
            <a href="{{ route('shows.index') }}">{{ app()->getLocale() === 'ar' ? 'شاهد المكتبة كاملة' : 'Enter the full library' }} ↗</a>
        </div>
    </div>
</section>