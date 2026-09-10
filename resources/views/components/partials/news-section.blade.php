@props(['mediaNews', 'mliNews'])

<section
    id="news"
    x-data="{
        activeTab: 'mli',
        openModal: null,
        items: [
            @foreach ($mliNews->concat($mediaNews) as $item)
                {
                    id: {{ $item->id }},
                    title: @js(app()->getLocale() === 'ar' ? $item->title_ar : $item->title_en),
                    body: @js(app()->getLocale() === 'ar' ? $item->body_ar : $item->body_en),
                    image: @js($item->featured_image_url),
                    imageAlt: @js($item->featured_image_alt),
                    vimeo_url: null,
                },
            @endforeach
        ]
    }"
    class="mx-auto max-w-6xl px-6 py-24">
    <div class="mb-10 flex items-center justify-between">
        <h2 class="text-3xl font-medium text-navy-950">
            {{ app()->getLocale() === 'ar' ? 'الأخبار' : 'News' }}
        </h2>

        <div class="flex gap-1 rounded-full bg-silver-100 p-1">
            <button
                @click="activeTab = 'mli'"
                :class="activeTab === 'mli' ? 'bg-navy-950 text-white' : 'text-navy-950/60'"
                class="rounded-full px-5 py-2 text-sm font-medium transition">
                {{ app()->getLocale() === 'ar' ? 'أخبار الشركة' : 'MLI News' }}
            </button>
            <button
                @click="activeTab = 'media'"
                :class="activeTab === 'media' ? 'bg-navy-950 text-white' : 'text-navy-950/60'"
                class="rounded-full px-5 py-2 text-sm font-medium transition">
                {{ app()->getLocale() === 'ar' ? 'أخبار الإعلام' : 'Media News' }}
            </button>
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <template x-if="activeTab === 'mli'">
            <template x-for="item in items.slice(0, {{ $mliNews->count() }})" :key="item.id">
                <button
                    @click="openModal = item.id"
                    class="group text-start">
                    <div class="aspect-[4/3] overflow-hidden rounded-xl bg-silver-100">
                        <img :src="item.image" :alt="item.imageAlt" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                    </div>
                    <h3 class="mt-4 font-medium text-navy-950" x-text="item.title"></h3>
                </button>
            </template>
        </template>

        <template x-if="activeTab === 'media'">
            <template x-for="item in items.slice({{ $mliNews->count() }})" :key="item.id">
                <button
                    @click="openModal = item.id"
                    class="group text-start">
                    <div class="aspect-[4/3] overflow-hidden rounded-xl bg-silver-100">
                        <img :src="item.image" :alt="item.imageAlt" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                    </div>
                    <h3 class="mt-4 font-medium text-navy-950" x-text="item.title"></h3>
                </button>
            </template>
        </template>
    </div>

    <x-partials.content-modal />
</section>