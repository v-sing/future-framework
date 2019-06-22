<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/22 0022
 * Time: 10:47
 */

use Illuminate\Support\Facades\Request;
use Future\Admin\Facades\Admin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

if (!function_exists('lang')) {
    function lang($name, $vars = [])
    {
        if ($name == '' || !is_string($name)) {
            return $name;
        }
        $name  = trim($name);
        $array = config('admin.lang');
        if (!$array) {
            loadLang();
            $array = config('admin.lang');
        }
        $name = isset($array[$name]) ? $array[$name] : $name;
        if (!is_array($vars)) {
            $vars = func_get_args();
            array_shift($vars);
        }
        if (!empty($vars)) {
            foreach ($vars as $var) {
                $name = sprintf($name, $var);
            }
        }
        return $name;
    }
}
if (!function_exists('loadLang')) {
    /**
     * 初始化语言包
     * @param $controller
     */
    function loadLang($controller = '')
    {
        if ($controller == '') {
            $backtrace = debug_backtrace();
            preg_match('/([\w]+)Controller$/', pathinfo($backtrace[1]['file'], PATHINFO_FILENAME), $ma);
            $controller = strtolower($ma[1]);
        }

        $add   = trans('admin_vendor' . '::' . $controller);
        $array = [];
        if (is_array($add)) {
            $array = trans('admin_vendor' . '::' . $controller);
        }
        if (empty($array)) {
            $add = trans('admin' . '::' . $controller);
            if (is_array($add)) {
                $array = trans('admin' . '::' . $controller);
            }
        }

        $array = array_merge(trans('admin_vendor::' . config('app.locale')), $array);
        config(['admin.lang' => $array]);

    }
}
if (!function_exists('toArray')) {
    /**
     * 对象转数组
     * @param $data
     * @return mixed
     */
    function toArray($array)
    {
        if (is_object($array)) {
            $array = json_decode(json_encode($array, true), true);
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = toArray($value);
            }
        }
        return $array;
    }
}

if (!function_exists('error')) {
    /**
     * @param string $msg
     * @param array $data
     * @param string $url
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\think\response\Redirect
     */

    function error($msg = 'Operation failed', $data = [], $url = '')
    {
        $data = [
            'code' => 0,
            'msg'  => lang($msg),
            'data' => $data,
            'url'  => $url
        ];
        if (isPost() || isAjax()) {
            return response()->json($data);
        } else {
            if ($url == '') {
                $data['url'] = Request::url();
            } else {
                $data['url'] = url($data['url']);
            }
            return redirect('admin/message')->with('msg', $data);
        }
    }
}

if (!function_exists('success')) {
    /**
     * @param string $msg
     * @param array $data
     * @param string $url
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\think\response\Redirect
     */
    function success($msg = 'Operation completed', $data = [], $url = '')
    {
        $data = [
            'code' => 1,
            'msg'  => lang($msg),
            'data' => $data,
            'url'  => $url
        ];
        if (isPost() || isAjax()) {
            return response()->json($data);
        } else {
            if ($url == '') {
                $data['url'] = isset($data['data']['url']) ? $data['data']['url'] : Request::url();
            } else {
                $data['url'] = url($data['url']);
            }
            return redirect('admin/message')->with('msg', $data);
        }
    }
}
if (!function_exists('isAjax')) {
    /**
     * @return bool
     */
    function isAjax()
    {
        $HTTP_X_REQUESTED_WITH = request()->server('HTTP_X_REQUESTED_WITH');
        return isset($HTTP_X_REQUESTED_WITH) && strtoupper($HTTP_X_REQUESTED_WITH) == 'XMLHTTPREQUEST';

    }
}

if (!function_exists('isPost')) {
    /**
     * @return bool
     */
    function isPost()
    {
        $REQUEST_METHOD = request()->server('REQUEST_METHOD');
        return isset($REQUEST_METHOD) && strtoupper($REQUEST_METHOD) == 'POST';
    }
}

if (!function_exists('isGet')) {
    /**
     * @return bool
     */
    function isGet()
    {
        $REQUEST_METHOD = request()->server('REQUEST_METHOD');
        return isset($REQUEST_METHOD) && strtoupper($REQUEST_METHOD) == 'GET';
    }
}

if (!function_exists('jsonp')) {
    /**
     *
     * @param $callback
     * @param array $data
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\JsonResponse
     */
    function jsonp($callback, $data = [], $status = 200, array $headers = [], $options = 0)
    {
        return response()->jsonp($callback, $data, $status, $headers, $options);
    }
}

if (!function_exists('admin_path')) {

    /**
     * Get admin path.
     *
     * @param string $path
     *
     * @return string
     */
    function admin_path($path = '')
    {
        return ucfirst(config('admin.directory')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('admin_url')) {
    /**
     * Get admin url.
     *
     * @param string $path
     * @param mixed $parameters
     * @param bool $secure
     *
     * @return string
     */
    function admin_url($path = '', $parameters = [], $secure = null)
    {
        if (\Illuminate\Support\Facades\URL::isValidUrl($path)) {
            return $path;
        }

        $secure = $secure ?: (config('admin.https') || config('admin.secure'));

        return url(admin_base_path($path), $parameters, $secure);
    }
}

if (!function_exists('admin_base_path')) {
    /**
     * Get admin url.
     *
     * @param string $path
     *
     * @return string
     */
    function admin_base_path($path = '')
    {
        $prefix = '/' . trim(config('admin.route.prefix'), '/');

        $prefix = ($prefix == '/') ? '' : $prefix;

        $path = trim($path, '/');

        if (is_null($path) || strlen($path) == 0) {
            return $prefix ?: '/';
        }

        return $prefix . '/' . $path;
    }
}
if (!function_exists('parse_hump')) {
    /**
     * 下划线转驼峰
     * @param $underline
     * @return mixed
     */
    function parse_hump($underline)
    {
        $underline = camel_case($underline);
        $underline = ucfirst($underline);
        return $underline;
    }
}

if (!function_exists('parse_underline')) {
    /**
     * 驼峰转下划线
     * @param $hump
     * @return mixed
     */
    function parse_underline($hump)
    {
        $hump = snake_case($hump);
        $hump = strtolower($hump);
        return $hump;
    }
}

if (!function_exists('build_heading')) {

    /**
     * 生成页面Heading
     *
     * @param string $path 指定的path
     * @return string
     */
    function build_heading($path = NULL, $container = TRUE)
    {


        $title = $content = '';
        if (is_null($path)) {
            $action     = Admin::action();
            $controller = str_replace('.', '/', Admin::controller());
            $path       = strtolower($controller . ($action && $action != 'index' ? '/' . $action : ''));
        }
        // 根据当前的URI自动匹配父节点的标题和备注
        $data = Model('AuthRule')->getInfo(['name' => $path]);
        if ($data) {
            $title   = lang($data['title']);
            $content = lang($data['remark']);
        }
        if (!$content)
            return '';
        $result = '<div class="panel-lead"><em>' . $title . '</em>' . $content . '</div>';
        if ($container) {
            $result = '<div class="panel-heading">' . $result . '</div>';
        }
        return $result;
    }
}
if (!function_exists('Model')) {
    /**
     * 数据库层
     * @param $model
     * @return mixed
     */
    function Model($model)
    {
        $model = parse_hump($model);
        if (!class_exists($class = '\\App\\Model\\' . $model)) {
            $class = '\\Future\\Admin\\Auth\\Database\\' . $model;
        }
        return new $class;
    }
}

if (!function_exists('admin_error')) {

    /**
     * Flash a error message bag to session.
     *
     * @param string $title
     * @param string $message
     */
    function admin_error($title, $message = '')
    {
        admin_info($title, $message, 'error');
    }
}

if (!function_exists('input')) {
    /**
     * 获取输入数据 支持默认值和过滤
     * @param string $key 获取的变量名
     * @param mixed $default 默认值
     * @param string $filter 过滤方法
     * @return mixed
     */

    function input($key = '', $default = '', $filter = '')
    {
        if ($key != '' || $key != null) {
            $result = Request::input($key, $default);
        } else {
            $result = Request::all();
        }
        return replace_null($result);
    }
}

if (!function_exists('build_toolbar')) {

    /**
     * 生成表格操作按钮栏
     * @param array $btns 按钮组
     * @param array $attr 按钮属性值
     * @return string
     */
    function build_toolbar($btns = NULL, $attr = [])
    {
        $auth       = \Future\Admin\Auth\Auth::instance();
        $controller = str_replace('.', '/', strtolower(Admin::controller()));
        $btns       = $btns ? $btns : ['refresh', 'add', 'edit', 'del', 'import'];
        $btns       = is_array($btns) ? $btns : explode(',', $btns);
        $index      = array_search('delete', $btns);
        if ($index !== FALSE) {
            $btns[$index] = 'del';
        }
        $btnAttr = [
            'refresh' => ['javascript:;', 'btn btn-primary btn-refresh', 'fa fa-refresh', '', lang('Refresh')],
            'add'     => ['javascript:;', 'btn btn-success btn-add', 'fa fa-plus', lang('Add'), lang('Add')],
            'edit'    => ['javascript:;', 'btn btn-success btn-edit btn-disabled disabled', 'fa fa-pencil', lang('Edit'), lang('Edit')],
            'del'     => ['javascript:;', 'btn btn-danger btn-del btn-disabled disabled', 'fa fa-trash', lang('Delete'), lang('Delete')],
            'import'  => ['javascript:;', 'btn btn-danger btn-import', 'fa fa-upload', lang('Import'), lang('Import')],
        ];
        $btnAttr = array_merge($btnAttr, $attr);
        $html    = [];
        foreach ($btns as $k => $v) {
            //如果未定义或没有权限
            if (!isset($btnAttr[$v]) || ($v !== 'refresh' && !$auth->check("{$controller}/{$v}"))) {
                continue;
            }
            list($href, $class, $icon, $text, $title) = $btnAttr[$v];
            $extend = $v == 'import' ? 'id="btn-import-file" data-url="ajax/upload" data-mimetype="csv,xls,xlsx" data-multiple="false"' : '';
            $html[] = '<a href="' . $href . '" class="' . $class . '" title="' . $title . '" ' . $extend . '><i class="' . $icon . '"></i> ' . $text . '</a>';
        }
        return implode(' ', $html);
    }
}
if (!function_exists('parseName')) {
    /**
     * 字符串命名风格转换
     * type 0 将 Java 风格转换为 C 的风格 1 将 C 风格转换为 Java 的风格
     * @param $name
     * @param int $type
     * @param bool $ucfirst
     * @return string
     */
    function parseName($name, $type = 0, $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);

            return $ucfirst ? ucfirst($name) : lcfirst($name);
        }

        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }

}

if (!function_exists('json')) {
    function json($data)
    {

        return response()->json($data);
    }
}

if (!function_exists('get_upload_imgae')) {
    /**
     * 获取图片
     * 配合数据库
     * @param string $path
     * @param int $type 0 路径 1 id
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    function get_upload_image($path = '')
    {
        $array = explode(',', $path);

        $base64Array = [];
        foreach ($array as $param) {
            $result = get_table_image($param);
            if (!$result) {
                $base64Array[] = get_storage_image();
            } else {
                $base64Array[] = get_storage_image($result['url'], $result['storage'], $result['mimetype']);
            }
        }
        return implode('|', $base64Array);
    }
}
if (!function_exists('get_table_image')) {
    /**
     *
     * @param $param
     * @return bool|mixed
     */
    function get_table_image($param)
    {
        $result = Cache::get('image-' . $param);
        if (!$result) {
            $result = Model('Attachment')->where(function ($query) use ($param) {
                $query->where(['id' => $param])->orwhere(['url' => $param]);
            })->first();
            if ($result) {
                Cache::put('image-' . $param, $result->toArray(), 60 * 60 * 12);
            } else {
                $result = false;
            }
        }
        return $result;
    }
}
if (!function_exists('get_storage_image')) {
    /**
     *
     * @param string $url
     * @param string $storage
     * @param string $mime_type
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    function get_storage_image($url = '', $storage = 'local', $mime_type = 'image/png')
    {
        if (!($default = Cache::get('default-image'))) {
            $default = file_get_contents(str_replace('%s', config('admin.default_image'), config('admin.default_image_url')));
            Cache::put('default-image', $default);
        }
        if (!$url) {
            $mime_type = 'image/png';
            $base64    = $default;
        } else {
            $ex = pathinfo($url, PATHINFO_EXTENSION);
            if (Storage::disk($storage)->exists($url)) {
                $result = Storage::disk($storage)->get($url);
                if (isImg($result)) {
                    if ($result) {
                        $base64 = $result;
                    }
                } else {
                    if (!($extension = Cache::get('Storage-get_storage_image-' . $ex))) {
                        $extension = file_get_contents(str_replace('%s', $ex, config('admin.default_image_url')));
                        Cache::get('Storage-get_storage_image-' . $ex, $extension);
                    }
                    $mime_type = 'image/png';
                    $base64    = $extension;
                }

            } else {
                $result = @file_get_contents('.' . $url);
                if ($result) {
                    $base64 = $result;
                } else {
                    if (!($extension = Cache::get('Storage-get_storage_image-' . $ex))) {
                        $extension = file_get_contents(str_replace('%s', $ex, config('admin.default_image_url')));
                        Cache::get('Storage-get_storage_image-' . $ex, $extension);
                    }
                    $mime_type = 'image/png';
                    $base64    = $extension;
                }
            }

        }
        if (!$base64) {
            $base64    = $default;
            $mime_type = 'image/png';
        }

        return base64EncodeImage($base64, $mime_type);
    }
}
if (!function_exists('base64EncodeImage')) {
    /**
     *
     * @param $image_file
     * @param string $mine
     * @return string
     */
    function base64EncodeImage($image_file, $mine = 'image/png')
    {
        $base64_image = 'data:' . $mine . ';base64,' . chunk_split(base64_encode($image_file));
        return $base64_image;
    }
}


if (!function_exists('replace_null')) {
    /**
     * 数组替换null
     * @param $array
     * @param string $replace_string
     * @return mixed
     */
    function replace_null(&$array, $replace_string = '')
    {
        if (!is_string($array) && isset($array) && !is_null($array)) {
            foreach ($array as $key => $value) {
                if (!is_string($value) && isset($value)) {
                    foreach ($value as $k => $v) {
                        if (is_array($value[$k])) {
                            $array[$key][$k] = replace_null($value[$v], $replace_string);
                        } else if (!isset($value[$k])) {
                            $array[$key][$k] = $replace_string;
                        }
                    }
                } else {
                    if (!isset($value)) {
                        $array[$key] = $replace_string;
                    }
                }

            }
        }

        return $array;
    }

}
if (!function_exists('isImage')) {
    /**
     *
     * @param $bin
     * @return bool
     */
    function isImg($bin)
    {

        $strInfo  = @unpack("C2chars", $bin);
        $typeCode = intval($strInfo['chars1'] . $strInfo['chars2']);
        if ($typeCode == 255216 /*jpg*/ || $typeCode == 7173 /*gif*/ || $typeCode == 13780 /*png*/) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('getRealRoute')) {
    function getRealRoute($real = '')
    {
        if ($real=='') {
            $real = toArray(Request::route())['uri'];
        }


        $app    = app();
        $routes = $app->routes->getRoutes();
        $uri    = array_column(toArray($routes), 'uri');
        $action = array_column(toArray($routes), 'action');
        foreach ($action as $k => $v) {
            if (!isset($v['prefix']) || is_null($v['prefix'])) {
                $action[$k]['prefix'] = 'web';
            }
            if (!isset($v['controller'])) {
                $action[$k]['controller'] = '';
            }
        }
        $module     = array_column($action, 'prefix');
        $controller = array_column($action, 'controller');

        $array = [];
        foreach ($controller as $key => $value) {
            if (preg_match('/([\w]+)Controller@(.*?)$/', $value, $matches)) {
                $array[$key] = [
                    'uri'        => $uri[$key],
                    'module'     => $module[$key],
                    'controller' => strtolower($matches[1]),
                    'action'     => strtolower($matches[2]),
                    'realPath'   => $module[$key] . '/' . strtolower($matches[1]) . '/' . strtolower($matches[2])
                ];
            } else {
                $array[$key] = [
                    'uri'        => $uri[$key],
                    'module'     => $module[$key],
                    'controller' => '',
                    'action'     => '',
                    'realPath'   => $module[$key] . $key
                ];
            }
        }
        $index = array_search($real, $uri);
        return $array[$index];

    }
}