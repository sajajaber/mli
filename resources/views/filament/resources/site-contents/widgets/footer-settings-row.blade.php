<x-filament::section>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <div class="flex items-center gap-3">
                <x-filament::icon
                    icon="heroicon-o-document-text"
                    class="h-5 w-5 text-gray-500 dark:text-gray-400"
                />

                <div>
                    <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                        Footer
                    </h3>

                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Manage the footer content, contact details, social links, and legal URL.
                    </p>
                </div>
            </div>
        </div>

        @if ($footer)
            <x-filament::button
                tag="a"
                :href="\App\Filament\Resources\FooterSettings\FooterSettingResource::getUrl('edit', ['record' => $footer])"
            >
                Edit Footer
            </x-filament::button>
        @endif
    </div>
</x-filament::section>
