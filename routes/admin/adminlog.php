<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/6/11 0011
 * Time: 9:56
 */
Route::any('adminlog','AdminlogController@index');
Route::any('adminlog/index','AdminlogController@index');

Route::any('adminlog/detail','AdminlogController@detail');
Route::any('adminlog/del','AdminlogController@del');