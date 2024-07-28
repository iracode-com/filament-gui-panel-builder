<x-filament::section style="margin: 20px">
    <x-slot name="heading">
        {{ __('Create Model') }}
    </x-slot>
    <form wire:submit="create">
        {{ $this->form }}

        <x-filament::link :href="$this->get_panel_builder_page_url()" style="margin: 0 10px">
            {{ __("Return") }}
        </x-filament::link>
        <x-filament::button type="submit" class="mt-3">
            <x-filament::loading-indicator class="h-5 w-5" wire:loading wire:target="create" />
            {{ __('Create') }}
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
</x-filament::section>
