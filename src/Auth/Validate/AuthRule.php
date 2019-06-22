<?php

namespace Future\Admin\Auth\Validate;

use Future\Admin\Future\Validate;

class AuthRule extends Validate
{

    /**
     * 正则
     */
    protected $regex = ['format' => '[a-z0-9_\/]+'];

    /**
     * 验证规则
     */
    protected $rule = [
        'name'  => 'require|format|unique:AuthRule',
        'title' => 'require',
    ];

    /**
     * 提示消息
     */
    protected $message = [
        'name.format' => 'URL规则只能是小写字母、数字、下划线和/组成'
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
    ];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field                  = [
            'name'  => lang('Name'),
            'title' => lang('Title'),
        ];
        $this->message['name.format'] = lang('Name only supports letters, numbers, underscore and slash');
        parent::__construct($rules, $message, $field);
    }

}
