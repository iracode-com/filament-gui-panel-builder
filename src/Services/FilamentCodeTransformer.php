<?php

namespace IracodeCom\FilamentGuiPanelBuilder\Services;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FilamentCodeTransformer
{
    public static function transformFilamentResource($string,$model)
    {
        // Step 1: Add relationship chain function to TextInput fields with *_id in form method
        $string = preg_replace_callback(
            "/Forms\\\\Components\\\\TextInput::make\('([^']*_id)'\)(.*?)(->default\(.*?\))/s",
            function ($matches) use($model){
                $fieldName = trim($matches[1]," ");
                $rest = $matches[2];
                $default = $matches[3];
                $tableName = Str::plural(str_replace('_id', '', $fieldName));
                $attribute = DatabaseService::getFirstStringColumn($tableName);

                if (!Schema::hasTable($tableName)) {
                    // If the table doesn't exist, return the original matched string
                    return $matches[0];
                }

                $rest = preg_replace('/->numeric\(\)/', '', $rest);
                $model_name = \IracodeCom\FilamentGuiPanelBuilder\Services\ModelFinder::searchModels(Str::studly(Str::singular($tableName)));
                if($model_name){
                    $rest = class_exists($model_name) ? "->searchable()->options(\\{$model_name}::pluck('$attribute','id'))" . $rest : '';
                }
                else{
                    $rest = method_exists($model, $tableName) ? "->searchable()->relationship('{$tableName}', '{$attribute}')" . $rest : '';
                }
                return "Forms\\Components\\Select::make('{$fieldName}'){$rest}{$default}";
            },
            $string
        );

        // Step 2: Convert TextColumn fields with *_id in table method
        $string = preg_replace_callback(
            "/Tables\\\\Columns\\\\TextColumn::make\('([^']*_id)'\)(.*?)(->sortable\(\))/s",
            function ($matches) {
                $fieldName = trim(str_replace('_id', '.attribute_name', $matches[1])," ");
                $tableName = Str::plural(str_replace(".attribute_name","",$fieldName));
                $attribute = DatabaseService::getFirstStringColumn($tableName);
                $fieldName = str_replace(".attribute_name",$attribute ? '.'.$attribute : "_id",$fieldName);
                $rest = $matches[2];
                $sortable = $matches[3];
                
                $rest = preg_replace('/->numeric\(\)/', '', $rest); 
                $sortable = str_replace('->sortable()', '->searchable()', $sortable);

                return "Tables\\Columns\\TextColumn::make('{$fieldName}'){$rest}{$sortable}";
            },
            $string
        );

        // Step 3: Add label chain function and remove existing labels
        $string = preg_replace_callback(
            "/(Forms\\\\Components\\\\TextInput|Textarea|FileUpload|Select|DateTimePicker|DatePicker|Forms\\\\Components\\\\Select|Tables\\\\Columns\\\\TextColumn)::make\('([^']*)'\)(.*?)(,|\)|;)/s",
            function ($matches) {
                $className = $matches[1];
                $fieldName = trim($matches[2]," ");
                $rest = $matches[3];
                $ending = $matches[4];

                // Remove existing label function
                $rest = preg_replace("/->label\(.*?\)/", '', $rest);

                // Create readable label
                $label = explode(".",trim(self::createLabel($fieldName)))[0];

                return "{$className}::make('{$fieldName}')->label(__(\"{$label}\")){$rest}{$ending}";
            },
            $string
        );

        return $string;
    }

    private static function createLabel($fieldName)
    {
        $label = str_replace(['_id', '_'], [' ', ' '], $fieldName);
        return ucwords($label);
    }

    public static function getNameSpace($class){
        $exploded_class_name = explode("\\",$class);
        $exploded_class_name = array_filter($exploded_class_name,function($item){
            if($item && $item != "") return $item;
        });
        array_pop($exploded_class_name);
        return implode("\\",$exploded_class_name);
    }

    public static function generateFilamentResource($model)
    {
        $model_name = trim(str_replace(static::getNameSpace($model), '', $model),"\\/ ");
        $resource_path = "Filament/Resources/{$model_name}Resource.php";
        $resource_class = "App\\".str_replace("/","\\",str_replace(".php","",$resource_path));
        if(class_exists($resource_class)){
            return false;
        }
        Artisan::call('make:filament-resource', [
            'name' => $model_name,
            '--generate' => true,
            "--model-namespace"=>static::getNameSpace($model),
        ]);
        $filament_resouce_text = static::getFileContent($resource_path);
        if ($filament_resouce_text && $filament_resouce_text != "") {
            static::putFileContent($resource_path, static::transformFilamentResource($filament_resouce_text,$model));
            return true;
        }
        return false;
    }

    public static function getFileContent($path)
    {
        $path = app_path($path);
        if (File::exists($path)) {
            $content = File::get($path);
            return $content;
        } else {
            return "";
        }
    }
    public static function putFileContent($path, $content)
    {
        $path = app_path($path);
        if (File::exists($path)) {
            File::put($path, $content);
            return true;
        } else {
            return "";
        }
    }
}
