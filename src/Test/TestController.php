<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/26 0026
 * Time: 10:16
 */

namespace Future\Admin\Test;


use Future\Admin\Controllers\BackendController;

class TestController extends BackendController
{
    public function form(){

        return $this->view();
    }
}