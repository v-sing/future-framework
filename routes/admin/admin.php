<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/4/28 0028
 * Time: 11:14
 */

Route::any('admin','AdminController@index');

Route::any('admin/index','AdminController@index');

Route::any('admin/add','AdminController@add');

Route::any('admin/edit','AdminController@edit');

Route::any('admin/del','AdminController@del');