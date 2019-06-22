<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/4/17 0017
 * Time: 15:02
 */

namespace Future\Admin\Controllers;


use Future\Admin\Future\Loader;
use Illuminate\Http\Request;
use Future\Admin\Facades\Admin;
use Illuminate\Support\Facades\DB;
use Future\Admin\Future\Random;
use Illuminate\Support\Facades\Hash;

class AdminController extends BackendController
{
    /**
     * @var null
     */
    protected $model = null;
    protected $childrenGroupIds = [];
    protected $childrenAdminIds = [];

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = model('Admin');
        $this->middleware('admin.adminController');
    }

    public function index(Request $request)
    {
        if (isAjax()) {
            $childrenAdminIds = Admin::getAssign('childrenAdminIds');
            $adminGroupName   = $this->auth->getGroupsAccessAll();
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('id,username,nickname,email,status,logintime');
            $total = $this->model
                ->where($where)
                ->whereIn('id', $childrenAdminIds)
                ->orderby($sort, $order)
                ->count();
            $list  = $this->model
                ->where($where)
                ->whereIn('id', $childrenAdminIds)
                ->orderby($sort, $order)
                ->offset($offset)
                ->limit($limit)
                ->get()->toArray();
            foreach ($list as $k => &$v) {
                unset($v['password']);
                unset($v['token']);
                unset($v['salt']);
                $groups           = isset($adminGroupName[$v['id']]) ? $adminGroupName[$v['id']] : [];
                $v['groups']      = implode(',', array_keys($groups));
                $v['groups_text'] = implode(',', array_values($groups));
            }
            unset($v);
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view();
    }

    /**
     * 添加
     */
    public function add()
    {
        if (isAjax()) {
            $params = input('row');
            if ($params) {
                DB::beginTransaction();
                try {
                    $params['salt']     = Random::alnum();
                    $params['password'] = Hash::make($params['password'] . $params['salt']);
                    $params['avatar']   = '/assets/img/avatar.png'; //设置新管理员默认头像。
                    $result             = $this->model->validate('Admin.add')->data($params)->save();
                    if ($result === false) {
                        DB::rollBack();
                        return error($this->model->getError());
                    }
                    $group = input('group');
                    //过滤不允许的组别,避免越权
                    $group   = array_intersect(Admin::getAssign('childrenGroupIds'), $group);
                    $dataset = [];
                    foreach ($group as $value) {
                        $dataset[] = ['uid' => $this->model->id, 'group_id' => $value];
                    }

                    Model('AuthGroupAccess')->addAll($dataset);
                    DB::commit();
                    return success();
                } catch (\Exception $exception) {
                    DB::rollBack();
                    return error($exception);
                }

            }
            return error();
        }
        return $this->view();
    }

    /**
     * 编辑
     */
    public function edit()
    {

        $ids = input('ids');
        $row = $this->model->where(['id' => $ids])->first();
        if (!$row)
            return error(lang('No Results were found'));
        if (isAjax()) {
            $params = input('row');
            if ($params) {
                DB::beginTransaction();
                try {
                    if ($params['password']) {
                        $params['salt']     = Random::alnum();
                        $params['password'] = Hash::make($params['password'] . $params['salt']);
                    } else {
                        unset($params['password'], $params['salt']);
                    }
                    //这里需要针对username和email做唯一验证
                    $adminValidate = Loader::validate('Admin');
                    $adminValidate->rule([
                        'username' => 'require|max:50|unique:admin,username,' . $row->id,
                        'email'    => 'require|email|unique:admin,email,' . $row->id
                    ]);
                    $result = $row->validate('Admin.edit')->data($params)->save();

                    if ($result === false) {
                        DB::rollBack();
                        return error($row->getError());
                    }
                    // 先移除所有权限
                    model('AuthGroupAccess')->where('uid', $row->id)->delete();
                    $group = input('group');

                    // 过滤不允许的组别,避免越权
                    $group   = array_intersect(Admin::getAssign('childrenGroupIds'), $group);
                    $dataset = [];
                    foreach ($group as $value) {
                        $dataset[] = ['uid' => $row->id, 'group_id' => $value];
                    }
                    model('AuthGroupAccess')->addAll($dataset);
                    DB::commit();
                    return success();
                } catch (\Exception $exception) {
                    DB::rollBack();
                    return error($exception);
                }

            }
            return error();
        }
        $grouplist = $this->auth->getGroupsAccess($ids);
        $groupids = [];
        foreach ($grouplist as $k => $v) {
            if(isset($v['group_id'])){
                $groupids[] = $v['group_id'];
            }
        }
        $this->assign("row", toArray($row));
        $this->assign("groupids", $groupids);
        return $this->view();
    }
}