<?php

namespace IracodeCom\FilamentGuiPanelBuilder\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use App\Http\Services\DatabaseService;
use App\Models\Post;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;
use Illuminate\Support\Str;

class GuiResourceBuilder extends Page implements HasForms
{
    use InteractsWithForms;
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public function getView(): string
    {
        if (view()->exists("filament.pages.gui-resource-builder")) {
            return "filament.pages.gui-resource-builder";
        }
        return "filament-gui-panel-builder::filament.pages.gui-resource-builder";
    }

    public static function canAccess(): bool
    {
        return config("filament-gui-panel-builder.can_access", true);
    }

    public ?array $data = [];
    public function get_panel_builder_page_url()
    {
        return GuiPanelBuilder::getUrl([]);
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Select::make("connection")
                    ->label(__("Connection"))
                    ->required()
                    ->searchable()
                    ->live()
                    ->options(\IracodeCom\FilamentGuiPanelBuilder\Services\DatabaseService::getAllConnections()),
                \Filament\Forms\Components\TextInput::make("namespace")
                    ->label(__("Namespace"))
                    ->required()
                    ->live()
                    ->default("App\\Models"),
                \Filament\Forms\Components\TextInput::make("table_prefix")
                    ->label(__("Table Prefix"))
                    ->live(),
                \Filament\Forms\Components\Select::make("models")
                    ->label(__("Models"))
                    ->required()
                    ->searchable()
                    ->multiple()
                    ->options(function (Get $get) {
                        $connection_lists = \IracodeCom\FilamentGuiPanelBuilder\Services\DatabaseService::getAllConnections();
                        if ($get("connection") && $get('namespace')) {
                            $model_list = \IracodeCom\FilamentGuiPanelBuilder\Services\DatabaseService::getModelNamesByConnectionWithSameKeyAndValues($connection_lists[$get("connection")], $get('namespace'),$get('table_prefix'));
                            $edited_model_list = [];
                            foreach ($model_list as $key => $value) {
                                $edited_model_list[$key] = explode('\\', $value)[count(explode('\\', $value)) - 1];
                            }
                            return $edited_model_list;
                        }
                        return [];
                    }),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function create()
    {
        try {
            $data = $this->form->getState();
            if ($data['models']) {
                foreach ($data['models'] as $model) {
                    \IracodeCom\FilamentGuiPanelBuilder\Services\FilamentCodeTransformer::generateFilamentResource($model);
                }
                return Notification::make()
                    ->title(__("Success"))
                    ->body(__("Resources Generated Successfully!"))
                    ->success()
                    ->send();
            }
            Artisan::call('filament:clear-cached-components');
            Artisan::call('filament:cache-components');
        } catch (\Exception $e) {
            return Notification::make()
                ->title(__("Error"))
                ->body(__("Error occurred in operation, Check code or laravel logs!"))
                ->danger()
                ->send();
        }
    }
}
