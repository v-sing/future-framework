<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/24
 * Time: 16:59
 */

namespace Future\Admin\Middleware;

use Closure;

use Illuminate\Support\Facades\Session;
use Future\Admin\Auth\Database\Config;
use Future\Admin\Facades\Admin;

/**
 *
 * 初始化框架需要的配置
 * Class Initialization
 * @package Future\Admin\Middleware
 */
class Initialization
{
    /**
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        $route = getRealRoute();
        Admin::setAction($route['action']);
        Admin::setController($route['controller']);
        Admin::setModule($route['module']);
        $this->config($request);
        loadLang($route['controller']);
        return $next($request);
    }

    /**
     * 设置公共参数
     * @param $request
     */
    private function config($request)
    {
        $config = Config::get()->toArray();
        foreach ($config as $key => $value) {
            if (in_array($value['type'], ['selects', 'checkbox', 'images'])) {
                $value['value'] = explode(',', $value['value']);
            }
            if (in_array($value['type'], ['array'])) {
                $value['value'] = json_decode($value['value'], true);
            }
            if ($value['group'] == 'upload') {
                config([$value['group'] . '.' . $value['name'] => $value['value']]);
            } else {
                config(['site.' . $value['name'] => $value['value']]);
            }

        }
        if (config('app.debug')) {
            config(['site.version' => time()]);
        }
        $modulename     = Admin::module();
        $controllername = Admin::controller();
        $actionname     = Admin::action();
        $lang           = config('site.languages.backend');
        if (!$lang) {
            $lang = 'zh-CN';
        }
        if (Session::get('change_lang')) {
            $lang = Session::get('change_lang');
        }
        config(['app.locale' => $lang]);
        trans()->setLocale($lang);
        $config = [
            'site'           => array_intersect_key(config('site'), array_flip(['name', 'indexurl', 'cdnurl', 'version', 'timezone', 'languages'])),
            'modulename'     => $modulename,
            'controllername' => $controllername,
            'actionname'     => $actionname,
            'jsname'         => 'backend/' . str_replace('.', '/', $controllername),
            'moduleurl'      => rtrim(url("/{$modulename}", false), '/'),
            'language'       => $lang,
            'admin'          => config('admin'),
            'referer'        => Session::get("referer"),
            'upload'         => config('upload'),
            'app_debug'      => config('app.debug')
        ];
        config(['app.timezone' => $config['site']['timezone']]);

        Admin::setAssign([
            'config' => $config,
            'site'   => config('site'),
            'admin'  => Session::get('admin') ? array_merge(Session::get('admin'), ['cdnurl' => $config['site']['cdnurl']]) : []
        ]);
    }

}