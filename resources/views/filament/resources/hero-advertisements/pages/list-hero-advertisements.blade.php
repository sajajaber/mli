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
                this.dropPosition = null;
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
        class="space-y-6"
    >
        <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="flex flex-col gap-4 border-b border-gray-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-base font-semibold tracking-tight text-gray-950 dark:text-white">
                            Hero artwork
                        </h2>

                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-white/5 dark:text-gray-300">
                            {{ $heroAdvertisements->count() }} {{ IlluminateSupportStr::plural('image', $heroAdvertisements->count()) }}
                        </span>
                    </div>

                    <p class="mt-1.5 max-w-2xl text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Drag a thumbnail to rearrange the hero sequence. Drop above the middle of a card to place it before that image, or below the middle to place it after it.
                    </p>
                </div>

                <div class="flex shrink-0 items-center gap-3">
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

            @if ($heroAdvertisements->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-500 dark:bg-white/5 dark:text-gray-400">
                        <x-filament::icon icon="heroicon-o-photo" class="h-6 w-6" />
                    </div>

                    <p class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">
                        No hero advertisements yet
                    </p>

                    <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Add your first hero artwork using the action at the top of this page.
                    </p>
                </div>
            @else
                <div class="p-4 sm:p-5">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                        <template x-for="(item, index) in items" :key="item.id">
                            <article
                                draggable="true"
                                x-on:dragstart="startDrag(item.id)"
                                x-on:dragover="dragOver($event, index)"
                                x-on:drop="drop($event, index)"
                                x-on:dragend="cancelDrag()"
                                x-bind:class="{
                                    'opacity-60 scale-[0.985]': draggedId === item.id,
                                    'shadow-lg': draggedId === item.id
                                }"
                                class="group relative cursor-grab overflow-visible rounded-2xl active:cursor-grabbing"
                            >
                                <div
                                    x-show="dropIndex === index && draggedId !== item.id"
                                    x-cloak
                                    class="pointer-events-none absolute inset-x-3 -top-1.5 z-20 h-1 rounded-full bg-primary-500 shadow-[0_0_0_4px_rgba(59,130,246,0.12)]"
                                ></div>

                                <div
                                    x-show="dropIndex === index + 1 && draggedId !== item.id"
                                    x-cloak
                                    class="pointer-events-none absolute inset-x-3 -bottom-1.5 z-20 h-1 rounded-full bg-primary-500 shadow-[0_0_0_4px_rgba(59,130,246,0.12)]"
                                ></div>

                                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition duration-200 group-hover:-translate-y-0.5 group-hover:shadow-md dark:border-white/10 dark:bg-gray-900">
                                    <div class="relative aspect-[16/9] w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                                        <img
                                            x-bind:src="item.image"
                                            x-bind:alt="item.alt"
                                            class="block h-full w-full select-none object-cover"
                                            draggable="false"
                                        />

                                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/45 via-black/0 to-black/0"></div>

                                        <span
                                            x-text="item.active ? 'Active' : 'Inactive'"
                                            x-bind:class="item.active
                                                ? 'bg-emerald-500/95 text-white'
                                                : 'bg-black/60 text-white'"
                                            class="absolute right-3 top-3 inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold tracking-wide backdrop-blur-sm"
                                        ></span>

                                        <div class="pointer-events-none absolute bottom-3 left-3 inline-flex items-center gap-2 rounded-full bg-black/50 px-2.5 py-1.5 text-[11px] font-medium text-white backdrop-blur-sm">
                                            <x-filament::icon icon="heroicon-m-bars-3" class="h-3.5 w-3.5" />
                                            <span>Drag to arrange</span>
                                        </div>
                                    </div>

                                    <div class="min-h-[72px] border-t border-gray-100 px-4 py-3 dark:border-white/10">
                                        <p
                                            x-text="item.alt || 'MLI hero advertisement'"
                                            class="line-clamp-2 text-xs leading-5 text-gray-500 dark:text-gray-400"
                                        ></p>
                                    </div>
                                </div>
                            </article>
                        </template>
                    </div>
                </div>
            @endif
        </section>
    </div>
</x-filament-panels::page>
