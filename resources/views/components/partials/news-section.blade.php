@props(['mediaNews', 'mliNews'])

<section
    id="news"
    x-data="{
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
    <h2 class="mb-12 text-3xl font-medium text-navy-950">
        {{ app()->getLocale() === 'ar' ? 'الأخبار' : 'News' }}
    </h2>

    <div class="grid gap-12 lg:grid-cols-2">
        {{-- MLI News column --}}
        <div>
            <h3 class="mb-6 border-b border-silver-200 pb-3 text-lg font-medium text-blue-600">
                {{ app()->getLocale() === 'ar' ? 'أخبار الشركة' : 'MLI News' }}
            </h3>
            <div class="space-y-5">
                <template x-for="item in items.slice(0, {{ $mliNews->count() }})" :key="item.id">
                    <button @click="openModal = item.id" class="flex w-full items-center gap-4 text-start group">
                        <div class="h-16 w-24 flex-shrink-0 overflow-hidden rounded-lg bg-silver-100">
                            <img :src="item.image" :alt="item.imageAlt" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                        </div>
                        <span class="font-medium text-navy-950 group-hover:text-blue-600 transition" x-text="item.title"></span>
                    </button>
                </template>

                @if ($mliNews->isEmpty())
                <p class="text-sm text-navy-950/50">
                    {{ app()->getLocale() === 'ar' ? 'لا توجد أخبار حاليًا' : 'No news yet.' }}
                </p>
                @endif
            </div>
        </div>

        {{-- Media News column --}}
        <div>
            <h3 class="mb-6 border-b border-silver-200 pb-3 text-lg font-medium text-gold-500">
                {{ app()->getLocale() === 'ar' ? 'أخبار الإعلام' : 'Media News' }}
            </h3>
            <div class="space-y-5">
                <template x-for="item in items.slice({{ $mliNews->count() }})" :key="item.id">
                    <button @click="openModal = item.id" class="flex w-full items-center gap-4 text-start group">
                        <div class="h-16 w-24 flex-shrink-0 overflow-hidden rounded-lg bg-silver-100">
                            <img :src="item.image" :alt="item.imageAlt" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                        </div>
                        <span class="font-medium text-navy-950 group-hover:text-blue-600 transition" x-text="item.title"></span>
                    </button>
                </template>

                @if ($mediaNews->isEmpty())
                <p class="text-sm text-navy-950/50">
                    {{ app()->getLocale() === 'ar' ? 'لا توجد أخبار حاليًا' : 'No news yet.' }}
                </p>
                @endif
            </div>
        </div>
    </div>

    <x-partials.content-modal />
</section>