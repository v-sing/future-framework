<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/24
 * Time: 16:22
 */

namespace Future\Admin\Controllers;

use Future\Admin\Facades\Admin;

class AdminlogController extends BackendController
{
    protected $model;
    protected $childrenGroupIds = [];
    protected $childrenAdminIds = [];

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = model('AdminLog');
        $this->middleware('admin.adminController');
    }

    /**
     * 日志
     */
    public function index()
    {
        if (isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('id,username,nickname,email,status,logintime');
            $childrenAdminIds = Admin::getAssign('childrenAdminIds');
            $total            = $this->model
                ->where($where)
                ->whereIn('admin_id', $childrenAdminIds)
                ->orderby($sort, $order)
                ->count();
            $list             = $this->model
                ->where($where)
                ->whereIn('admin_id', $childrenAdminIds)
                ->orderby($sort, $order)
                ->offset($offset)
                ->limit($limit)
                ->get();
            $result           = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view();
    }

    /**
     * 日志详情
     */
    public function detail()
    {
        $row = $this->model->where(['id' => input('ids')])->first();
        if (!$row)
            $this->error(lang('No Results were found'));
        $this->assign("row", $row->toArray());
        return $this->view();
    }
}