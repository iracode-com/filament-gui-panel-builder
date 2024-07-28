<?php

namespace IracodeCom\FilamentGuiPanelBuilder;

use Filament\Contracts\Plugin;
use Filament\FilamentManager;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use IracodeCom\FilamentGuiPanelBuilder\Filament\Pages\GuiMigrationBuilder;
use IracodeCom\FilamentGuiPanelBuilder\Filament\Pages\GuiModelBuilder;
use IracodeCom\FilamentGuiPanelBuilder\Filament\Pages\GuiPanelBuilder;
use IracodeCom\FilamentGuiPanelBuilder\Filament\Pages\GuiResourceBuilder;
use IracodeCom\FilamentGuiPanelBuilder\Filament\Pages\GuiSqlBuilder;

class GuiPanelBuilderPlugin implements Plugin
{

    /**
     * @param Panel $panel
     * @return void
     */
    public function boot(Panel $panel): void
    {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return 'filament-gui-panel-builder';
    }

    /**
     * @param Panel $panel
     * @return void
     */
    public function register(Panel $panel): void
    {
        $panel->pages([
            GuiPanelBuilder::class,
            GuiMigrationBuilder::class,
            GuiModelBuilder::class,
            GuiResourceBuilder::class,
            GuiSqlBuilder::class,
        ]);
    }

    /**
     * @return static
     */
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @return Plugin|FilamentManager
     */
    public static function get(): FilamentManager | Plugin
    {
        return filament(app(static::class)->getId());
    }
}
