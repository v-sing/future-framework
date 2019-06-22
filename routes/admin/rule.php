<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/6/11 0011
 * Time: 13:33
 */

Route::any('rule','RuleController@index');
Route::any('rule/index','RuleController@index');
Route::any('rule/add','RuleController@add');
Route::any('rule/edit','RuleController@edit');
Route::any('rule/multi','RuleController@multi');
Route::any('rule/del','RuleController@del');