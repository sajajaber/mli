@props(['people'])

<section id="key-people" class="mli-people section">
    <div class="container">
        <div class="mli-people__heading" data-reveal="up">
            <div>
                <span class="mli-people__index">05 / PEOPLE</span>
                <h2>{{ app()->getLocale() === 'ar' ? 'فريقنا' : 'Key People' }}</h2>
            </div>
        </div>

        @if($people->isNotEmpty())
            <div class="mli-people__grid">
                @foreach($people as $index => $person)
                    <article class="mli-person-card" data-reveal="up" style="--reveal-delay: {{ min($index, 5) * 70 }}ms">
                        <div class="mli-person-card__photo">
                            @if($person->photo_url)
                                <img src="{{ $person->photo_url }}" alt="{{ $person->name }}" loading="lazy">
                            @endif
                            <span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3>{{ $person->name }}</h3>
                        @if($person->role_title)
                            <p>{{ $person->role_title }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @else
            <div class="mli-content-empty" data-reveal="up">
                {{ app()->getLocale() === 'ar' ? 'لا يوجد أشخاص منشورون حاليًا.' : 'No people added yet.' }}
            </div>
        @endif
    </div>
</section>
