<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/6/15 0015
 * Time: 10:20
 */

namespace Future\Admin\Auth\Validate;


use Future\Admin\Future\Validate;

class Admin extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'username' => 'require|max:50|unique:admin',
        'nickname' => 'require',
        'password' => 'require',
        'email'    => 'require|email|unique:admin,email',
    ];

    /**
     * 提示消息
     */
    protected $message = [
    ];

    /**
     * 字段描述
     */
    protected $field = [
    ];

    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['username', 'email', 'nickname', 'password'],
        'edit' => ['username', 'email', 'nickname'],
    ];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'username' => lang('Username'),
            'nickname' => lang('Nickname'),
            'password' => lang('Password'),
            'email'    => lang('Email'),
        ];
        parent::__construct($rules, $message, $field);
    }
}