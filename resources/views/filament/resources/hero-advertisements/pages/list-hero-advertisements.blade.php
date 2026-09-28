@php
    use App\Models\HeroAdvertisement;
@endphp

<x-filament-panels::page>
    @php
        $heroAdvertisements = $this->getHeroAdvertisements();
    @endphp

    <div
        x-data="{
            items: @js($heroAdvertisements->map(fn (HeroAdvertisement $hero) => [
                'id' => $hero->id,
                'image' => $hero->image_url,
                'alt' => $hero->image_alt ?: 'MLI hero advertisement',
            ])->values()),
            draggedId: null,
            dirty: false,
            dropIndex: null,

            startDrag(id) {
                this.draggedId = id;
            },

            dragOver(event, index) {
                event.preventDefault();
                this.dropIndex = index;
            },

            drop(event, targetIndex) {
                event.preventDefault();

                if (this.draggedId === null) {
                    return;
                }

                const fromIndex = this.items.findIndex(item => item.id === this.draggedId);

                if (fromIndex === -1 || fromIndex === targetIndex) {
                    this.draggedId = null;
                    this.dropIndex = null;
                    return;
                }

                const [moved] = this.items.splice(fromIndex, 1);
                const adjustedTarget = fromIndex < targetIndex ? targetIndex - 1 : targetIndex;

                this.items.splice(adjustedTarget, 0, moved);
                this.dirty = true;
                this.draggedId = null;
                this.dropIndex = null;
            },

            cancelDrag() {
                this.draggedId = null;
                this.dropIndex = null;
            },

            async saveOrder() {
                if (!this.dirty) {
                    return;
                }

                await this.$wire.saveHeroOrder(this.items.map(item => item.id));
                this.dirty = false;
            }
        }"
        class="space-y-6"
    >
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-950 dark:text-white">
                    Hero artwork
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Drag any thumbnail and place it before or after another one. Click Save Order when you are finished.
                </p>
            </div>

            <button
                type="button"
                x-on:click="saveOrder()"
                x-bind:disabled="! dirty"
                class="fi-btn fi-btn-size-md fi-btn-color-primary inline-flex shrink-0 items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm transition disabled:pointer-events-none disabled:opacity-45"
            >
                <x-filament::icon icon="heroicon-o-check" class="h-5 w-5" />
                <span>Save Order</span>
            </button>
        </div>

        @if ($heroAdvertisements->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center shadow-sm dark:border-white/15 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                    No hero advertisements have been added yet.
                </p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Use the Add Hero Advertisement action above to upload your first hero image.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                <template x-for="(item, index) in items" :key="item.id">
                    <article
                        draggable="true"
                        x-on:dragstart="startDrag(item.id)"
                        x-on:dragover="dragOver($event, index)"
                        x-on:drop="drop($event, index)"
                        x-on:dragend="cancelDrag()"
                        x-bind:class="{
                            'ring-2 ring-primary-500/70': draggedId === item.id,
                            'ring-2 ring-primary-300/60': dropIndex === index && draggedId !== item.id
                        }"
                        class="group cursor-grab overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md active:cursor-grabbing dark:border-white/10 dark:bg-gray-900"
                    >
                        <div class="relative aspect-[16/9] w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                            <img
                                x-bind:src="item.image"
                                x-bind:alt="item.alt"
                                class="block h-full w-full object-cover select-none"
                                draggable="false"
                            />

                            <div class="pointer-events-none absolute inset-0 bg-black/0 transition group-hover:bg-black/5"></div>
                        </div>
                    </article>
                </template>
            </div>
        @endif
    </div>
</x-filament-panels::page>
