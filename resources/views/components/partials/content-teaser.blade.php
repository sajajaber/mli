@props(['shows'])

<section id="content" class="mli-catalogue">
    <div class="mli-catalogue__intro container" data-reveal="up">
        <div>
            <span class="mli-catalogue__index">02 / THE LIBRARY</span>
            <h2>{{ app()->getLocale() === 'ar' ? 'تنوع. جودة. توافر.' : 'Variety. Quality. Availability.' }}</h2>
        </div>
        <p>{{ app()->getLocale() === 'ar' ? 'ثلاثة مبادئ تشكل مكتبتنا — تنوع واسع، جودة مختارة بعناية، ومحتوى متاح للشركاء والجمهور.' : 'Three principles shape the library — a wide range of titles, carefully selected quality, and content ready for partners and audiences.' }}</p>
    </div>

    <div class="mli-catalogue__rail" aria-label="{{ app()->getLocale() === 'ar' ? 'مكتبة البرامج' : 'Show library' }}">
        @foreach($shows->take(8) as $index => $show)
            <a href="{{ route('shows.index') }}" class="mli-catalogue-card" data-reveal="up" style="--reveal-delay: {{ min($index, 5) * 70 }}ms">
                <div class="mli-catalogue-card__image">
                    @if($show->cover_image_url)
                        <img src="{{ $show->cover_image_url }}" alt="{{ $show->cover_image_alt ?? '' }}" loading="lazy">
                    @endif
                    <span>0{{ $index + 1 }}</span>
                </div>
                <div class="mli-catalogue-card__info">
                    <h3>{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}</h3>
                    @if($show->category)
                        <small>{{ app()->getLocale() === 'ar' ? $show->category->name_ar : $show->category->name_en }}</small>
                    @endif
                </div>
                <b>↗</b>
            </a>
        @endforeach
    </div>

    <div class="container mli-catalogue__bottom">
        <span>08 / {{ app()->getLocale() === 'ar' ? 'برامج مختارة' : 'SELECTED TITLES' }}</span>
        <a href="{{ route('shows.index') }}">{{ app()->getLocale() === 'ar' ? 'شاهد المكتبة كاملة' : 'View the complete library' }} ↗</a>
    </div>
</section>