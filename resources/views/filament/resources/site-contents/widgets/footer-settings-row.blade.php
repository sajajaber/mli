<x-filament::section class="mli-admin-footer-panel">
    <div class="mli-admin-footer-panel__top">
        <div class="mli-admin-footer-panel__heading">
            <div class="mli-admin-footer-panel__icon" aria-hidden="true">
                <x-filament::icon icon="heroicon-o-building-office-2" class="h-5 w-5" />
            </div>

            <div>
                <div class="mli-admin-footer-panel__eyebrow">Website-wide</div>
                <h3 class="mli-admin-footer-panel__title">Footer &amp; Contact</h3>
                <p class="mli-admin-footer-panel__description">
                    Control the information that appears in the MLI footer across every public page.
                </p>
            </div>
        </div>

        @if ($footer)
            <x-filament::button
                tag="a"
                icon="heroicon-m-pencil-square"
                color="gray"
                :href="\App\Filament\Resources\FooterSettings\FooterSettingResource::getUrl('edit', ['record' => $footer])"
            >
                Edit Footer
            </x-filament::button>
        @endif
    </div>

    @if ($footer)
        <div class="mli-admin-footer-panel__grid">
            <div class="mli-admin-footer-panel__preview">
                <span class="mli-admin-footer-panel__label">Brand</span>
                <strong>
                    {{ app()->getLocale() === 'ar' ? ($footer->tagline_ar ?: $footer->tagline) : $footer->tagline ?: 'No tagline set' }}
                </strong>
                <p>
                    {{ app()->getLocale() === 'ar'
                        ? ($footer->description_ar ?: $footer->description)
                        : $footer->description ?: 'No footer description set.' }}
                </p>
            </div>

            <div class="mli-admin-footer-panel__details">
                <div class="mli-admin-footer-panel__detail">
                    <span>Email</span>
                    <strong>{{ $footer->email ?: 'Not set' }}</strong>
                </div>

                <div class="mli-admin-footer-panel__detail">
                    <span>Phone</span>
                    <strong>{{ $footer->phone_primary ?: 'Not set' }}</strong>
                </div>

                <div class="mli-admin-footer-panel__detail">
                    <span>Office</span>
                    <strong>{{ $footer->office_address ? str_replace(["\r\n", "\r", "\n"], ' · ', $footer->office_address) : 'Not set' }}</strong>
                </div>

                <div class="mli-admin-footer-panel__detail">
                    <span>Social</span>
                    <strong>
                        {{ collect([
                            $footer->facebook_url ? 'Facebook' : null,
                            $footer->linkedin_url ? 'LinkedIn' : null,
                        ])->filter()->implode(' · ') ?: 'Not set' }}
                    </strong>
                </div>
            </div>
        </div>

        <div class="mli-admin-footer-panel__bottom">
            <span>
                Last updated {{ $footer->updated_at?->diffForHumans() ?? '—' }}
            </span>
            <span class="mli-admin-footer-panel__status">Configured</span>
        </div>
    @else
        <div class="mli-admin-footer-panel__empty">
            <strong>Footer settings are not configured yet.</strong>
            <span>Create the global footer settings to populate the public site footer.</span>
        </div>
    @endif
</x-filament::section>