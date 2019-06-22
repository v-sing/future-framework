<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/22 0022
 * Time: 10:41
 */
return [
    /**
     * 路由规则
     */
    'route'     => [
        'prefix'     => env('ADMIN_ROUTE_PREFIX', 'admin'),
        'namespace'  => 'App\\Admin\\Controllers',
        'middleware' => ['web', 'admin'],
    ],
    /**
     * 安装路径
     */
    'directory' => app_path('Admin'),
    /**
     * 数据库表
     */
    'database'  => [
        'connection'              => [],
        'admin_table'             => 'admin',
        'admin_log_table'         => 'admin_log',
        'attachment_table'        => 'attachment',
        'auth_group_table'        => 'auth_group',
        'auth_group_access_table' => 'auth_group_access',
        'auth_rule_table'         => 'auth_rule',
        'category_table'          => 'category',
        'config_table'            => 'config',
        'ems_table'               => 'ems',
        'sms_table'               => 'sms',
        'test_table'              => 'test',
        'user_table'              => 'user',
        'user_group_table'        => 'user_group',
        'user_money_log_table'    => 'user_money_log',
        'user_rule_table'         => 'user_rule',
        'user_score_log_table'    => 'user_score_log',
        'user_token_table'        => 'user_token',
        'version_table'           => 'version',

        'admin_model'             => \Future\Admin\Auth\Database\Admin::class,
        'admin_log_model'         => \Future\Admin\Auth\Database\AdminLog::class,
        'attachment_model'        => \Future\Admin\Auth\Database\attachment::class,
        'auth_group_model'        => \Future\Admin\Auth\Database\AuthGroup::class,
        'auth_group_access_model' => \Future\Admin\Auth\Database\AuthGroupAccess::class,
        'auth_rule_model'         => \Future\Admin\Auth\Database\AuthRule::class,
        'category_model'          => \Future\Admin\Auth\Database\Category::class,
        'config_model'            => \Future\Admin\Auth\Database\Config::class,
        'ems_model'               => \Future\Admin\Auth\Database\Ems::class,
        'sms_model'               => \Future\Admin\Auth\Database\Sms::class,
        'test_model'              => \Future\Admin\Auth\Database\Test::class,
        'user_model'              => \Future\Admin\Auth\Database\User::class,
        'user_group_model'        => \Future\Admin\Auth\Database\UserGroup::class,
        'user_money_log_model'    => \Future\Admin\Auth\Database\UserMoneyLog::class,
        'user_rule_model'         => \Future\Admin\Auth\Database\UserRule::class,
        'user_score_log_model'    =>\Future\Admin\Auth\Database\UserScoreLog::class,
        'user_token_model'        => \Future\Admin\Auth\Database\UserToken::class,
        'version_model'           => \Future\Admin\Auth\Database\Version::class,

    ],

    //是否开启前台会员中心
    'usercenter'          => true,
    //登录验证码
    'login_captcha'       => true,
    //登录失败超过10则1天后重试
    'login_failure_retry' => true,
    //是否同一账号同一时间只能在一个地方登录
    'login_unique'        => false,
    //登录页默认背景图
    'login_background'    => "/assets/img/loginbg.jpg",
    //是否启用多级菜单导航
    'multiplenav'         => false,
    //自动检测更新
    'checkupdate'         => false,
    //版本号
    'version'             => '1.0.0.20181031_beta',
    //API接口地址
    'api_url'             => 'https://api.fastadmin.net',
    //
    'lang_switch_on'      => true,
    'default_image'       => 'file',
    'default_image_url'=>'https://tool.fastadmin.net/icon/%s.png'
];