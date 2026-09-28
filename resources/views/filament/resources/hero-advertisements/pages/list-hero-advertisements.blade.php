@php
    $heroAdvertisements = $this->getHeroAdvertisements();
@endphp

<x-filament-panels::page>
    <div
        x-data="{
            items: @js($heroAdvertisements->map(fn ($hero) => [
                'id' => $hero->id,
                'image' => $hero->image_url,
                'alt' => $hero->image_alt ?: 'MLI hero advertisement',
                'active' => $hero->is_active,
            ])->values()),
            draggedId: null,
            dirty: false,
            dropIndex: null,
            saving: false,

            startDrag(id) {
                this.draggedId = id;
                this.dropIndex = null;
            },

            dragOver(event, index) {
                event.preventDefault();

                if (this.draggedId === null) {
                    return;
                }

                const rect = event.currentTarget.getBoundingClientRect();
                const midpoint = rect.top + (rect.height / 2);

                this.dropIndex = event.clientY < midpoint ? index : index + 1;
            },

            drop(event) {
                event.preventDefault();

                if (this.draggedId === null || this.dropIndex === null) {
                    this.cancelDrag();
                    return;
                }

                const fromIndex = this.items.findIndex(item => item.id === this.draggedId);
                let insertionIndex = this.dropIndex;

                if (fromIndex === -1) {
                    this.cancelDrag();
                    return;
                }

                if (fromIndex < insertionIndex) {
                    insertionIndex -= 1;
                }

                if (fromIndex === insertionIndex) {
                    this.cancelDrag();
                    return;
                }

                const [moved] = this.items.splice(fromIndex, 1);

                this.items.splice(insertionIndex, 0, moved);
                this.dirty = true;

                this.cancelDrag();
            },

            cancelDrag() {
                this.draggedId = null;
                this.dropIndex = null;
            },

            async saveOrder() {
                if (!this.dirty || this.saving) {
                    return;
                }

                this.saving = true;

                try {
                    await this.$wire.saveHeroOrder(this.items.map(item => item.id));
                    this.dirty = false;
                } finally {
                    this.saving = false;
                }
            }
        }"
        class="space-y-5"
    >
        <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="border-b border-gray-100 px-5 py-5 dark:border-white/10">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-primary-600">
                            Homepage
                        </p>

                        <div class="mt-1 flex flex-wrap items-center gap-2.5">
                            <h2 class="text-xl font-semibold tracking-tight text-gray-950 dark:text-white">
                                Hero Advertisements
                            </h2>

                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-white/5 dark:text-gray-300">
                                {{ $heroAdvertisements->count() }} {{ $heroAdvertisements->count() === 1 ? 'image' : 'images' }}
                            </span>
                        </div>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500 dark:text-gray-400">
                            Arrange the homepage hero visually. Drag a thumbnail before or after another image, then save when the sequence is correct.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span
                            x-show="dirty"
                            x-cloak
                            class="inline-flex items-center gap-2 text-xs font-medium text-amber-700 dark:text-amber-300"
                        >
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                            Unsaved changes
                        </span>

                        <button
                            type="button"
                            x-on:click="saveOrder()"
                            x-bind:disabled="! dirty || saving"
                            class="fi-btn fi-btn-size-md fi-btn-color-primary inline-flex min-w-[130px] items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm transition disabled:pointer-events-none disabled:opacity-45"
                        >
                            <x-filament::icon
                                x-show="! saving"
                                icon="heroicon-o-check"
                                class="h-5 w-5"
                            />
                            <x-filament::icon
                                x-show="saving"
                                icon="heroicon-o-arrow-path"
                                class="h-5 w-5 animate-spin"
                            />
                            <span x-text="saving ? 'Saving...' : 'Save Order'">Save Order</span>
                        </button>
                    </div>
                </div>
            </div>

            @if ($heroAdvertisements->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-500 dark:bg-white/5 dark:text-gray-400">
                        <x-filament::icon icon="heroicon-o-photo" class="h-6 w-6" />
                    </div>

                    <p class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">
                        No hero advertisements yet
                    </p>

                    <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Use Add Hero Advertisement at the top of the page to upload your first hero artwork.
                    </p>
                </div>
            @else
                <div class="space-y-6 p-5">
                    <div>
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-400">
                                    Current order
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    This preview updates immediately as you drag items.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-3 overflow-x-auto pb-1">
                            <template x-for="(item, index) in items" :key="'preview-' + item.id">
                                <div class="w-[112px] shrink-0">
                                    <div class="relative overflow-hidden rounded-lg border border-gray-200 bg-gray-100 dark:border-white/10 dark:bg-gray-800">
                                        <div class="aspect-[16/9] w-full">
                                            <img
                                                x-bind:src="item.image"
                                                x-bind:alt="item.alt"
                                                class="h-full w-full object-cover"
                                                draggable="false"
                                            />
                                        </div>

                                        <span
                                            x-text="index + 1"
                                            class="absolute left-2 top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-black/65 px-1.5 text-[10px] font-semibold text-white backdrop-blur-sm"
                                        ></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6 dark:border-white/10">
                        <div class="mb-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-400">
                                Hero images
                            </p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Drag the artwork cards to change their order.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            <template x-for="(item, index) in items" :key="item.id">
                                <article
                                    draggable="true"
                                    x-on:dragstart="startDrag(item.id)"
                                    x-on:dragover="dragOver($event, index)"
                                    x-on:drop="drop($event)"
                                    x-on:dragend="cancelDrag()"
                                    x-bind:class="{
                                        'scale-[0.985] opacity-60': draggedId === item.id
                                    }"
                                    class="group relative cursor-grab active:cursor-grabbing"
                                >
                                    <div
                                        x-show="dropIndex === index && draggedId !== item.id"
                                        x-cloak
                                        class="pointer-events-none absolute inset-x-2 -top-1 z-20 h-0.5 rounded-full bg-primary-500"
                                    ></div>

                                    <div
                                        x-show="dropIndex === index + 1 && draggedId !== item.id"
                                        x-cloak
                                        class="pointer-events-none absolute inset-x-2 -bottom-1 z-20 h-0.5 rounded-full bg-primary-500"
                                    ></div>

                                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition duration-200 group-hover:-translate-y-0.5 group-hover:shadow-md dark:border-white/10 dark:bg-gray-900">
                                        <div class="relative aspect-[16/9] w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                                            <img
                                                x-bind:src="item.image"
                                                x-bind:alt="item.alt"
                                                class="block h-full w-full select-none object-cover object-center"
                                                draggable="false"
                                            />

                                            <span
                                                x-text="index + 1"
                                                class="absolute left-2.5 top-2.5 inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-black/65 px-1.5 text-[10px] font-bold text-white backdrop-blur-sm"
                                            ></span>

                                            <span
                                                x-text="item.active ? 'Active' : 'Inactive'"
                                                x-bind:class="item.active
                                                    ? 'bg-emerald-500/95 text-white'
                                                    : 'bg-black/60 text-white'"
                                                class="absolute right-2.5 top-2.5 inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-semibold backdrop-blur-sm"
                                            ></span>
                                        </div>

                                        <div class="px-3 py-2.5">
                                            <p
                                                x-text="item.alt || 'MLI hero advertisement'"
                                                class="truncate text-xs font-medium text-gray-600 dark:text-gray-300"
                                            ></p>
                                        </div>
                                    </div>
                                </article>
                            </template>
                        </div>
                    </div>
                </div>
            @endif
        </section>
    </div>
</x-filament-panels::page>
