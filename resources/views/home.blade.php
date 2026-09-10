<x-layouts.public :title="'Media Link International'">
    <x-partials.hero :shows="$shows" />
    <x-partials.content-teaser :shows="$shows" />
    <x-partials.about-section :page="$aboutPage" />
    <x-partials.news-section :media-news="$mediaNews" :mli-news="$mliNews" />

    {{-- Clients, Contact sections coming next --}}
</x-layouts.public>