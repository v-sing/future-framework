<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/6/19
 * Time: 16:54
 */

namespace Future\Admin\Auth\Validate;
use Future\Admin\Future\Validate;

class AuthGroup extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'name' => 'require',
        'rules' => 'require',
    ];

}