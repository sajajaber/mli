{{--
    Reusable floating-tab modal, controlled by Alpine's x-data on a wrapping element.
    Triggered by setting `openModal = <id>` on a card's click; closes on backdrop click or Escape.
--}}
<div
    x-show="openModal !== null"
    x-cloak
    class="fixed inset-0 z-[100] flex items-center justify-center p-4"
    style="display: none;">
    {{-- Backdrop --}}
    <div
        x-show="openModal !== null"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="openModal = null"
        class="absolute inset-0 bg-navy-950/80 backdrop-blur-sm"></div>

    {{-- Panel --}}
    <div
        x-show="openModal !== null"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-6 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-6 scale-95"
        @click.outside="openModal = null"
        @keydown.window.escape="openModal = null"
        class="relative z-10 max-h-[85vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-paper-50 shadow-2xl">
        <button
            @click="openModal = null"
            class="absolute end-4 top-4 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-navy-950/10 text-navy-950 transition hover:bg-navy-950/20"
            aria-label="Close">
            &times;
        </button>

        <template x-for="item in items" :key="item.id">
            <div x-show="openModal === item.id" class="p-8">
                {{-- Vimeo trailer (Shows) --}}
                <div x-show="item.vimeo_url" class="mb-6 aspect-video overflow-hidden rounded-xl bg-navy-950">
                    <iframe
                        :src="openModal === item.id && item.vimeo_url ? item.vimeo_url : ''"
                        class="h-full w-full"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen></iframe>
                </div>

                {{-- Featured image (News) --}}
                <img
                    x-show="item.image"
                    :src="item.image"
                    :alt="item.imageAlt"
                    class="mb-6 w-full rounded-xl object-cover">

                <h3 class="text-2xl font-medium text-navy-950" x-text="item.title"></h3>
                <div class="prose prose-navy mt-4 max-w-none" x-html="item.body"></div>
            </div>
        </template>
    </div>
</div>