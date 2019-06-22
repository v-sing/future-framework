<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/26 0026
 * Time: 16:36
 */

namespace Future\Admin\Controllers;

use Future\Admin\Auth\Database\Config;
use Illuminate\Http\Request;

class ConfigController extends BackendController
{
    protected $model = null;
    protected $noNeedRight = ['check'];

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->model = Model('Config');
    }

    /**
     * 系统配置
     */
    public function index()
    {
        $siteList = [];
        $groupList = Config::getGroupList();
        foreach ($groupList as $k => $v) {
            $siteList[$k]['name'] = $k;
            $siteList[$k]['title'] = $v;
            $siteList[$k]['list'] = [];
        }
        foreach ($this->model->get()->toArray() as $k => $v) {

            if (!isset($siteList[$v['group']])) {
                continue;
            }
            $value = $v;
            $value['title'] = $value['title'];
            if (in_array($value['type'], ['select', 'selects', 'checkbox', 'radio'])) {
                $value['value'] = explode(',', $value['value']);
            }
            $value['content'] = json_decode($value['content'], TRUE);
            $siteList[$v['group']]['list'][] = $value;
        }
        $index = 0;
        foreach ($siteList as $k => &$v) {
            $v['active'] = !$index ? true : false;
            $index++;
        }

        $assign = [
            'siteList'  => $siteList,
            'typeList'  => Config::getTypeList(),
            'groupList' => Config::getGroupList()
        ];
        return $this->view($assign);
    }

    /**
     * 添加
     */
    public function add()
    {
        $params = input('row');
        if ($params) {
            foreach ($params as $k => &$v) {
                $v = is_array($v) ? implode(',', $v) : $v;
            }
            try {
                if (in_array($params['type'], ['select', 'selects', 'checkbox', 'radio', 'array'])) {
                    $params['content'] = json_encode(Config::decode($params['content']), JSON_UNESCAPED_UNICODE);
                } else {
                    $params['content'] = '';
                }
                $result = $this->model->insert($params);
                if ($result !== false) {
                    try {

                        return success();
                    } catch (Exception $e) {
                        return error($e->getMessage());
                    }
                } else {
                    return error();
                }
            } catch (Exception $e) {
                return error($e->getMessage());
            }
        }
        return error(lang('Parameter %s can not be empty', ''));
    }

    /**
     * 更新
     */
    public function edit()
    {
        $row = input('row');
        if ($row) {
            $configList = [];
            foreach ($this->model->get() as $v) {
                if (isset($row[$v->name])) {
                    $value = $row[$v->name];
                    if (is_array($value) && isset($value['field'])) {
                        $value = json_encode(Config::getArrayData($value), JSON_UNESCAPED_UNICODE);
                    } else {
                        $value = is_array($value) ? implode(',', $value) : $value;
                    }
                    $v->value = $value;

                    $v->save();
                }
            }
            try {
                return success();
            } catch (Exception $e) {
                return error($e->getMessage());
            }
        }
        return error(lang('Parameter %s can not be empty', ''));
    }

    /**
     * 验证
     */
    public function check()
    {
        $params = input('row');

        if ($params) {
            $config = $this->model->where($params)->first();
            if (!$config) {
                return success();
            } else {
                return error('Name already exist');
            }
        } else {
            return error('Invalid parameters');
        }
    }
}