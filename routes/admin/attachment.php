<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/28
 * Time: 21:36
 */
Route::any('attachment','AttachmentController@index');
Route::any('attachment/index','AttachmentController@index');
Route::any('attachment/select','AttachmentController@select');
Route::any('attachment/add','AttachmentController@add');
Route::any('attachment/edit','AttachmentController@edit');
Route::get('attachment/detail','AttachmentController@detail');