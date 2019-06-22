<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/13 0013
 * Time: 15:35
 */

namespace Future\Admin\Form;

use Future\Admin\Form;
use Illuminate\Support\Facades\Facade;

class FormFacade extends Facade
{
    /**
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Form::class;
    }
}