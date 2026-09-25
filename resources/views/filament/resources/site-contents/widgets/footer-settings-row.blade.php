<x-filament::section class="mli-admin-footer-card">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex min-w-0 items-start gap-4">
            <div class="mli-admin-footer-card__icon" aria-hidden="true">
                <x-filament::icon icon="heroicon-o-envelope" class="h-5 w-5" />
            </div>

            <div class="min-w-0">
                <div class="mli-admin-footer-card__eyebrow">Global site settings</div>

                <h3 class="mli-admin-footer-card__title">
                    Footer &amp; Contact
                </h3>

                <p class="mli-admin-footer-card__description">
                    Manage the footer brand copy, contact details, office address, social links, and privacy URL shown across the public website.
                </p>

                @if ($footer)
                    <div class="mli-admin-footer-card__meta">
                        <span class="mli-admin-footer-card__pill">Configured</span>

                        @if ($footer->email)
                            <span class="mli-admin-footer-card__pill">{{ $footer->email }}</span>
                        @endif

                        @if ($footer->updated_at)
                            <span class="mli-admin-footer-card__pill">
                                Updated {{ $footer->updated_at->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                @else
                    <div class="mli-admin-footer-card__meta">
                        <span class="mli-admin-footer-card__pill">Not configured yet</span>
                    </div>
                @endif
            </div>
        </div>

        @if ($footer)
            <div class="mli-admin-footer-card__actions">
                <x-filament::button
                    tag="a"
                    icon="heroicon-m-pencil-square"
                    :href="\App\Filament\Resources\FooterSettings\FooterSettingResource::getUrl('edit', ['record' => $footer])"
                >
                    Edit Footer
                </x-filament::button>
            </div>
        @endif
    </div>
</x-filament::section>