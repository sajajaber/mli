<x-layouts.public :title="'Media Link International'">
    <x-partials.hero :shows="$shows" />
    <x-partials.news-section :media-news="$mediaNews" :mli-news="$mliNews" />
    <x-partials.content-teaser :shows="$shows" :categories="$categories" />
    <x-partials.about-section :content="$aboutPage" :stats="$aboutStats" :services="$mediaServices" />
    <x-partials.key-people :people="$people" />
    <x-partials.clients :clients="$clients" />
</x-layouts.public>
