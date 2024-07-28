<?php

namespace IracodeCom\FilamentGuiPanelBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModelFinder
{
    /**
     * Get all classes that start with App\Models
     *
     * @return array
     */
    public static function getAllModels()
    {
        $modelsPath = app_path('Models');
        $modelsNamespace = 'App\\Models\\';
        $modelClasses = [];

        if (File::exists($modelsPath)) {
            $files = File::allFiles($modelsPath);

            foreach ($files as $file) {
                $path = $file->getRealPath();
                $class = "App\\Models\\".str_replace(".php","",explode(("\\app\\Models\\"),$path)[1]);
                $modelClasses[] = $class;
            }
        }

        return $modelClasses;
    }

    public static function searchModels($model)
    {
        $modelsPath = app_path('Models');
        $modelsNamespace = 'App\\Models\\';
        $modelClass = null;

        if (File::exists($modelsPath)) {
            $files = File::allFiles($modelsPath);

            foreach ($files as $file) {
                $path = $file->getRealPath();
                $class = "App\\Models\\".str_replace(".php","",explode(("\\app\\Models\\"),$path)[1]);
                if(str_contains($class,$model)){
                    $modelClass = $class;
                    break;
                }
            }
        }
        return $modelClass;
    }

    /**
     * Extract the fully qualified class name from a file
     *
     * @param string $path
     * @param string $namespace
     * @return string|null
     */
    private static function getClassFromFile($path, $namespace)
    {
        $content = File::get($path);
        $tokens = token_get_all($content);
        $count = count($tokens);
        $namespaceFound = false;
        $class = '';

        for ($i = 0; $i < $count; $i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                $namespaceFound = true;
                $namespace = '';
                while ($tokens[++$i][0] === T_STRING || $tokens[$i][0] === T_NS_SEPARATOR) {
                    $namespace .= $tokens[$i][1];
                }
                $namespace .= '\\';
            }

            if ($tokens[$i][0] === T_CLASS) {
                if ($namespaceFound) {
                    $class = $namespace;
                    $namespaceFound = false;
                }
                while ($tokens[++$i][0] !== T_STRING) {
                    // Skip whitespace
                }
                $class .= $tokens[$i][1];
                return $class;
            }
        }

        return null;
    }
}
