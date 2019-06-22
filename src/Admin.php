<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/22 0022
 * Time: 10:42
 */

namespace Future\Admin;

use Closure;

class Admin
{
    private $module;
    private $controller;
    private $action;
    private $assign = [];
    private $nature = [];

    /**
     * 设置模块
     * @param $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * 设置对应控制器
     * @param $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * 设置对应
     * @param $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function action()
    {
        return $this->action;
    }

    /**
     * @return mixed
     */
    public function controller()
    {
        return $this->controller;
    }

    /**
     * @return mixed
     */
    public function module()
    {
        return $this->module;
    }

    /**
     * 设置模板输出
     * @param array $assign
     */
    public function setAssign($assign = [])
    {
        $this->assign = array_merge($this->assign, $assign);
    }

    /**
     *
     * @return array
     */
    public function assign()
    {
        return $this->assign;
    }

    /**
     * 设置属性
     */
    public function setNature($nature = [])
    {
        $this->nature = $nature;
    }

    /**
     *
     * @return array
     */
    public function nature()
    {
        return $this->nature;
    }

    public function Form($table, Closure $callback)
    {

    }

    /**
     * 获取模板输出
     * @param $key
     * @return mixed
     */
    public function getAssign($key)
    {
      return  $this->assign[$key];
    }
}