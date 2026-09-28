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
        class="mli-hero-manager"
    >
        <section class="mli-hero-manager__panel">
            <div class="mli-hero-manager__header">
                <div >
                    <div>
                        <p class="mli-hero-manager__eyebrow">
                            Homepage
                        </p>

                        <div class="mli-hero-manager__title-row">
                            <h2 class="mli-hero-manager__title">
                                Hero Advertisements
                            </h2>

                            <span class="mli-hero-manager__count">
                                {{ $heroAdvertisements->count() }} {{ $heroAdvertisements->count() === 1 ? 'image' : 'images' }}
                            </span>
                        </div>

                        <p class="mli-hero-manager__description">
                            Arrange the homepage hero visually. Drag a thumbnail before or after another image, then save when the sequence is correct.
                        </p>
                    </div>

                    <div class="mli-hero-manager__header-actions">
                        <span
                            x-show="dirty"
                            x-cloak
                            class="mli-hero-manager__dirty"
                        >
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                            Unsaved changes
                        </span>

                        <button
                            type="button"
                            x-on:click="saveOrder()"
                            x-bind:disabled="! dirty || saving"
                            class="fi-btn fi-btn-size-md fi-btn-color-primary mli-hero-manager__save"
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
                <div class="mli-hero-manager__empty">
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
                <div class="mli-hero-manager__body">
                    <div>
                        <div class="mli-hero-manager__section-head">
                            <div>
                                <p class="mli-hero-manager__section-label">
                                    Current order
                                </p>
                                <p class="mli-hero-manager__section-note">
                                    This preview updates immediately as you drag items.
                                </p>
                            </div>
                        </div>

                        <div class="mli-hero-manager__preview-strip">
                            <template x-for="(item, index) in items" :key="'preview-' + item.id">
                                <div class="mli-hero-manager__preview">
                                    <div class="mli-hero-manager__preview-frame">
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
                                            class="mli-hero-manager__preview-index"
                                        ></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="mli-hero-manager__section">
                        <div class="mli-hero-manager__section-head">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-400">
                                Hero images
                            </p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Drag the artwork cards to change their order.
                            </p>
                        </div>

                        <div class="mli-hero-manager__grid">
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
                                    class="mli-hero-manager__card"
                                >
                                    <div
                                        x-show="dropIndex === index && draggedId !== item.id"
                                        x-cloak
                                        class="mli-hero-manager__drop-line mli-hero-manager__drop-line--top"
                                    ></div>

                                    <div
                                        x-show="dropIndex === index + 1 && draggedId !== item.id"
                                        x-cloak
                                        class="mli-hero-manager__drop-line mli-hero-manager__drop-line--bottom"
                                    ></div>

                                    <div class="mli-hero-manager__card-shell">
                                        <div class="mli-hero-manager__card-image">
                                            <img
                                                x-bind:src="item.image"
                                                x-bind:alt="item.alt"
                                                class="block h-full w-full select-none object-cover object-center"
                                                draggable="false"
                                            />

                                            <span
                                                x-text="index + 1"
                                                class="mli-hero-manager__card-index"
                                            ></span>

                                            <span
                                                x-text="item.active ? 'Active' : 'Inactive'"
                                                x-bind:class="item.active
                                                    ? 'bg-emerald-500/95 text-white'
                                                    : 'mli-hero-manager__card-status--inactive'"
                                                class="mli-hero-manager__card-status"
                                            ></span>
                                        </div>

                                        <div class="mli-hero-manager__card-meta">
                                            <p
                                                x-text="item.alt || 'MLI hero advertisement'"
                                                class="mli-hero-manager__card-alt"
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
