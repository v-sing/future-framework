<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/24
 * Time: 17:01
 */

namespace Future\Admin\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Future\Admin\Facades\Admin;
use Illuminate\Support\Facades\DB;
use Future\Admin\Future\Exception\ValidateException;
use Future\Admin\Future\Loader;

trait Backend
{
    protected $excludeFields = ['updated_at', 'created_at'];
    /**
     * @var bool 验证失败是否抛出异常
     */
    protected $failException = false;

    /**
     * @var bool 是否批量验证
     */
    protected $batchValidate = false;

    /**
     * 设置验证失败后是否抛出异常
     * @access protected
     * @param bool $fail 是否抛出异常
     * @return $this
     */
    protected function validateFailException($fail = true)
    {
        $this->failException = $fail;

        return $this;
    }

    /**
     * 排除前台提交过来的字段
     * @param $params
     * @return array
     */
    protected function preExcludeFields($params)
    {
        if (is_array($this->excludeFields)) {
            foreach ($this->excludeFields as $field) {
                if (key_exists($field, $params)) {
                    unset($params[$field]);
                }
            }
        } else {
            if (key_exists($this->excludeFields, $params)) {
                unset($params[$this->excludeFields]);
            }
        }
        return $params;
    }


    /**
     * 验证数据
     * @access protected
     * @param  array $data 数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array $message 提示信息
     * @param  bool $batch 是否批量验证
     * @param  mixed $callback 回调方法（闭包）
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate($data, $validate, $message = [], $batch = false, $callback = null)
    {
        if (is_array($validate)) {
            $v = Loader::validate();
            $v->rule($validate);
//            var_dump($v);exit;
        } else {
            // 支持场景
            if (strpos($validate, '.')) {
                list($validate, $scene) = explode('.', $validate);
            }

            $v = Loader::validate($validate);

            !empty($scene) && $v->scene($scene);
        }

        // 批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        // 设置错误信息
        if (is_array($message)) {
            $v->message($message);
        }

        // 使用回调验证
        if ($callback && is_callable($callback)) {
            call_user_func_array($callback, [$v, &$data]);
        }
        if (!$v->check($data)) {
            if ($this->failException) {
                throw new ValidateException($v->getError());
            }

            return $v->getError();
        }

        return true;
    }

    /**
     * 向模板输出变量
     * @param $name
     * @param array $value
     */
    protected function assign($name, $value = array())
    {
        //如果name 是数组 且value 为空时
        if (is_array($name) && empty($value)) {
            $this->assign = array_merge($this->assign, $name);
        } else {
            $this->assign[$name] = $value;
        }
    }

    /**
     * 生成查询所需要的条件,排序方式
     * @param mixed $searchfields 快速查询的字段
     * @param boolean $relationSearch 是否关联查询
     * @param boolean $model 当前使用的主表model
     * @return array
     */
    protected function buildparams($searchfields = null, $relationSearch = null, $model = null)
    {
        $model          = is_null($model) ? $this->model : $model;
        $searchfields   = is_null($searchfields) ? $this->searchFields : $searchfields;
        $relationSearch = is_null($relationSearch) ? $this->relationSearch : $relationSearch;
        $search         = input("search", '');
        $filter         = input("filter", '');
        $op             = input("op", '', 'trim');
        $sort           = input("sort", "id");
        $order          = input("order", "DESC");
        $offset         = input("offset", 0);
        $limit          = input("limit", 0);
        $filter         = (array)json_decode($filter, TRUE);
        $op             = (array)json_decode($op, TRUE);
        $filter         = $filter ? $filter : [];
        $where          = [];
        $tableName      = '';
        if ($relationSearch) {
            if (!empty($model)) {
                $name      = parseName(basename(str_replace('\\', '/', get_class($model))));
                $tableName = $name . '.';
            }
            $sortArr = explode(',', $sort);
            foreach ($sortArr as $index => & $item) {
                $item = stripos($item, ".") === false ? $tableName . trim($item) : $item;
            }
            unset($item);
            $sort = implode(',', $sortArr);
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $where[] = ['in', $tableName . $this->dataLimitField, 'in', $adminIds];
        }
        if ($search) {
            $searcharr = is_array($searchfields) ? $searchfields : explode(',', $searchfields);
            foreach ($searcharr as $k => &$v) {
                $v = stripos($v, ".") === false ? $tableName . $v : $v;
            }
            unset($v);

            $where[] = ['search', implode("|", $searcharr), "LIKE", "{$search}"];
        }
        foreach ($filter as $k => $v) {
            if (stripos($k, '|') !== false) {
                if (stripos($k, ".") === false) {
                    $k = $tableName . $k;
                }
                $where[] = ['search', $k, '=', (string)$v];
                continue;
            }
            $sym = isset($op[$k]) ? $op[$k] : '=';
            if (stripos($k, ".") === false) {
                $k = $tableName . $k;
            }
            $v   = !is_array($v) ? trim($v) : $v;
            $sym = strtoupper(isset($op[$k]) ? $op[$k] : $sym);
            switch ($sym) {
                case '=':
                case '!=':
                    $where[] = ['basics', $k, $sym, (string)$v];
                    break;
                case 'LIKE':
                case 'NOT LIKE':
                case 'LIKE %...%':
                case 'NOT LIKE %...%':
                    $where[] = ['like', $k, trim(str_replace('%...%', '', $sym)), $v];
                    break;
                case '>':
                case '>=':
                case '<':
                case '<=':
                    $where[] = ['basics', $k, $sym, intval($v)];
                    break;
                case 'FINDIN':
                case 'FINDINSET':
                case 'FIND_IN_SET':
                    $where[] = ['find_id', "FIND_IN_SET('{$v}', " . ($relationSearch ? $k : '`' . str_replace('.', '`.`', $k) . '`') . ")"];
                    break;
                case 'IN':
                case 'IN(...)':
                case 'NOT IN':
                case 'NOT IN(...)':
                    $where[] = ['in', $k, str_replace('(...)', '', $sym), is_array($v) ? $v : explode(',', $v)];
                    break;
                case 'BETWEEN':
                case 'NOT BETWEEN':
                    $arr = array_slice(explode(',', $v), 0, 2);
                    if (stripos($v, ',') === false || !array_filter($arr))
                        continue;
                    //当出现一边为空时改变操作符
                    if ($arr[0] === '') {
                        $sym = $sym == 'BETWEEN' ? '<=' : '>';
                        $arr = $arr[1];
                    } else if ($arr[1] === '') {
                        $sym = $sym == 'BETWEEN' ? '>=' : '<';
                        $arr = $arr[0];
                    }
                    $where[] = ['range', $k, $sym, $arr];
                    break;
                case 'RANGE':
                case 'NOT RANGE':
                    $v   = str_replace(' - ', ',', $v);
                    $arr = array_slice(explode(',', $v), 0, 2);
                    if (stripos($v, ',') === false || !array_filter($arr))
                        continue;
                    //当出现一边为空时改变操作符
                    if ($arr[0] === '') {
                        $sym = $sym == 'RANGE' ? '<=' : '>';
                        $arr = $arr[1];
                    } else if ($arr[1] === '') {
                        $sym = $sym == 'RANGE' ? '>=' : '<';
                        $arr = $arr[0];
                    }
                    $arr[0]  = strtotime($arr[0]);
                    $arr[1]  = strtotime($arr[1]);
                    $where[] = ['range', $k, str_replace('RANGE', 'BETWEEN', $sym) . ' ', $arr];
                    break;
                case 'LIKE':
                case 'LIKE %...%':
                    $where[] = ['like', $k, 'LIKE', $v];
                    break;
                case 'NULL':
                case 'IS NULL':
                case 'NOT NULL':
                case 'IS NOT NULL':
                    $where[] = ['null', $k, strtolower(str_replace('IS ', '', $sym))];
                    break;
                default:
                    break;
            }
        }
        $where = function ($query) use ($where) {
            foreach ($where as $k => $v) {
                switch ($v[0]) {
                    case 'search';
                        $arr = explode('|', $v[1]);
                        if (stripos($v[3], '-') !== false) {
                            $v[3] = explode('-', $v[3]);
                        }
                        foreach ($arr as $k1 => $v1) {
                            if (is_array($v[3])) {
                                if (isset($v[3][$k1])) {
                                    $query->orWhere($v1, 'like', '%' . $v[3][$k1] . '%');
                                }
                            } else {
                                $query->orWhere($v1, 'like', '%' . $v[3] . '%');
                            }
                        }
                        break;
                    case 'basics';
                        $query->where($v[1], $v[2], $v[3]);
                        break;
                    case 'find_id';
                        $query->whereRaw($v[1]);
                        break;
                    case 'in';
                        $query->whereIn($v[1], $v[3]);
                        break;
                    case 'like';
                        $query->where($v[1], $v[2], '%' . $v[3] . '%');
                        break;
                    case 'range';
                        if (stripos($v[2], 'NOT') !== false) {
                            $query->whereNotBetween($v[1], $v[3]);
                        } else {
                            $query->whereBetween($v[1], $v[3]);
                        }
                        break;
                }
            }

        };

        return [$where, $sort, $order, $offset, $limit];
    }

    /**
     * 获取数据限制的管理员ID
     * 禁用数据限制时返回的是null
     * @return mixed
     */
    protected function getDataLimitAdminIds()
    {
        if (!$this->dataLimit) {
            return null;
        }
        if (Admin::getAssgin('auth')->isSuperAdmin()) {
            return null;
        }
        $adminIds = [];
        if (in_array($this->dataLimit, ['auth', 'personal'])) {
            $adminIds = $this->dataLimit == 'auth' ? Admin::getAssgin('auth')->getChildrenAdminIds(true) : [Admin::getAssgin('auth')->id];
        }
        return $adminIds;
    }

    /**
     * 加载模板
     * @param string $template
     * @param array $data
     * @return mixed
     */
    protected function view($data = [], $view = null)
    {
        if (!$view) {
            $view = Admin::module() . '::' . Admin::controller() . '.' . Admin::action();
        }

        $data = array_merge($this->assign, $data, Admin::assign());
        if (is_null($this->layout)) {
            return view($view)->with($data);
        } else {
            //加载公共模板
            if (!is_null($this->layout) && request()->isMethod('GET')) {
                $this->layout = View::make($this->layout, $data);
            }
            return $this->layout->nest('layouts', $view, $data);
        }
    }

    /**
     * 删除
     */
    public function del()
    {
        $ids = input('ids');
        if ($ids) {
            $pk       = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $this->model->whereIn($this->dataLimitField, $adminIds);
            }
            $list  = $this->model->whereIn($pk, explode(',', $ids))->get();
            $count = 0;
            DB::beginTransaction();
            try {
                foreach ($list as $k => $v) {
                    $count += $v->delete();
                }
                DB::commit();
            } catch (PDOException $e) {
                DB::rollback();
                return $this->error($e->getMessage());
            } catch (Exception $e) {
                DB::rollback();
                return $this->error($e->getMessage());
            }
            if ($count) {
                return $this->success();
            } else {
                return $this->error(lang('No rows were deleted'));
            }
        }
        return $this->error(lang('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 添加
     * @return mixed
     */
    public function add()
    {
        if (isAjax()) {
            $params = input('row');
            if ($params) {
                $params = $this->preExcludeFields($params);
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                $validateResult=true;
                DB::beginTransaction();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        //是否采用模型验证
                        if ($this->modelValidate) {
                            $name           = parse_hump($this->model->getTable());
                            $validate       = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                            $validateResult = $this->model->validateData($params, $validate);
                        }
                        if ($validateResult === false) {
                            $this->error($this->model->getError());
                        }
                    }
                    $result = $this->model->data($params)->save();
                    DB::commit();
                } catch (ValidateException $e) {
                    DB::rollback();
                    return $this->error($e->getMessage());
                } catch (PDOException $e) {
                    DB::rollback();
                    return $this->error($e->getMessage());
                } catch (Exception $e) {
                    return DB::rollback();
                    return $this->error($e->getMessage());
                }
                if ($result !== false) {
                    return $this->success();
                } else {
                    return $this->error(lang('No rows were inserted'));
                }
            }
            return $this->error(lang('Parameter %s can not be empty', ''));
        }
        return $this->view();
    }

    /**
     * 编辑
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed|\think\response\Redirect
     */
    public function edit()
    {
        $ids  = input('ids');
        $rows = $this->model->where($this->model->getPk(), $ids)->first();
        if (!$rows) {
            return $this->error(lang('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($rows[$this->dataLimitField], $adminIds)) {
                return $this->error(lang('You have no permission'));
            }
        }
        if (isAjax()) {
            $params = input("row");
            if ($params) {
                $params         = $this->preExcludeFields($params);
                $result         = false;
                $validateResult = true;
                DB::beginTransaction();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name           = parse_hump($this->model->getTable());
                        $validate       = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $validateResult = $rows->validateData($params, $validate);
                    }
                    if ($validateResult === false) {
                        $this->error($rows->getError());
                    }
                    $result = $rows->data($params)->save();

                    DB::commit();
                } catch (ValidateException $e) {
                    DB::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    DB::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    DB::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    return $this->success();
                } else {
                    return $this->error(lang('No rows were updated'));
                }
            }
            return $this->error(lang('Parameter %s can not be empty', ''));
        }

        if ($rows) {
            Admin::setAssign(['row' => $rows]);
        }
        return $this->view();
    }

    /**
     * 多条更新
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\think\response\Redirect
     */
    public function multi()
    {
        $ids = input("ids");
        if (input('params')) {
            parse_str(input("params"), $values);
            $values = array_intersect_key($values, array_flip(is_array($this->multiFields) ? $this->multiFields : explode(',', $this->multiFields)));
        }
        if ($values || Admin::getAssgin('auth')->isSuperAdmin()) {
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $this->model->whereIn($this->dataLimitField, $adminIds);
            }
            DB::beginTransaction();
            try {
                $list = $this->model->whereIn($this->model->getPk(), explode(',', $ids))->get()->toArray();
                foreach ($list as $index => $item) {
                    foreach ($values as $key => $value) {
                        $list[$index][$key] = $value;
                    }
                }
                $result = $this->model->updateBatch($list);
                DB::commit();
            } catch (PDOException $e) {
                DB::rollback();
                return error($e->getMessage());
            } catch (Exception $e) {
                Db::rollback();
                return error($e->getMessage());
            }
            if ($result) {
                return success();
            } else {
                return error(lang('No rows were updated'));
            }
        } else {
            return error(lang('You have no permission'));
        }
    }

    protected function success($msg = 'Operation completed', $data = [], $url = '')
    {
        return success($msg, $data, $url);
    }

    protected function error($msg = 'Operation failed', $data = [], $url = '')
    {
        return error($msg, $data, $url);
    }
}