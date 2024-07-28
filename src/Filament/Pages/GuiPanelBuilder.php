<?php

namespace IracodeCom\FilamentGuiPanelBuilder\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

class GuiPanelBuilder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function shouldRegisterNavigation(): bool
    {
        return config('filament-gui-panel-builder.should_register_navigation', true);
    }

    public function getView(): string
    {
        if (view()->exists("filament.pages.gui-panel-builder")) {
            return "filament.pages.gui-panel-builder";
        }
        return "filament-gui-panel-builder::filament.pages.gui-panel-builder";
    }

    public static function canAccess(): bool
    {
        return config("filament-gui-panel-builder.can_access", true);
    }

    public function get_migration_builder_page_url()
    {
        return GuiMigrationBuilder::getUrl([]);
    }

    public function get_model_builder_page_url()
    {
        return GuiModelBuilder::getUrl([]);
    }

    public function get_resource_builder_page_url()
    {
        return GuiResourceBuilder::getUrl([]);
    }

    public function get_sql_builder_page_url()
    {
        return GuiSqlBuilder::getUrl([]);
    }

    public function optimize()
    {
        try {
            Artisan::call('filament:clear-cached-components');
            Artisan::call('filament:cache-components');
            Notification::make()
                ->title(__("Success"))
                ->body(__("Project Optimized!"))
                ->success()
                ->send();
        } catch (\Throwable $th) {
            Notification::make()
                ->title(__("Error"))
                ->body(__("Feature is not enabled!"))
                ->danger()
                ->send();
        }
    }

    public function filament_cache()
    {
        try {
            Artisan::call('filament:clear-cached-components');
            Artisan::call('filament:cache-components');
            Notification::make()
                ->title(__("Success"))
                ->body(__("Filament Optimized!"))
                ->success()
                ->send();
        } catch (\Throwable $th) {
            Notification::make()
                ->title(__("Error"))
                ->body(__("Feature is not enabled!"))
                ->danger()
                ->send();
        }
    }

    public function icons_cache()
    {
        try {
            Artisan::call('icons:cache');
            Notification::make()
                ->title(__("Success"))
                ->body(__("Blade Icons Optimized!"))
                ->success()
                ->send();
        } catch (\Throwable $th) {
            Notification::make()
                ->title(__("Error"))
                ->body(__("Feature is not enabled!"))
                ->danger()
                ->send();
        }
    }

    public function clear_caches()
    {
        try {
            Artisan::call('optimize:clear');
            Artisan::call('filament:clear-cached-components');
            Notification::make()
                ->title(__("Success"))
                ->body(__("All Caches Cleared!"))
                ->success()
                ->send();
        } catch (\Throwable $th) {
            Notification::make()
                ->title(__("Error"))
                ->body(__("Feature is not enabled!"))
                ->danger()
                ->send();
        }
    }
}
