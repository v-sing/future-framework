<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/27 0027
 * Time: 15:37
 */


Route::any('ajax/lang', 'AjaxController@lang');
Route::any('ajax/upload', 'AjaxController@upload');
Route::get('ajax/wipecache', 'AjaxController@wipecache');
Route::any('ajax/weigh','AjaxController@weigh');

