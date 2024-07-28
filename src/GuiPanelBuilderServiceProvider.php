<?php

namespace IracodeCom\FilamentGuiPanelBuilder;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use IracodeCom\FilamentGuiPanelBuilder\Services\DatabaseService;
use IracodeCom\FilamentGuiPanelBuilder\Services\FilamentCodeTransformer;
use IracodeCom\FilamentGuiPanelBuilder\Services\ModelFinder;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class GuiPanelBuilderServiceProvider extends PackageServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-gui-panel-builder');

        $this->publishes([
            __DIR__ . '/../config/filament-gui-panel-builder.php' => config_path('filament-gui-panel-builder.php'),
        ],'filament-gui-panel-builder-config');
        $this->publishes([
            __DIR__ . '/../resources/views/filament/pages/gui-migration-builder.blade.php' => resource_path('views/filament/pages/gui-migration-builder.blade.php'),
            __DIR__ . '/../resources/views/filament/pages/gui-model-builder.blade.php' => resource_path('views/filament/pages/gui-model-builder.blade.php'),
            __DIR__ . '/../resources/views/filament/pages/gui-panel-builder.blade.php' => resource_path('views/filament/pages/gui-panel-builder.blade.php'),
            __DIR__ . '/../resources/views/filament/pages/gui-resource-builder.blade.php' => resource_path('views/filament/pages/gui-resource-builder.blade.php'),
            __DIR__ . '/../resources/views/filament/pages/gui-sql-builder.blade.php' => resource_path('views/filament/pages/gui-sql-builder.blade.php'),
        ],'filament-gui-panel-builder-resources');
        $this->publishes([
            __DIR__ . '/../resources/views/components/layouts/app.blade.php' => resource_path('views/components/layouts/app.blade.php'),
        ],'iracode-filament-layout-resource');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/filament-gui-panel-builder.php', 'filament-gui-panel-builder'
        );
    }

    public function configurePackage(Package $package): void
    {
        $package->name('filament-gui-panel-builder')
                ->hasConfigFile(['filament-gui-panel-builder'])
                ->hasViews();
    }

    public static function installPackage()
    {
        Artisan::call('filament:clear-cached-components');
        Artisan::call('filament:cache-components');
        Artisan::call('vendor:publish', ['--tag' => 'reliese-models']);
    }
}
