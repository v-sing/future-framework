<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/5/6 0006
 * Time: 9:38
 */

namespace Future\Admin\Test;


use Illuminate\Support\Facades\Facade;

class TestFacade extends Facade
{
    /**
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Test::class;
    }
}