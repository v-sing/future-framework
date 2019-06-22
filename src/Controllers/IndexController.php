<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/24
 * Time: 18:09
 */

namespace Future\Admin\Controllers;

use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class IndexController extends BackendController
{
    protected $noNeedLogin = ['login', 'logout'];
    protected $noNeedRight = ['login', 'logout'];
    protected $layout = null;

    public function index(Request $request)
    {
        //左侧菜单
        list($menulist, $navlist, $fixedmenu, $referermenu) = $this->auth->getSidebar([
            'dashboard' => 'hot',
            'addon'     => ['new', 'red', 'badge'],
            'auth/rule' => lang('Menu'),
            'general'   => ['new', 'purple'],
        ], config('site.fixedpage'));
        $action = $action = $request->input('controller')['action'];
        if (isPost()) {
            if ($action == 'refreshmenu') {
                success('', null, ['menulist' => $menulist, 'navlist' => $navlist]);
            }
        }

        $assign = ['title' => lang('Home'), 'menulist' => $menulist, 'navlist' => $navlist, 'fixedmenu' => $fixedmenu, 'referermenu' => $referermenu];
        return $this->view($assign);
    }

    /**
     * 登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed|\think\response\Redirect
     */
    public function login(Request $request)
    {
        $url = Session::get('referer') ? Session::get('referer') : url('/admin');
        if (isAjax()) {
            $rule = [
                ['username', 'require'],
                ['password', 'require']
            ];
            if (config('admin.login_captcha')) {
                $rule[] = ['captcha', 'require|captcha'];
            }
            $result = $this->validate(input(), $rule);
            if ($result !== true) {
                return error($result);
            }
            $username = $request->input('username');
            $password = $request->input('password');
            $result   = $this->auth->login($username, $password, config('site.keeptime'));
            if ($result) {
                return success(lang('Login successful'), array_merge(Session::get("admin"), ['url' => $url]));
            } else {
                return error(lang($this->auth->getError()));
            }

        }
        if ($this->auth->isLogin()) {
            return success(lang("You've logged in, do not login again"), array_merge(Session::get("admin"), ['url' => $url]));
        }
        if ($this->auth->autologin()) {
            return success(lang("Auto login"), array_merge(Session::get("admin"), ['url' => $url]));
        }
        return $this->view(['title' => lang('Login')]);
    }

    /**
     * 退出
     */
    public function logout()
    {
        $this->auth->logout();
        return success(lang('Logout successful'), [], url('admin/login'));
    }

    public function lang(Request $request)
    {
        $lang = $request->input('lang');
        if ($lang) {
            Session::put('change_lang', $lang);
        }
        return $this->success();
    }
}