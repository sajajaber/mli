@props(['people'])

<section id="key-people" class="mli-people section">
    <div class="container">
        <div class="mli-people__heading" data-reveal="up">
            <div>
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
                        </div>
                        <h3>{{ app()->getLocale() === 'ar' ? ($person->name_ar ?: $person->name) : $person->name }}</h3>
                        @php
                            $role = app()->getLocale() === 'ar'
                                ? ($person->role_title_ar ?: $person->role_title)
                                : $person->role_title;
                        @endphp
                        @if($role)
                            <p>{{ $role }}</p>
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
