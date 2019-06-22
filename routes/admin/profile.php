<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/4/16 0016
 * Time: 20:19
 */

Route::any('profile', 'ProfileController@index');
Route::any('profile/index', 'ProfileController@index');
Route::post('profile/update', 'ProfileController@update');