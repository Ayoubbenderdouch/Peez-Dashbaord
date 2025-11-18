<x-filament-panels::page>
    <form wire:submit="activateSubscription">
        {{ $this->form }}

        <x-filament::button
            type="submit"
            class="mt-6"
            size="lg"
        >
            Activate Subscription
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
