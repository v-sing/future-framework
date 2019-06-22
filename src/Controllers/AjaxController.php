<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/5 0005
 * Time: 17:45
 */

namespace Future\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Future\Admin\Future\Random;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
class AjaxController extends BackendController
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    /**
     * 返回语言
     * @return \Illuminate\Http\JsonResponse|\think\response\Jsonp
     */
    public function lang(Request $request)
    {
        $controller = $request->input('controllername');
        $this->loadLang($controller);
        return jsonp('define', config('ajax.lang'));
    }

    public function upload()
    {
        $fileCharater = $this->request->file('file');
        if ($fileCharater->isValid()) {
            //获取文件的扩展名
            $ext           = $fileCharater->getClientOriginalExtension();
            $upload        = config('upload');
            $systemMaxSize = ini_get('upload_max_filesize');
            preg_match('/(\d+)(\w+)/', $upload['maxsize'], $matches);
            preg_match('/(\d+)(\w+)/', $systemMaxSize, $matches1);
            $type       = strtolower($matches[2]);
            $systemType = strtolower($matches1[2]);
            $typeDict   = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
            $fileSize   = $fileCharater->getClientSize();
            $size       = (int)$upload['maxsize'] * pow(1024, isset($typeDict[$type]) ? $typeDict[$type] : 0);
            $systemSize = (int)$systemMaxSize * pow(1024, isset($typeDict[$systemType]) ? $typeDict[$systemType] : 0);
            if ($fileSize > $systemSize) {
                return error(lang('System max size error', $systemMaxSize));
            }
            if ($fileSize > $size) {
                return error(lang('Max size error', $upload['maxsize']));
            }
            $fileName   = $fileCharater->getClientOriginalName();
            $path       = $fileCharater->getRealPath();
            $mimetype   = $fileCharater->getMimeType();
            $replaceArr = [
                '{year}'     => date("Y"),
                '{mon}'      => date("m"),
                '{day}'      => date("d"),
                '{hour}'     => date("H"),
                '{min}'      => date("i"),
                '{sec}'      => date("s"),
                '{random}'   => Random::alnum(16),
                '{random32}' => Random::alnum(32),
                '{filename}' => $ext ? substr($fileName, 0, strripos($fileName, '.')) : $fileName,
                '{suffix}'   => $ext,
                '{.suffix}'  => $ext ? '.' . $ext : '',
                '{filemd5}'  => md5_file($path),
            ];
            $savekey    = $upload['savekey'];
            $savekey    = str_replace(array_keys($replaceArr), array_values($replaceArr), $savekey);
            if (count(Model('Attachment')->where(['url' => $savekey])->first()) > 0) {
                return success('Upload successful', ['url' => $savekey, 'fullurl' => get_upload_image($savekey)]);
            }
            $bool = Storage::disk(config('upload.disks'))->put($savekey, file_get_contents($path));
            if ($bool) {
                $imagewidth = $imageheight = 0;
                if (in_array($ext, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'])) {
                    $imgInfo     = getimagesize($path);
                    $imagewidth  = isset($imgInfo[0]) ? $imgInfo[0] : $imagewidth;
                    $imageheight = isset($imgInfo[1]) ? $imgInfo[1] : $imageheight;
                }
                $params     = array(
                    'admin_id'    => (int)$this->auth->id,
                    'user_id'     => 0,
                    'filesize'    => $size,
                    'imagewidth'  => $imagewidth,
                    'imageheight' => $imageheight,
                    'imagetype'   => $ext,
                    'imageframes' => 0,
                    'mimetype'    => $mimetype,
                    'url'         => $savekey,
                    'uploadtime'  => time(),
                    'storage'     => config('upload.disks'),
                    'sha1'        => $fileCharater->isValid(),
                );
                $attachment = Model("Attachment");
                $attachment->data($params);
                $result = $attachment->save();
                if ($result) {
                    return success('Upload successful', ['url' => $savekey, 'fullurl' => get_storage_image($savekey, config('upload.disks'), $mimetype)]);
                } else {
                    return error();
                }
            } else {
                return error();
            }
        }
    }

    //初始化语言包
    protected function loadLang($controller)
    {
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
        config(['ajax.lang' => $array]);
    }

    /**
     * 发送测试邮件
     */
    public function emailtest()
    {

    }

    /**
     * 清除缓存
     */
    public function wipecache()
    {
        $type = input('type');

        switch ($type) {
            case 'all':
                Artisan::call('cache:clear');
                Artisan::call('view:clear');
                Artisan::call('config:clear');
                Artisan::call('route:clear');
            case 'content':
                Artisan::call('cache:clear');
                break;
            case 'template':
                Artisan::call('view:clear');
            case 'config':
                Artisan::call('config:clear');
                break;
            case 'route':
                Artisan::call('route:clear');
                break;

        }
        return success();
    }

    public function weigh()
    {
        $input=input();
        $ids=$input['ids'];
        $changeid=$input['changeid'];
        $field=$input['field'];
        $table=$input['table'];
        $orderway=strtolower($input['orderway']);
        $orderway = $orderway == 'asc' ? 'ASC' : 'DESC';
        $sour = $weighdata = [];
        $ids = explode(',', $ids);
        $prikey = 'id';
        $pid=$input['pid'];
        $field = in_array($field, ['weigh']) ? $field : 'weigh';

        // 如果设定了pid的值,此时只匹配满足条件的ID,其它忽略
        if ($pid !== '') {
            $hasids = [];
            $list =  DB::table($table)->whereIn($prikey, $ids)->whereIn('pid', [$pid])->select('id','pid')->get();
            foreach ($list as $k => $v) {
                $hasids[] = $v->id;
            }
            $ids = array_values(array_intersect($ids, $hasids));
        }
        $list =  DB::table($table)->select($prikey,$field)->whereIn($prikey, $ids)->orderBy($field, $orderway)->get();
        foreach ($list as $k => $v) {
            $sour[] = $v->$prikey;
            $weighdata[$v->$prikey] = $v->$field;
        }

        $position = array_search($changeid, $ids);
        $desc_id = $sour[$position];    //移动到目标的ID值,取出所处改变前位置的值
        $sour_id = $changeid;
        $weighids = array();
        $temp = array_values(array_diff_assoc($ids, $sour));
        foreach ($temp as $m => $n) {
            if ($n == $sour_id) {
                $offset = $desc_id;
            } else {
                if ($sour_id == $temp[0]) {
                    $offset = isset($temp[$m + 1]) ? $temp[$m + 1] : $sour_id;
                } else {
                    $offset = isset($temp[$m - 1]) ? $temp[$m - 1] : $sour_id;
                }
            }
            $weighids[$n] = $weighdata[$offset];
            DB::table($table)->where($prikey, $n)->update([$field => $weighdata[$offset]]);
        }
        return success();
    }
}