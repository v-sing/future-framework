<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/6/11 0011
 * Time: 11:50
 */

namespace Future\Admin\Controllers;

use Future\Admin\Facades\Admin;
use Future\Admin\Auth\Database\AuthGroup;
use Future\Admin\Future\Tree;

class GroupController extends BackendController
{
    protected $model;

    protected $noNeedRight = ['roletree'];

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = model('AuthGroup');
        $this->middleware('admin.adminController');

        $html = '    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">' . lang('Permission') . ':</label>
        <div class="col-xs-12 col-sm-8">
            <span class="text-muted"><input type="checkbox" name="" id="checkall" /> <label for="checkall"><small>' . lang('Check all') . '</small></label></span>
            <span class="text-muted"><input type="checkbox" name="" id="expandall" /> <label for="expandall"><small>' . lang('Expand all') . '</small></label></span>
            <div id="treeview"></div>
        </div>
        </div>';
        $this->assign('html', $html);
    }

    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
        if (isAjax()) {
            $groupdata = Admin::getAssign('groupdata');
            $list      = AuthGroup::whereIn('id', array_keys($groupdata))->get()->toArray();
            $groupList = [];
            foreach ($list as $k => $v) {
                $groupList[$v['id']] = $v;
            }
            $list = [];
            foreach ($groupdata as $k => $v) {
                if (isset($groupList[$k])) {
                    $groupList[$k]['name'] = $v;
                    $list[]                = $groupList[$k];
                }
            }
            $total  = count($list);
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view();
    }

    /**
     * 删除
     */
    public function del()
    {
        $ids=input('ids');
        if ($ids) {
            $auth      = Admin::getAssign('auth');
            $ids       = explode(',', $ids);
            $grouplist = $auth->getGroups();
            $group_ids = array_map(function ($group) {
                return $group['id'];
            }, $grouplist);
            // 移除掉当前管理员所在组别
            $ids = array_diff($ids, $group_ids);

            // 循环判断每一个组别是否可删除
            $grouplist        = $this->model->whereIn('id', $ids)->get()->toArray();
            $groupaccessmodel = Model('AuthGroupAccess');
            foreach ($grouplist as $k => $v) {
                // 当前组别下有管理员
                $groupone = $groupaccessmodel->where(['group_id' => $v['id']])->get()->toArray();
                if ($groupone) {
                    $ids = array_diff($ids, [$v['id']]);
                    continue;
                }
                // 当前组别下有子组别
                $groupone = $this->model->where(['pid' => $v['id']])->get()->toArray();
                if ($groupone) {
                    $ids = array_diff($ids, [$v['id']]);
                    continue;
                }
            }
            if (!$ids) {
                return $this->error(lang('You can not delete group that contain child group and administrators'));
            }
            $count = $this->model->whereIn('id', $ids)->delete();
            if ($count) {
                return $this->success();
            }
        }
        return $this->error();
    }

    /**
     * 获取权限
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\think\response\Redirect
     * @throws \think\exception\DbException
     */
    public function roletree()
    {

        $model            = Model('AuthGroup');
        $id               = input("id");
        $pid              = input("pid");
        $parentGroupModel = $model->where('id', $pid)->first();
        if ($id) {
            $currentGroupModel = $model->where('id', $id)->first();

        }
        if (($pid || $parentGroupModel) && (!$id || $currentGroupModel)) {
            $id       = $id ? $id : null;
            $ruleList = model('AuthRule')->orderBy('weigh', 'desc')->orderBy('id', 'asc')->get();
            //读取父类角色所有节点列表
            $parentRuleList = [];
            if (in_array('*', explode(',', $parentGroupModel->rules))) {
                $parentRuleList = $ruleList;
            } else {
                $parentRuleIds = explode(',', $parentGroupModel->rules);
                foreach ($ruleList as $k => $v) {
                    if (in_array($v['id'], $parentRuleIds)) {
                        $parentRuleList[] = $v;
                    }
                }
            }
            $parentRuleList = toArray($parentRuleList);
            $ruleTree       = new Tree();
            $groupTree      = new Tree();
            //当前所有正常规则列表
            $ruleTree->init($parentRuleList);
            //角色组列表
            $groupTree->init(model('AuthGroup')->whereIn('id', Admin::getAssign('childrenGroupIds'))->get()->toArray());
            $auth = Admin::getAssign('auth');
            //读取当前角色下规则ID集合
            $adminRuleIds = $auth->getRuleIds();
            //是否是超级管理员
            $superadmin = $auth->isSuperAdmin();
            //当前拥有的规则ID集合
            $currentRuleIds = $id ? explode(',', $currentGroupModel->rules) : [];

            if (!$id || !in_array($pid, Admin::getAssign('childrenGroupIds')) || !in_array($pid, $groupTree->getChildrenIds($id, true))) {
                $parentRuleList = $ruleTree->getTreeList($ruleTree->getTreeArray(0), 'name');
                $hasChildrens   = [];

                foreach ($parentRuleList as $k => $v) {
                    if ($v['haschild']) {
                        $hasChildrens[] = $v['id'];
                    }
                }
                $parentRuleIds = array_map(function ($item) {
                    return $item['id'];
                }, $parentRuleList);
                $nodeList      = [];

                foreach ($parentRuleList as $k => $v) {
                    if (!$superadmin && !in_array($v['id'], $adminRuleIds)) {
                        continue;
                    }
                    if ($v['pid'] && !in_array($v['pid'], $parentRuleIds)) {
                        continue;
                    }
                    $state      = array('selected' => in_array($v['id'], $currentRuleIds) && !in_array($v['id'], $hasChildrens));
                    $nodeList[] = array('id' => $v['id'], 'parent' => $v['pid'] ? $v['pid'] : '#', 'text' => lang($v['title']), 'type' => 'menu', 'state' => $state);
                }
                return $this->success('', $nodeList);
            } else {
                return $this->error(lang('Can not change the parent to child'));
            }
        } else {
            return $this->error(lang('Group not found'));
        }
    }
}