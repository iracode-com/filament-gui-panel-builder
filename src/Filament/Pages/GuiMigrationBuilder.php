<?php

namespace IracodeCom\FilamentGuiPanelBuilder\Filament\Pages;

use Filament\Pages\Page;
use App\Http\Services\DatabaseService;
use App\Models\Post;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;
use Illuminate\Support\Str;

class GuiMigrationBuilder extends Page implements HasForms
{
    use InteractsWithForms;
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public function getView(): string
    {
        if (view()->exists("filament.pages.gui-migration-builder")) {
            return "filament.pages.gui-migration-builder";
        }
        return "filament-gui-panel-builder::filament.pages.gui-migration-builder";
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
                \Filament\Forms\Components\TextInput::make("name")
                    ->label(__("Table Name"))
                    ->maxLength(100)
                    ->required(),
                \Filament\Forms\Components\Select::make("connection")
                    ->label(__("Connection"))
                    ->required()
                    ->searchable()
                    ->live()
                    ->options(\IracodeCom\FilamentGuiPanelBuilder\Services\DatabaseService::getAllConnections()),
                \Filament\Forms\Components\TextInput::make("table_comment")
                    ->label(__("Table Comment"))
                    ->maxLength(100),
                \Filament\Forms\Components\Repeater::make("fields")
                    ->label(__("Table Fields"))
                    ->schema([
                        \Filament\Forms\Components\Section::make()
                            ->schema([
                                \Filament\Forms\Components\TextInput::make("name")
                                    ->label(__("Column Name"))
                                    ->maxLength(100)
                                    ->required(),
                                \Filament\Forms\Components\Select::make("type")
                                    ->label(__("Column Type"))
                                    ->required()
                                    ->searchable()
                                    ->options(\IracodeCom\FilamentGuiPanelBuilder\Services\DatabaseService::get_field_types()),
                                \Filament\Forms\Components\TextInput::make("length")
                                    ->label(__("Column Length"))
                                    ->numeric(),
                                \Filament\Forms\Components\TextInput::make("default")
                                    ->label(__("Default")),
                                \Filament\Forms\Components\TextInput::make("column_comment")
                                    ->label(__("Comment")),
                                \Filament\Forms\Components\Toggle::make("is_primary")
                                    ->label(__("Is Primary"))
                                    ->required(),
                                \Filament\Forms\Components\Toggle::make("is_ai")
                                    ->label(__("Is Auto Increment"))
                                    ->required(),
                                \Filament\Forms\Components\Toggle::make("nullable")
                                    ->label(__("Nullable"))
                                    ->required(),
                                \Filament\Forms\Components\Section::make(__('Field Relation'))
                                    ->schema([
                                        \Filament\Forms\Components\Select::make("relation_table_exists")
                                            ->live()
                                            ->searchable()
                                            ->options([
                                                0=>'No Existing Table',
                                                1=>'Existing Table',
                                            ])
                                            ->label(__("Relationship Type")),
                                        \Filament\Forms\Components\Select::make("column_relation")
                                            ->label(__("Has Relation With Table"))
                                            ->searchable()
                                            ->visible(function (Get $get) {
                                                return $get('relation_table_exists') == 1;
                                            })
                                            ->options(function (Get $get) {
                                                $connection_lists = \IracodeCom\FilamentGuiPanelBuilder\Services\DatabaseService::getAllConnections();
                                                return $get("../../connection") ? \IracodeCom\FilamentGuiPanelBuilder\Services\DatabaseService::getAllTablesWithSameKeyAndValues($connection_lists[$get("../../connection")]) : [];
                                            }),
                                        \Filament\Forms\Components\TextInput::make("column_relation")
                                            ->label(__("Has Relation With Table"))
                                            ->visible(function (Get $get) {
                                                return $get('relation_table_exists') == 0;
                                            }),
                                    ])->columns(2)
                            ])->columns(2)
                    ])
                    ->columnSpanFull(),
                \Filament\Forms\Components\Toggle::make("migrate")
                    ->label(__("Migrate"))
                    ->live()
                    ->required(),
                \Filament\Forms\Components\Toggle::make("create_model")
                    ->label(__("Create Model"))
                    ->required(),
                \Filament\Forms\Components\Toggle::make("generate_id")
                    ->label(__("Generate Id Field"))
                    ->required(),
                \Filament\Forms\Components\Toggle::make("generate_timestamps_fields")
                    ->label(__("Generate Timestamps Fields (created_at,updated_at)"))
                    ->required(),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function create()
    {
        try {
            $create_migration_result = \IracodeCom\FilamentGuiPanelBuilder\Services\DatabaseService::createMigration($this->form->getState());
            if ($create_migration_result) {
                if ($this->form->getState()['migrate'] == 1) {
                    Artisan::call("migrate");
                }
                if ($this->form->getState()['migrate'] == 1 && $this->form->getState()['create_model'] == 1) {
                    try {
                        $connection_name = \IracodeCom\FilamentGuiPanelBuilder\Services\DatabaseService::getAllConnections()[$this->form->getState()['connection']];
                        \IracodeCom\FilamentGuiPanelBuilder\Services\DatabaseService::generateModelFromTable($this->form->getState()['name'], $connection_name);
                    } catch (\Exception $e) {
                        Artisan::call('make:model', [
                            'name' => Str::studly(Str::singular($this->form->getState()['name'])),
                        ]);
                    }
                }
                return Notification::make()
                    ->title(__("Success"))
                    ->body(__("Migration created successfully!"))
                    ->success()
                    ->send();
            } else {
                return Notification::make()
                    ->title(__("Error"))
                    ->body(__("Error in migrate creation!"))
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            return Notification::make()
                ->title(__("Error"))
                ->body(__("Error occurred in operation, Check code or laravel logs!"))
                ->danger()
                ->send();
        }
    }
}
