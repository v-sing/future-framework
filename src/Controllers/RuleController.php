<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/6/11 0011
 * Time: 13:34
 */

namespace Future\Admin\Controllers;

use Future\Admin\Facades\Admin;
use Future\Admin\Future\Tree;
use Illuminate\Support\Facades\Cache;
use Future\Admin\Auth\Database\AuthRule;
use Future\Admin\Future\Loader;
use Illuminate\Support\Facades\Session;
class RuleController extends BackendController
{
    public $model;
    public $multiFields = 'ismenu,status';
    public $rulelist = [];

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = Model('AuthRule');
        $that=$this;
        $this->middleware(function ($request, $next)use ($that) {
            if (Session::get('change_lang')) {
                trans()->setLocale(Session::get('change_lang'));
            }
            // 必须将结果集转换为数组
            $ruleList = $that->model->orderBy('weigh', 'desc')->orderBy('id', 'asc')->get()->toArray();
            foreach ($ruleList as $k => &$v) {
                $v['title']  = lang($v['title']);
                $v['remark'] = lang($v['remark']);
            }
            unset($v);
            Tree::instance()->init($ruleList);
            $that->rulelist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');
            $ruledata       = [0 => lang('None')];
            foreach ($that->rulelist as $k => &$v) {
                if (!$v['ismenu']) {
                    continue;
                }
                $ruledata[$v['id']] = $v['title'];
            }
            unset($v);
            Admin::setAssign([
                'ruledata' => $ruledata
            ]);
            return $next($request);
        });



    }

    public function index()
    {
        if (isAjax()) {
            $list   = $this->rulelist;
            $total  = count($this->rulelist);
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
                if (!$params['ismenu'] && !$params['pid']) {
                   return $this->error(lang('The non-menu rule must have parent'));
                }
                $result = $this->model->data($params)->validate("AuthRule.add")->save();
                if ($result === false) {
                 return   $this->error($this->model->getError());
                }
                Cache::forget('__menu__');
               return $this->success();
            }
           return $this->error();
        }
        return $this->view();
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $row = $this->model->where(['id' => input('ids')])->first();
        if (!$row) {
            return $this->error(lang('No Results were found'));
        }
        if (isAjax()) {
            $params = input('row');
            if ($params) {
                if (!$params['ismenu'] && !$params['pid']) {
                    return $this->error(lang('The non-menu rule must have parent'));
                }
                if ($params['pid'] != $row['pid']) {
                    $childrenIds = Tree::instance()->init(AuthRule::get()->toArray())->getChildrenIds($row->id);
                    if (in_array($params['pid'], $childrenIds)) {
                        return $this->error(lang('Can not change the parent to child'));
                    }
                }
                //这里需要针对name做唯一验证
                $ruleValidate = Loader::validate('AuthRule');
                $ruleValidate->rule([
                    'name' => 'require|format|unique:AuthRule,name,' . $row->id,
                ]);
                $result = $row->validate("AuthRule.edit")->data($params)->save();
                if ($result === false) {
                 return   $this->error($row->getError());
                }
                Cache::forget('__menu__');
             return   $this->success();
            }
          return  $this->error();
        }
        $this->assign("row", $row);
        return $this->view();
    }

    /**
     * 删除
     */
    public function del()
    {
        $ids = input('ids');
        if ($ids) {
            $delIds = [];
            foreach (explode(',', $ids) as $k => $v) {
                $delIds = array_merge($delIds, Tree::instance()->getChildrenIds($v, true));
            }
            $delIds = array_unique($delIds);
            $count  = $this->model->whereIn('id', $delIds)->delete();
            if ($count) {
                Cache::forget('__menu__');
               return $this->success();
            }
        }
      return  $this->error();
    }
}