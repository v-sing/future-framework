<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/22 0022
 * Time: 14:24
 */

namespace Future\Admin\Auth\Database;

use Illuminate\Database\Seeder;


class AdminTablesSeeder extends Seeder
{
    public function run()
    {
//        $dataArray = require dirname(dirname(dirname(__DIR__))) . '/database/data/data.php';
        $data = $this->loadRoutesFile(database_path('data'));
        if (empty($data)) {
            return false;
        }
        rsort($data);
        $path=realpath($data[0]);
        $dataArray = include $path;
        foreach ($dataArray as $table => $data) {
            if($data){
                $model = config('admin.database.' . $table . '_model');
                $model::truncate();
                $model::insert($data);
            }
        }
    }

    /**
     * 递归文件
     * @param $path
     * @return array
     */
    protected function loadRoutesFile($path)
    {
        $allRoutesFilePath = array();
        foreach (glob($path) as $file) {
            if (is_dir($file)) {
                $allRoutesFilePath = array_merge($allRoutesFilePath, $this->loadRoutesFile($file . '/*'));
            } else {
                $allRoutesFilePath[] = $file;
            }
        }
        return $allRoutesFilePath;
    }

}