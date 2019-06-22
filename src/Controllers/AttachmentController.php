<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/28
 * Time: 21:26
 */

namespace Future\Admin\Controllers;

use Illuminate\Support\Facades\DB;

/**
 * 附件管理
 *
 * @icon fa fa-circle-o
 * @remark 主要用于管理上传到又拍云的数据或上传至本服务的上传数据
 */
class AttachmentController extends BackendController
{


    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Attachment');
    }

    public function index()
    {
        $mimetypeQuery = [];
        if (isAjax()) {
            $filter    = input('filter');
            $filterArr = (array)json_decode($filter, TRUE);
            if (isset($filterArr['mimetype']) && stripos($filterArr['mimetype'], ',') !== false) {
                $this->request->merge(['filter' => json_encode(array_merge($filterArr, ['mimetype' => '']))]);
                $mimetypeQuery = function ($query) use ($filterArr) {
                    $mimetypeArr = explode(',', $filterArr['mimetype']);
                    foreach ($mimetypeArr as $index => $item) {
                        $query->whereOr('mimetype', 'like', '%' . $item . '%');
                    }
                };
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($mimetypeQuery)
                ->where($where)
                ->orderby($sort, $order)
                ->count();

            $list = $this->model
                ->where($mimetypeQuery)
                ->where($where)
                ->orderby($sort, $order)
                ->offset($offset)
                ->limit($limit)
                ->get();

            $cdnurl = preg_replace("/\/(\w+)\.php$/i", '', $this->request->root());
            foreach ($list as $k => &$v) {
                $v['fullurl'] =get_upload_image($v['url']);
            }
            unset($v);
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        } else {
            return $this->view();
        }
    }

    /**
     * 选择图片
     */
    public function select()
    {
        if (isAjax()) {
            return $this->index();
        }
        return $this->view();
    }

    public function add()
    {
        return $this->view();
    }

    public function detail()
    {
        return $this->view();
    }
}