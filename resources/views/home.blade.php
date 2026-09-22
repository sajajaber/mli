<x-layouts.public :title="'Media Link International'">
    <x-partials.hero :shows="$shows" />
    <x-partials.content-teaser :shows="$shows" />
    <x-partials.about-section :content="$aboutPage" :stats="$aboutStats" />
    <x-partials.news-section :media-news="$mediaNews" :mli-news="$mliNews" />

    <section id="contact" class="contact-section">
        <div class="container contact-section__inner" data-reveal="up">
            <div>
                <p class="eyebrow">{{ app()->getLocale() === 'ar' ? 'لنبقى على تواصل' : 'Let’s connect' }}</p>
                <h2>{{ app()->getLocale() === 'ar' ? 'لديكم قصة؟<br>لنتحدث.' : 'Have a story?<br>Let’s talk.' }}</h2>
            </div>
            <a href="mailto:info@mli.example" class="button button--light">{{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Get in touch' }} ↗</a>
        </div>
    </section>
</x-layouts.public>