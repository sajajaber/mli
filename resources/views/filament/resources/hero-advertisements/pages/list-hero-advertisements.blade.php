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
                    'editUrl' => \App\Filament\Resources\HeroAdvertisements\HeroAdvertisementResource::getUrl('edit', ['record' => $hero]),
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

            async toggleHero(id) {
                await this.$wire.toggleHeroActive(id);
                window.location.reload();
            },

            async deleteHero(id) {
                if (!window.confirm('Delete this hero advertisement and its image permanently?')) {
                    return;
                }

                await this.$wire.deleteHeroAdvertisement(id);
                window.location.reload();
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
                                x-data="{ actionsOpen: false }"
                                draggable="true"

                                x-on:dragstart="startDrag(item.id)"
                                x-on:dragover="dragOver($event, index)"
                                x-on:drop="drop($event)"
                                x-on:dragend="cancelDrag()"

                                x-bind:class="{
                                        'opacity-60': draggedId === item.id,
                                        'mli-hero-manager__card--menu-open': actionsOpen
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

                                <div class="mli-hero-manager__card-shell relative overflow-visible" style="position: relative; overflow: visible;">



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

                                    <div class="mli-hero-manager__card-meta mli-hero-manager__card-meta--with-actions">
                                        <p
                                            x-text="
                                                    item.alt ||
                                                    'MLI hero advertisement'
                                                "
                                            class="mli-hero-manager__card-alt"></p>

                                        <div
                                            x-on:click.stop
                                            x-on:dragstart.prevent.stop
                                            draggable="false"
                                            class="mli-hero-manager__actions">
                                            <button
                                                type="button"
                                                aria-label="Hero image actions"
                                                x-on:click.stop="actionsOpen = !actionsOpen"
                                                x-bind:aria-expanded="actionsOpen"
                                                class="mli-hero-manager__actions-trigger">
                                                <x-filament::icon icon="heroicon-m-ellipsis-vertical" class="h-4 w-4" />
                                            </button>

                                            <div
                                                x-show="actionsOpen"
                                                x-cloak
                                                x-on:click.outside="actionsOpen = false"
                                                x-on:keydown.escape.window="actionsOpen = false"
                                                class="mli-hero-manager__actions-menu">
                                                <a
                                                    x-bind:href="item.editUrl"
                                                    x-on:click.stop
                                                    class="mli-hero-manager__action-item">
                                                    <x-filament::icon icon="heroicon-o-pencil-square" class="h-4 w-4" />
                                                    Edit
                                                </a>

                                                <button
                                                    type="button"
                                                    x-on:click.stop="actionsOpen = false; toggleHero(item.id)"
                                                    class="mli-hero-manager__action-item">
                                                    <x-filament::icon icon="heroicon-o-eye-slash" class="h-4 w-4" />
                                                    Inactivate
                                                </button>

                                                <button
                                                    type="button"
                                                    x-on:click.stop="actionsOpen = false; deleteHero(item.id)"
                                                    class="mli-hero-manager__action-item mli-hero-manager__action-item--danger">
                                                    <x-filament::icon icon="heroicon-o-trash" class="h-4 w-4" />
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
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
                        <article
                            x-data="{ actionsOpen: false }"
                            x-bind:class="{ 'mli-hero-manager__card--menu-open': actionsOpen }"
                            class="mli-hero-manager__card">

                            <div class="mli-hero-manager__card-shell relative overflow-visible" style="position: relative; overflow: visible;">



                                <div class="mli-hero-manager__card-image">

                                    <img
                                        src="{{ $hero->image_url }}"
                                        alt="{{ $hero->image_alt ?: 'MLI hero advertisement' }}" />

                                    <span class="mli-hero-manager__card-status mli-hero-manager__card-status--inactive">
                                        Inactive
                                    </span>

                                </div>


                                <div class="mli-hero-manager__card-meta mli-hero-manager__card-meta--with-actions">
                                    <p class="mli-hero-manager__card-alt">
                                        {{ $hero->image_alt ?: 'MLI hero advertisement' }}
                                    </p>

                                    <div
                                        x-on:click.stop
                                        draggable="false"
                                        class="mli-hero-manager__actions">
                                        <button
                                            type="button"
                                            aria-label="Hero image actions"
                                            x-on:click.stop="actionsOpen = !actionsOpen"
                                            x-bind:aria-expanded="actionsOpen"
                                            class="mli-hero-manager__actions-trigger">
                                            <x-filament::icon icon="heroicon-m-ellipsis-vertical" class="h-4 w-4" />
                                        </button>

                                        <div
                                            x-show="actionsOpen"
                                            x-cloak
                                            x-on:click.outside="actionsOpen = false"
                                            x-on:keydown.escape.window="actionsOpen = false"
                                            class="mli-hero-manager__actions-menu">
                                            <a
                                                href="{{ \App\Filament\Resources\HeroAdvertisements\HeroAdvertisementResource::getUrl('edit', ['record' => $hero]) }}"
                                                x-on:click.stop
                                                class="mli-hero-manager__action-item">
                                                <x-filament::icon icon="heroicon-o-pencil-square" class="h-4 w-4" />
                                                Edit
                                            </a>

                                            <button
                                                type="button"
                                                x-on:click.stop="actionsOpen = false; toggleHero({{ $hero->id }})"
                                                class="mli-hero-manager__action-item">
                                                <x-filament::icon icon="heroicon-o-eye" class="h-4 w-4" />
                                                Activate
                                            </button>

                                            <button
                                                type="button"
                                                x-on:click.stop="actionsOpen = false; deleteHero({{ $hero->id }})"
                                                class="mli-hero-manager__action-item mli-hero-manager__action-item--danger">
                                                <x-filament::icon icon="heroicon-o-trash" class="h-4 w-4" />
                                                Delete
                                            </button>
                                        </div>
                                    </div>
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


    <style>
        /* Keep open menus above neighboring cards and section content. */
        .mli-hero-manager,
        .mli-hero-manager__panel,
        .mli-hero-manager__body,
        .mli-hero-manager__section,
        .mli-hero-manager__grid,
        .mli-hero-manager__card,
        .mli-hero-manager__card-shell {
            position: relative;
            overflow: visible !important;
        }

        .mli-hero-manager__section:has(.mli-hero-manager__card--menu-open) {
            z-index: 10000 !important;
        }

        .mli-hero-manager__card--menu-open {
            z-index: 10001 !important;
        }

        .mli-hero-manager__card-meta--with-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .mli-hero-manager__card-meta--with-actions .mli-hero-manager__card-alt {
            flex: 1 1 auto;
            min-width: 0;
            margin: 0;
            overflow-wrap: anywhere;
        }

        .mli-hero-manager__actions {
            position: relative;
            flex: 0 0 auto;
            z-index: 10002 !important;
        }

        .mli-hero-manager__actions-trigger {
            display: inline-flex;
            width: 32px;
            height: 32px;
            align-items: center;
            justify-content: center;
            padding: 0;
            border: 1px solid transparent;
            border-radius: 8px;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            transition:
                background-color 150ms ease,
                border-color 150ms ease,
                color 150ms ease;
        }

        .mli-hero-manager__actions-trigger:hover,
        .mli-hero-manager__actions-trigger[aria-expanded="true"] {
            border-color: #e2e8f0;
            background: #f1f5f9;
            color: #2f4156;
        }

        .mli-hero-manager__actions-trigger:focus-visible {
            outline: 2px solid #c5a46d;
            outline-offset: 2px;
        }

        .mli-hero-manager__actions-menu {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            z-index: 10003 !important;
            width: 168px;
            overflow: hidden;
            padding: 4px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
            color: #374151;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.14);
        }

        .mli-hero-manager__action-item {
            display: flex;
            width: 100%;
            min-height: 36px;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: inherit;
            font: inherit;
            font-size: 13px;
            line-height: 1.25;
            text-align: left;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 140ms ease, color 140ms ease;
        }

        .mli-hero-manager__action-item:hover {
            background: #f3f4f6;
            color: #2f4156;
        }

        .mli-hero-manager__action-item:focus-visible {
            outline: 2px solid #c5a46d;
            outline-offset: -2px;
        }

        .mli-hero-manager__action-item--danger {
            color: #b42318;
        }

        .mli-hero-manager__action-item--danger:hover {
            background: #fef2f2;
            color: #991b1b;
        }

        .dark .mli-hero-manager__actions-trigger {
            color: #9ca3af;
        }

        .dark .mli-hero-manager__actions-trigger:hover,
        .dark .mli-hero-manager__actions-trigger[aria-expanded="true"] {
            border-color: #4b5563;
            background: #374151;
            color: #f9fafb;
        }

        .dark .mli-hero-manager__actions-menu {
            border-color: #374151;
            background: #1f2937;
            color: #f3f4f6;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.3);
        }

        .dark .mli-hero-manager__action-item:hover {
            background: #374151;
            color: #fff;
        }

        .dark .mli-hero-manager__action-item--danger {
            color: #fca5a5;
        }

        .dark .mli-hero-manager__action-item--danger:hover {
            background: rgba(127, 29, 29, 0.28);
            color: #fecaca;
        }

        @media (max-width: 640px) {
            .mli-hero-manager__actions-trigger {
                width: 30px;
                height: 30px;
            }

            .mli-hero-manager__actions-menu {
                width: 160px;
            }
        }
    </style>

</x-filament-panels::page>
