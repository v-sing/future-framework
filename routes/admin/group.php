<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/6/11 0011
 * Time: 11:57
 */
Route::any('group', 'GroupController@index');
Route::any('group/index', 'GroupController@index');
Route::any('group/add', 'GroupController@add');
Route::any('group/edit', 'GroupController@edit');
Route::any('group/roletree', 'GroupController@roletree');
Route::any('group/del', 'GroupController@del');