<x-layouts.public :title="'Shows — Media Link International'">
    <section class="library-hero">
        <div class="container" data-reveal="up">
            <p class="eyebrow eyebrow--dark">{{ app()->getLocale() === 'ar' ? 'المكتبة' : 'The library' }}</p>
            <h1>{{ app()->getLocale() === 'ar' ? 'كل أعمالنا.' : 'Our complete library.' }}</h1>
            <p>{{ app()->getLocale() === 'ar' ? 'اكتشف مجموعة البرامج والمحتوى الذي تنتجه وتوزعه MLI.' : 'Explore the shows and content produced and distributed by MLI.' }}</p>
        </div>
    </section>
    <section class="section section--light">
        <div class="container">
            <div class="show-grid">
                @foreach($shows as $index => $show)
                    <a href="{{ route('shows.index') }}" class="show-card" data-reveal="up" style="--reveal-delay: {{ min($index % 6, 5) * 60 }}ms">
                        <div class="show-card__image">
                            @if($show->cover_image_url)<img src="{{ $show->cover_image_url }}" alt="{{ $show->cover_image_alt ?? '' }}" loading="lazy">@endif
                            <span class="show-card__arrow">↗</span>
                        </div>
                        <div class="show-card__meta">
                            <h3>{{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}</h3>
                            @if($show->category)<span>{{ app()->getLocale() === 'ar' ? $show->category->name_ar : $show->category->name_en }}</span>@endif
                        </div>
                    </a>
                @endforeach
            </div>
            @if($shows->hasPages())
                <div class="library-pagination">{{ $shows->links() }}</div>
            @endif
        </div>
    </section>
</x-layouts.public>