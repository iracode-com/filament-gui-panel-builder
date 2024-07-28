<x-filament-panels::page>
    <div>
        <h3>A powerfull plugin to work with database and create filament resources with a graphical user
            interface(GUI).This plugin has these following features to help you work with database and filament easily:
        </h3>
    </div>
    <div class="grid" style="grid-template-columns: repeat(2, minmax(0, 1fr)); gap:10px">
        <a href="{{ $this->get_migration_builder_page_url() }}" id="model-card"
            class="flex rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
            <x-heroicon-s-arrow-up-on-square-stack style="color: rgb(100 116 139); width:64px;" />
            <div style="margin:0 10px; display: flex; flex-direction: column">
                <h2 style="font-weight: bold">{{ __('Migration Generator') }}</h2>
                <p style="font-size: 14px">
                    {{ __('This feature helps you to create a migration with fields that you need.') }}</p>
            </div>
        </a>
        <a href="{{ $this->get_model_builder_page_url() }}" id="model-card"
            class="flex rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
            <x-heroicon-m-squares-plus style="color: rgb(100 116 139); width:64px;" />
            <div style="margin:0 10px; display: flex; flex-direction: column">
                <h2 style="font-weight: bold">{{ __('Model Generator') }}</h2>
                <p style="font-size: 14px">{{ __('This feature helps you to create a model from an existing table.') }}
                </p>
            </div>
        </a>
        <a href="{{ $this->get_resource_builder_page_url() }}" id="model-card"
            class="flex rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
            <x-heroicon-m-tag style="color: rgb(100 116 139); width:64px;" />
            <div style="margin:0 10px; display: flex; flex-direction: column">
                <h2 style="font-weight: bold">{{ __('Resource Generator') }}</h2>
                <p style="font-size: 14px">
                    {{ __('This feature helps you to create a filament from an existing model.') }}</p>
            </div>
        </a>
        <a href="{{ $this->get_sql_builder_page_url() }}" id="model-card"
            class="flex rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
            <x-heroicon-s-arrow-down-on-square-stack style="color: rgb(100 116 139); width:64px;" />
            <div style="margin:0 10px; display: flex; flex-direction: column">
                <h2 style="font-weight: bold">{{ __('Model and Resource Generator') }}</h2>
                <p style="font-size: 14px">
                    {{ __('This feature helps you to create model and resource from an existing database.') }}</p>
            </div>
        </a>
    </div>
    <div>
        <h3>To optimize your project and have better performance on your project use these features:</h3>
    </div>
    <div class="grid" style="grid-template-columns: repeat(2, minmax(0, 1fr)); gap:10px">
        <a id="model-card" wire:click="optimize"
            class="flex rounded-lg cursor-pointer bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
            <x-heroicon-s-rocket-launch style="color: rgb(100 116 139); width:64px;" />
            <div style="margin:0 10px; display: flex; flex-direction: column">
                <h2 style="font-weight: bold">{{ __('Optimize Project') }}</h2>
                <p style="font-size: 14px">
                    {{ __('Optimizing project with re-caching project entities for better performance.') }}</p>
                <x-filament::loading-indicator class="h-5 w-5" wire:loading wire:target="optimize" />
            </div>
        </a>
        <a id="model-card" wire:click="filament_cache"
            class="flex rounded-lg cursor-pointer bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
            <x-heroicon-m-check-badge style="color: rgb(100 116 139); width:64px;" />
            <div style="margin:0 10px; display: flex; flex-direction: column">
                <h2 style="font-weight: bold">{{ __('Optimize Filament') }}</h2>
                <p style="font-size: 14px">
                    {{ __('Optimizing filament with re-caching its entities for better performance.') }}</p>
                <x-filament::loading-indicator class="h-5 w-5" wire:loading wire:target="filament_cache" />
            </div>
        </a>
        <a id="model-card" wire:click="icons_cache"
            class="flex rounded-lg cursor-pointer bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
            <x-heroicon-s-bolt style="color: rgb(100 116 139); width:64px;" />
            <div style="margin:0 10px; display: flex; flex-direction: column">
                <h2 style="font-weight: bold">{{ __('Cache Icons') }}</h2>
                <p style="font-size: 14px">
                    {{ __('Optimizing project with re-caching its blade icons for better performance.') }}</p>
                <x-filament::loading-indicator class="h-5 w-5" wire:loading wire:target="icons_cache" />
            </div>
        </a>
        <a id="model-card" wire:click="clear_caches"
            class="flex rounded-lg cursor-pointer bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
            <x-heroicon-m-bars-arrow-up style="color: rgb(100 116 139); width:64px;" />
            <div style="margin:0 10px; display: flex; flex-direction: column">
                <h2 style="font-weight: bold">{{ __('Clear All Caches') }}</h2>
                <p style="font-size: 14px">{{ __('Clear all caches in project include laravel and filament caches.') }}
                </p>
                <x-filament::loading-indicator class="h-5 w-5" wire:loading wire:target="clear_caches" />
            </div>
        </a>
    </div>
</x-filament-panels::page>
