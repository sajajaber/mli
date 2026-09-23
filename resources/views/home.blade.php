<x-layouts.public :title="'Media Link International'">
    <x-partials.hero :shows="$shows" />
    <x-partials.content-teaser :shows="$shows" />
    <x-partials.new-releases :releases="$newReleases" />
    <x-partials.about-section :content="$aboutPage" :stats="$aboutStats" />
    <x-partials.key-people :people="$people" />
    <x-partials.news-section :media-news="$mediaNews" :mli-news="$mliNews" />

    <section id="contact" class="mli-contact">
        <div class="mli-contact__giant" aria-hidden="true">MLI</div>
        <div class="container mli-contact__inner" data-reveal="up">
            <span class="mli-contact__index">06 / CONTACT</span>
            <h2>
                @if(app()->getLocale() === 'ar')
                    لديك قصة؟ <em>لنحرّكها.</em>
                @else
                    Have a story? <em>Let’s move it.</em>
                @endif
            </h2>
            <a href="mailto:info@mli-lb.com" class="mli-contact__link">
                <span>{{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Start a conversation' }}</span>
                <b>↗</b>
            </a>
        </div>
        <div class="mli-contact__footer container">
            <span>BEIRUT / DUBAI / THE WORLD</span>
            <span>MEDIA LINK INTERNATIONAL</span>
        </div>
    </section>
</x-layouts.public>