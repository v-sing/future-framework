<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/27 0027
 * Time: 15:39
 */


Route::get('config', 'ConfigController@index');
Route::any('config/add', 'ConfigController@add');
Route::post('config/edit', 'ConfigController@edit');
Route::post('config/check', 'ConfigController@check');