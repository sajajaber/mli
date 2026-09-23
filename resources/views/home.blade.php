<x-layouts.public :title="'Media Link International'">
    <x-partials.hero :shows="$shows" />
    <x-partials.content-teaser :shows="$shows" />
    <x-partials.about-section :content="$aboutPage" :stats="$aboutStats" :services="$mediaServices" />
    <x-partials.key-people :people="$people" />
    <x-partials.news-section :media-news="$mediaNews" :mli-news="$mliNews" />
</x-layouts.public>
