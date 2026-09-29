@php
$heroAdvertisements = $this->getHeroAdvertisements();

$activeHeroAdvertisements = $heroAdvertisements
->where('is_active', true)
->values();

$inactiveHeroAdvertisements = $heroAdvertisements
->where('is_active', false)
->values();
@endphp

<x-filament-panels::page>
    <div
        x-data="{
            items: @js(
                $activeHeroAdvertisements->map(fn ($hero) => [
                    'id' => $hero->id,
                    'image' => $hero->image_url,
                    'alt' => $hero->image_alt ?: 'MLI hero advertisement',
                    'active' => true,
                ])->values()
            ),

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

                this.dropIndex = event.clientY < midpoint
                    ? index
                    : index + 1;
            },

            drop(event) {
                event.preventDefault();

                if (
                    this.draggedId === null ||
                    this.dropIndex === null
                ) {
                    this.cancelDrag();
                    return;
                }

                const fromIndex = this.items.findIndex(
                    item => item.id === this.draggedId
                );

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
                    await this.$wire.saveHeroOrder(
                        this.items.map(item => item.id)
                    );

                    this.dirty = false;
                } finally {
                    this.saving = false;
                }
            }
        }"
        class="mli-hero-manager">
        <section class="mli-hero-manager__panel">

            {{-- HEADER --}}
            <div class="mli-hero-manager__header">
                <div>
                    <p class="mli-hero-manager__eyebrow">
                        Homepage
                    </p>

                    <div class="mli-hero-manager__title-row">
                        <h2 class="mli-hero-manager__title">
                            Hero Advertisements
                        </h2>

                        <span class="mli-hero-manager__count">
                            {{ $activeHeroAdvertisements->count() }}
                            {{ $activeHeroAdvertisements->count() === 1 ? 'active image' : 'active images' }}
                        </span>
                    </div>

                    <p class="mli-hero-manager__description">
                        Arrange the homepage hero visually. Drag the active artwork cards
                        to change their order, then save when the sequence is correct.
                    </p>
                </div>

                <div class="mli-hero-manager__header-actions">
                    <span
                        x-show="dirty"
                        x-cloak
                        class="mli-hero-manager__dirty">
                        Unsaved changes
                    </span>

                    <button
                        type="button"
                        x-on:click="saveOrder()"
                        x-bind:disabled="! dirty || saving"
                        class="fi-btn fi-btn-size-md fi-btn-color-primary mli-hero-manager__save">
                        <x-filament::icon
                            x-show="! saving"
                            icon="heroicon-o-check"
                            class="h-5 w-5" />

                        <x-filament::icon
                            x-show="saving"
                            icon="heroicon-o-arrow-path"
                            class="h-5 w-5 animate-spin" />

                        <span x-text="saving ? 'Saving...' : 'Save Order'">
                            Save Order
                        </span>
                    </button>
                </div>
            </div>

            <div class="mli-hero-manager__body">

                {{-- ACTIVE HERO IMAGES --}}
                <section class="mli-hero-manager__section">
                    <div class="mli-hero-manager__section-head">
                        <p class="mli-hero-manager__section-label">
                            Hero images
                        </p>

                        <p class="mli-hero-manager__section-note">
                            Drag the artwork cards to change their order.
                        </p>
                    </div>

                    @if ($activeHeroAdvertisements->isEmpty())
                    <div class="mli-hero-manager__empty">
                        <p class="mli-hero-manager__section-label">
                            No active hero images
                        </p>

                        <p class="mli-hero-manager__section-note">
                            Activate an image to include it in the homepage hero.
                        </p>
                    </div>
                    @else
                    <div class="mli-hero-manager__grid">
                        <template
                            x-for="(item, index) in items"
                            :key="item.id">
                            <article
                                draggable="true"

                                x-on:dragstart="startDrag(item.id)"
                                x-on:dragover="dragOver($event, index)"
                                x-on:drop="drop($event)"
                                x-on:dragend="cancelDrag()"

                                x-bind:class="{
                                        'opacity-60': draggedId === item.id
                                    }"

                                class="mli-hero-manager__card">
                                {{-- Drop before --}}
                                <div
                                    x-show="
                                            dropIndex === index &&
                                            draggedId !== item.id
                                        "
                                    x-cloak
                                    class="mli-hero-manager__drop-line mli-hero-manager__drop-line--top"></div>

                                {{-- Drop after --}}
                                <div
                                    x-show="
                                            dropIndex === index + 1 &&
                                            draggedId !== item.id
                                        "
                                    x-cloak
                                    class="mli-hero-manager__drop-line mli-hero-manager__drop-line--bottom"></div>

                                <div class="mli-hero-manager__card-shell">

                                    <div class="mli-hero-manager__card-image">
                                        <img
                                            x-bind:src="item.image"
                                            x-bind:alt="item.alt"
                                            draggable="false" />

                                        <span
                                            x-text="index + 1"
                                            class="mli-hero-manager__card-index"></span>

                                        <span class="mli-hero-manager__card-status mli-hero-manager__card-status--active">
                                            Active
                                        </span>
                                    </div>

                                    <div class="mli-hero-manager__card-meta">
                                        <p
                                            x-text="
                                                    item.alt ||
                                                    'MLI hero advertisement'
                                                "
                                            class="mli-hero-manager__card-alt"></p>
                                    </div>

                                </div>
                            </article>
                        </template>
                    </div>
                    @endif
                </section>

                {{-- INACTIVE HERO IMAGES --}}
                @if ($inactiveHeroAdvertisements->isNotEmpty())
                <section class="mli-hero-manager__section">

                    <div class="mli-hero-manager__section-head">
                        <p class="mli-hero-manager__section-label">
                            Inactive images
                        </p>

                        <p class="mli-hero-manager__section-note">
                            These images are saved but are not currently displayed
                            in the homepage hero.
                        </p>
                    </div>

                    <div class="mli-hero-manager__grid">

                        @foreach ($inactiveHeroAdvertisements as $hero)
                        <article class="mli-hero-manager__card">

                            <div class="mli-hero-manager__card-shell">

                                <div class="mli-hero-manager__card-image">

                                    <img
                                        src="{{ $hero->image_url }}"
                                        alt="{{ $hero->image_alt ?: 'MLI hero advertisement' }}" />

                                    <span class="mli-hero-manager__card-status mli-hero-manager__card-status--inactive">
                                        Inactive
                                    </span>

                                </div>

                                <div class="mli-hero-manager__card-meta">
                                    <p class="mli-hero-manager__card-alt">
                                        {{ $hero->image_alt ?: 'MLI hero advertisement' }}
                                    </p>
                                </div>

                            </div>

                        </article>
                        @endforeach

                    </div>
                </section>
                @endif

            </div>
        </section>
    </div>
</x-filament-panels::page>