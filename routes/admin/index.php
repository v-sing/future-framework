<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/27 0027
 * Time: 15:35
 */

Route::any('/', 'IndexController@index');
Route::any('index', 'IndexController@index');
Route::any('index/index', 'IndexController@index');
Route::any('login','IndexController@login');
Route::get('logout','IndexController@logout');
Route::any('index/lang','IndexController@lang');