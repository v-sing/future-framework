<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/4/8 0008
 * Time: 11:22
 */

namespace Future\Admin\Controllers;

use Future\Admin\Future\Random;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class ProfileController extends BackendController
{

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('AdminLog');
    }

    /**
     * 日志
     * @return \Illuminate\Http\JsonResponse|mixed|\think\response\Json
     */
    public function index()
    {
        if (isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('title,url');

            $total = $this->model
                ->where($where)
                ->where('admin_id', $this->auth->id)
                ->orderby($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where('admin_id', $this->auth->id)
                ->orderby($sort, $order)
                ->offset($offset)
                ->limit($limit)
                ->get();

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        } else {
            return $this->view();
        }


    }

    /**
     * 更新
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\think\response\Redirect
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function update()
    {
        $params = input('row');
        $params = array_filter(array_intersect_key($params, array_flip(array('email', 'nickname', 'password', 'avatar'))));
        if (isset($params['password'])) {
            $params['salt']     = Random::alnum();
            $params['password'] = Hash::make($params['password'] . $params['salt']);
        }
        if (!empty($params)) {
            $admin  = Model('Admin')->find($this->auth->id);
            $admin->data($params)->save();
            Session::put('admim', $admin->toArray());
            return success();
        }
        return error();
    }
}