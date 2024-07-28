<?php

namespace IracodeCom\FilamentGuiPanelBuilder\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class DatabaseService
{
    public static function createMigration($tableData)
    {
        $tableName = $tableData['name'];
        $connectionName = static::getAllConnections()[$tableData['connection']];
        $tableComment = $tableData['table_comment'];
        $fields = $tableData['fields'];
        $generateId = $tableData['generate_id'];
        $generateTimestamps = $tableData['generate_timestamps_fields'];

        $migrationName = 'create_' . $tableName . '_table';
        $className = Str::studly($migrationName);
        $timestamp = date('Y_m_d_His');
        $filePath = database_path("migrations/{$timestamp}_{$migrationName}.php");

        $migrationContent = "<?php
    
use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::connection('{$connectionName}')->create('{$tableName}', function (Blueprint \$table) {
    ";

        if ($generateId) {
            $migrationContent .= "        \$table->id();\n";
        }

        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'];
            $length = isset($field['length']) && $field['length'] ? ", {$field['length']}" : '';
            $default = isset($field['default']) && $field['default'] !== null ? "->default('{$field['default']}')" : '';
            $nullable = isset($field['nullable']) && $field['nullable'] ? '->nullable()' : '';
            $columnComment = isset($field['column_comment']) && $field['column_comment'] ? "->comment('{$field['column_comment']}')" : '';
            $columnRelation = isset($field['column_relation']) ? $field['column_relation'] : '';

            if ($columnRelation == '') {
                switch ($type) {
                    case 'VARCHAR':
                        $type = "string";
                        break;
                    case 'CHAR':
                        $type = "char";
                        break;
                    case 'INT':
                    case 'BIGINT':
                    case 'TINYINT':
                    case 'SMALLINT':
                        $type = "integer";
                        break;
                    case 'FLOAT':
                        $type = "float";
                        break;
                    case 'DOUBLE':
                        $type = "double";
                        break;
                    case 'DECIMAL':
                        $type = "decimal";
                        break;
                    case 'TEXT':
                    case 'MEDIUMTEXT':
                    case 'LONGTEXT':
                        $type = "text";
                        break;
                    case 'DATE':
                        $type = "date";
                        break;
                    case 'TIME':
                        $type = "time";
                        break;
                    case 'DATETIME':
                        $type = "dateTime";
                        break;
                    case 'TIMESTAMP':
                        $type = "timestamp";
                        break;
                    case 'BOOLEAN':
                        $type = "boolean";
                        break;
                    case 'JSON':
                        $type = "json";
                        break;
                    default:
                        $type = "string";
                        break;
                }
            }

            $migrationContent .= $columnRelation == '' ? "        \$table->{$type}('{$name}'{$length}){$default}{$nullable}{$columnComment};\n" : '';

            if ($field['is_primary']) {
                $migrationContent .= "            \$table->primary('{$name}');\n";
            }

            if ($field['is_ai'] && !str_contains($migrationContent, 'autoIncrement()')) {
                $migrationContent .= "            \$table->{$type}('{$name}')->autoIncrement();\n";
            }

            if ($columnRelation && Schema::hasTable($columnRelation)) {
                $migrationContent .= "            \$table->foreignId('{$name}')->constrained('{$columnRelation}');\n";
            }
        }

        if ($generateTimestamps) {
            $migrationContent .= "            \$table->timestamps();\n";
        }

        $migrationContent .= "        });

        Schema::table('{$tableName}', function (Blueprint \$table) {
            \$table->comment('{$tableComment}');
        });
    }

    public function down()
    {
        Schema::dropIfExists('{$tableName}');
    }
};
";

        File::put($filePath, $migrationContent);

        return true;
    }

    public static function generateModelFromTable($tableName, $connection = null)
    {
        if (!Schema::hasTable($tableName)) {
            return false;
        }

        $connection = $connection ? "protected \$connection = '$connection';" : '';

        // Fetch table columns
        $columns = DB::getSchemaBuilder()->getColumnListing($tableName);

        // Determine model name from table name
        $modelName = Str::studly(Str::singular($tableName));

        // Generate fillable attributes
        $fillable_columns = implode(',', array_map(function ($item) {
            return "'" . $item . "'";
        }, $columns));

        // Generate relationships (example: assuming 'users' table has 'hasMany' relationship)
        $relationships = implode("\n", array_map(function ($item) {
            $relation_name = Str::singular($item['table_name']);
            $model_name = Str::studly(Str::singular($item['table_name']));
            return "\t\t\tpublic function $relation_name(){
                return \$this->belongsTo($model_name::class);
            }";
        }, static::getRelationshipsList($tableName)));

        // Generate model class content
        $modelContent = <<<EOD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Generated With Iracode Gui Panel Builder

class $modelName extends Model
{
    protected \$fillable = [
        $fillable_columns
    ];

    $connection

    $relationships
}
EOD;

        // Define model file path
        $filePath = app_path("Models/$modelName.php");

        // Write model content to file
        File::put($filePath, $modelContent);

        return $filePath;
    }

    public static function getRelationshipsList($tableName)
    {
        $foreignKeys = [];
        if (Schema::hasTable($tableName)) {
            $columns = Schema::getColumnListing($tableName);

            foreach ($columns as $column) {
                if (str_ends_with($column, '_id') && static::foreignKeyColumnExists($tableName, $column)) {
                    $foreignKeys[] = [
                        'table_name' => Str::plural(str_replace('_id', '', $column)),
                        'model_name' => Str::studly(str_replace('_id', '', $column))
                    ];
                }
            }
        }
        return $foreignKeys;
    }
    public static function foreignKeyColumnExists($tableName, $foreignKeyColumn)
    {
        $databaseName = DB::connection()->getDatabaseName();
        $query = "
        SELECT COUNT(*) as count
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = ?
        AND TABLE_NAME = ?
        AND COLUMN_NAME = ?
        AND REFERENCED_TABLE_NAME IS NOT NULL
        ";

        $result = DB::select($query, [$databaseName, $tableName, $foreignKeyColumn]);

        return $result[0]->count > 0;
    }
    public static function getAllConnections(): array
    {
        $databaseConfig = config('database.connections');
        $connectionNames = array_keys($databaseConfig);
        return $connectionNames;
    }
    public static function getAllConnectionsWithSameKeyAndValues(): array
    {
        $databaseConfig = config('database.connections');
        $connectionNames = array_keys($databaseConfig);
        return array_combine($connectionNames, $connectionNames);
    }
    public static function getAllTables(string $connection = null): array
    {
        $connection = $connection ?: config('database.default');
        $tables = array_column(Schema::connection($connection)->getTables(), 'name');
        return $tables;
    }
    public static function getAllTablesWithSameKeyAndValues(string $connection = null): array
    {
        $connection = $connection ?: config('database.default');
        $tables = array_column(Schema::connection($connection)->getTables(), 'name');
        return array_combine($tables, $tables);
    }
    public static function getModelNamesByConnection($connectionName = null)
    {
        $connectionName = $connectionName ?: config('database.default');
        $tables = static::getAllTables($connectionName);
        $models = [];

        foreach ($tables as $table) {
            $modelName = Str::studly(Str::singular($table));
            if (class_exists('App\\Models\\' . $modelName)) {
                $models[] = 'App\\Models\\' . $modelName;
            }
        }
        return $models;
    }
    public static function getModelNamesByConnectionWithSameKeyAndValues($connectionName = null, $namespace = "App\\Models", $table_prefix = null)
    {
        try {
            $connectionName = $connectionName ?: config('database.default');
            $tables = static::getAllTables($connectionName);
            $models = [];

            foreach ($tables as $table) {
                if ($table_prefix) {
                    $table = str_replace($table_prefix, "", $table);
                }
                $modelName = Str::studly(Str::singular($table));
                try {
                    if (class_exists($namespace . "\\" . $modelName)) {
                        $models[] = $namespace . "\\" . $modelName;
                    }
                } catch (\Throwable $th) {
                    continue;
                }
            }
            return array_combine($models, $models);
        } catch (\Throwable $th) {
            return [];
        }
    }

    public static function get_field_types()
    {
        return [
            'INT' => 'INT',
            'SMALLINT' => 'SMALLINT',
            'BIGINT' => 'BIGINT',
            'TINYINT' => 'TINYINT',
            'FLOAT' => 'FLOAT',
            'DOUBLE' => 'DOUBLE',
            'DECIMAL' => 'DECIMAL',
            'CHAR' => 'CHAR',
            'VARCHAR' => 'VARCHAR',
            'TEXT' => 'TEXT',
            'MEDIUMTEXT' => 'MEDIUMTEXT',
            'LONGTEXT' => 'LONGTEXT',
            'DATE' => 'DATE',
            'TIME' => 'TIME',
            'DATETIME' => 'DATETIME',
            'TIMESTAMP' => 'TIMESTAMP',
            'BOOLEAN' => 'BOOLEAN',
            'JSON' => 'JSON',
        ];
    }

    public static function getColumnsList($tableName)
    {
        if (Schema::hasTable($tableName)) {
            return Schema::getColumnListing($tableName);
        }
        return [];
    }
    public static function getFirstStringColumn($tableName)
    {
        $firstVarcharField = null;

        $tableColumns = static::getColumnsList($tableName);

        foreach ($tableColumns as $column) {
            if (Schema::getColumnType($tableName, $column) == 'varchar') {
                $firstVarcharField = $column;
                break;
            }
        }
        return $firstVarcharField;
    }
    public static function getAllModelClasses()
    {
        $files = File::files(app_path('Models'));

        $classes = collect($files)
            ->map(function ($file) {
                $className = pathinfo($file, PATHINFO_FILENAME);
                return "App\\Models\\" . $className;
            })
            ->filter(function ($className) {
                return class_exists($className);
            });
        return $classes;
    }
    public static function getModelClassByName($name)
    {
        $files = File::files(app_path('Models'));

        $classes = collect($files)
            ->map(function ($file) {
                $className = pathinfo($file, PATHINFO_FILENAME);
                return "App\\Models\\" . $className;
            })
            ->filter(function ($className) {
                return class_exists($className);
            });
        return $classes;
    }
}
