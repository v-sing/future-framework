<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/25 0025
 * Time: 11:24
 */

namespace Future\Admin\Middleware;

use Closure;
use Future\Admin\Auth\Auth;
use Illuminate\Support\Facades\Session;
use Future\Admin\Facades\Admin;

class AuthMiddleware
{
    protected $auth = null;

    /**
     * 验证登录
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $nature      = Admin::nature();
        $noNeedRight = $nature['noNeedRight'];
        $noNeedLogin = $nature['noNeedLogin'];
        $this->auth  = Auth::instance();
        // 检测是否需要验证登录
        if (!$this->auth->match($noNeedLogin)) {
            if (!$this->auth->isLogin()) {
                $url = Session::get('referer');
                $url = $url ? $url : $request->url();
                return error('Please login first', [], url('admin/login?url=' . urlencode($url)));
            }
        }
        $modulename     = Admin::module();
        $controllername = Admin::controller();
        $actionname     = Admin::action();
        $path           = $controllername . '/' . $actionname;
        if (!$this->auth->match($noNeedRight)) {
            if (!$this->auth->check($path)) {
                return error('You have no permission');
            }
        }
        // 设置面包屑导航数据

        $breadcrumb = $this->auth->getBreadCrumb($path);
        array_pop($breadcrumb);
        Admin::setAssign(
            [
                'auth'       => $this->auth,
                'breadcrumb' =>$breadcrumb
            ]
        );
        // 定义是否Addtabs请求
        !defined('IS_ADDTABS') && define('IS_ADDTABS', $request->input("addtabs") ? TRUE : FALSE);
        // 定义是否Dialog请求
        !defined('IS_DIALOG') && define('IS_DIALOG', $request->input("dialog") ? TRUE : FALSE);
        // 定义是否AJAX请求
        !defined('IS_AJAX') && define('IS_AJAX', isAjax());
        // 非选项卡时重定向
        // dd(!isPost() && !IS_AJAX && !IS_ADDTABS && !IS_DIALOG && $request->input("ref") == 'addtabs');
        if (!IS_AJAX && !IS_ADDTABS && !IS_DIALOG && $request->input("ref") == 'addtabs') {
            $url = preg_replace_callback("/([\?|&]+)ref=addtabs(&?)/i", function ($matches) {
                return $matches[2] == '&' ? $matches[1] : '';
            }, $request->url());
            if (config('admin.url_domain_deploy')) {
                if (stripos($url, $request->server('SCRIPT_NAME')) === 0) {
                    $url = substr($url, strlen($request->server('SCRIPT_NAME')));
                }
                $url = url($url, '', false);
            }
            return redirect(url('admin/index/index'))->with(['referer' => $url]);
        }
        return $next($request);
    }
}