<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/4/5
 * Time: 10:27
 */

namespace Future\Admin\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseTableCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the export table';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Install directory.
     *
     * @var string
     */
    protected $directory = '';

    public function handle()
    {
        $this->CreateSchemaTable();
    }

    /**
     * 创建备份文件
     */
    protected function CreateSchemaTable()
    {
        $databse    = config('database.connections.' . config('database.default'));
        $table      = "select table_name from information_schema.tables where table_schema='" . $databse['database'] . "'";
        $tableArray = toArray(DB::select($table));
        $str        = '$connection = config(\'admin.database.connection\') ?: config(\'database.default\');' . "\r\n";
        $downStr    = $str;
        $array      = [];
        foreach ($tableArray as $table) {
            $table_name = str_replace($databse['prefix'], '', $table['table_name']);
            if($table_name=='migrations'){
                continue;
            }
            $data       = DB::table($table_name)->get();
            if ($data) {
                $data               = toArray($data);
                $array[$table_name] = $data;
            }
            $str     .= 'Schema::connection($connection)->dropIfExists("' . $table_name . '");' . "\r\n";
            $downStr .= 'Schema::connection($connection)->dropIfExists("' . $table_name . '");' . "\r\n";
            $str     .= 'Schema::connection($connection)->create("' . $table_name . '", function (Blueprint $table) {' . "\r\n";
            $sql     = "SELECT column_name,column_default,is_nullable,data_type,character_maximum_length,numeric_precision,numeric_scale,column_key,column_comment,column_type FROM information_schema.columns WHERE table_schema= '{$databse['database']}' and  table_name = '{$table['table_name']}'";
            $column  = DB::select($sql);
            foreach ($column as $item) {
                $str .= $this->SwitchField(toArray($item));
            }
            $str .= ' });' . "\r\n";
        }
        $file     = database_path('migrations') . '/' . date('Y_m_d_') . '100000_create_admin_tables.php';
        $dataFile = database_path('data') . '/' . date('Y_m_d_') . '100000_create_admin_tables_data.php';
        $contents = $this->getStub('create_admin_tables');
        $this->laravel['files']->put($file, str_replace(['<%up%>', '<%down%>'], [$str, $downStr], $contents));
        $this->line($file);
        $this->laravel['files']->put($dataFile,'<?php '."\r\n".' return $rows='.var_export($array,true).';');
        $this->line($dataFile);

    }

    /**
     * 生成
     * @param $column
     * @return string
     */
    protected function SwitchField($column)
    {
        $column_name              = $column['column_name'];
        $column_default           = $column['column_default'];
        $is_nullable              = $column['is_nullable'];
        $character_maximum_length = $column['character_maximum_length'];
        $numeric_precision        = $column['numeric_precision'];
        $numeric_scale            = $column['numeric_scale'];
        $column_key               = $column['column_key'];
        $column_comment           = $column['column_comment'];
        $column_type              = $column['column_type'];
        switch ($column['data_type']) {
            case 'tinyint':
                $field = "tinyInteger('{$column_name}')";
                break;
            case 'int':
                $field = "unsignedInteger('{$column_name}')";
                break;
            case 'bigint':
                $field = "unsignedBigInteger('{$column_name}')";
                break;
            case 'blob':
                $field = "binary('{$column_name}')";
                break;
            case 'boolean':
                $field = "boolean('{$column_name}')";
                break;
            case 'char':
                $field = "char('{$column_name}',$character_maximum_length)";
                break;
            case 'date':
                $field = "date('{$column_name}')";
                break;
            case 'datetime':
                $field = "dateTime('{$column_name}')";
                break;
            case 'decimal':
                $field = "decimal('{$column_name}',$numeric_precision,$numeric_scale)";
                break;
            case 'double':
                $field = "double('{$column_name}',$numeric_precision,$numeric_scale)";
                break;
            case 'enum':
                preg_match('/\((.*?)\)/', $column_type, $ma);
                $field = "enum('{$column_name}',[" . $ma[1] . "])";
                break;
            case 'set':
                preg_match('/\((.*?)\)/', $column_type, $ma);
                $field = "enum('{$column_name}',[" . $ma[1] . "])";
                break;
            case 'float':
                $field = "float('{$column_name}',$numeric_precision,$numeric_scale)";
                break;
            case 'json':
                $field = "json('{$column_name}')";
                break;
            case 'jsonb':
                $field = "jsonb('{$column_name}')";
                break;
            case 'longtext':
                $field = "longText('{$column_name}')";
                break;
            case 'varchar':
                $field = "string('{$column_name}',$character_maximum_length)";
                break;
            case 'string':
                $field = "string('{$column_name}',$character_maximum_length)";
                break;
            case 'text':
                $field = "text('{$column_name}')";
                break;
            case 'time':
                $field = "time('{$column_name}')";
                break;
            case 'timestamp':
                $field = "timestamp('{$column_name}')";
                break;
            case 'year':
                $field = "year('{$column_name}')";
                break;
            case 'mediumint':
                $field = "mediumInteger('{$column_name}')";
                break;
            default:
                $this->line('Unsupported field types Please revise it by yourself.');
                exit;
                break;
        }
        $chain = '$table->' . $field;
        if ($is_nullable == 'YES') {
            $chain .= '->nullable()';
        }
        if (is_string($column_default)) {
            $column_default = "'" . $column_default . "'";
        }
        if ($column_default == null) {
        } else {
            $chain .= "->default($column_default)";
        }
        if ($column_key == 'PRI') {
            $chain .= "->autoIncrement()";
        }
        if ($column_key == 'UNI') {
            $chain .= "->unique()";
        }
        $chain .= "->comment('{$column_comment}');" . "\r\n";
        return $chain;
    }

    /**
     * Get stub contents.
     *
     * @param $name
     *
     * @return string
     */
    protected function getStub($name)
    {
        return $this->laravel['files']->get(__DIR__ . "/stubs/$name.stub");
    }
}