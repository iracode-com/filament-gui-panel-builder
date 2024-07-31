# Filament GUI Panel Builder
Filament GUI Panel Builder provides a filament graphical user interface plugin for creating models, migrations, and Filament resources in Laravel applications simply and quickly.


## Prerequisite
- PHP >= 8.1
- Laravel >= 10
- Filament v3

## Installation and Configure
#### Package Installation
```bash
composer require iracode-com/filament-gui-panel-builder
```
#### Add Provider
in Laravel v11 , bootstrap/providers.php:

    <?php
        use IracodeCom\FilamentGuiPanelBuilder\GuiPanelBuilderServiceProvider;
        return [
    		App\Providers\AppServiceProvider::class,
    		App\Providers\Filament\AdminPanelProvider::class,
      		GuiPanelBuilderServiceProvider::class
	   ];

    ?>
    

in Laravel v10 , in config/app.php:

    <?php
        use IracodeCom\FilamentGuiPanelBuilder\GuiPanelBuilderServiceProvider;
        'providers' => ServiceProvider::defaultProviders()->merge([
			...,
		       GuiPanelBuilderServiceProvider::class
		])->toArray(),
    ?>
    
#### Add Plugin
in app\Providers\Filament\AdminPanelProvider.php:

    <?php
        use IracodeCom\FilamentGuiPanelBuilder\GuiPanelBuilderPlugin;
        return $panel
		...
		->plugins([
			GuiPanelBuilderPlugin::make()
		]);
    ?>
    
#### Publish Configs(optional)
```bash
php artisan vendor:publish --tag=filament-gui-panel-builder-config
```
#### Publish Resources(optional)
```bash
php artisan vendor:publish --tag=filament-gui-panel-builder-resources
```
#### Clear Caches
```bash
php artisan filament:clear
```
Now in Filament admin panel , Gui Panel Builder menu is registered!
## Translation
All texts in this plugin is used with laravel translation helper you should just translate them in your language translation json file.
