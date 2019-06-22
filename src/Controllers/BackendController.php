<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/24
 * Time: 18:10
 */

namespace Future\Admin\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Future\Admin\Traits\Backend;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Future\Admin\Auth\Auth;
use Future\Admin\Facades\Admin;

class BackendController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, Backend;

    /**
     * 公共模板
     * @var string
     */
    protected $layout = 'admin::layouts.site';
    /**
     * 要传递的参数
     * @var array
     */
    private $assign = [];
    /**
     * @var array|\Illuminate\Http\Request|null|string|\think\Request
     */
    protected $request = null;
    /**
     * 权限控制类
     * @var Auth
     */
    protected $auth = null;

    /**
     * 模型对象
     * @var \think\Model
     */
    protected $model = null;

    /**
     * 快速搜索时执行查找的字段
     */
    protected $searchFields = 'id';

    /**
     * 是否是关联查询
     */
    protected $relationSearch = false;

    /**
     * 是否开启数据限制
     * 支持auth/personal
     * 表示按权限判断/仅限个人
     * 默认为禁用,若启用请务必保证表中存在admin_id字段
     */
    protected $dataLimit = false;

    /**
     * 数据限制字段
     */
    protected $dataLimitField = 'admin_id';

    /**
     * 数据限制开启时自动填充限制字段值
     */
    protected $dataLimitFieldAutoFill = true;

    /**
     * 是否开启Validate验证
     */
    protected $modelValidate = false;

    /**
     * 是否开启模型场景验证
     */
    protected $modelSceneValidate = false;

    /**
     * Multi方法可批量修改的字段
     */
    protected $multiFields = 'status';

    /**
     * Selectpage可显示的字段
     */
    protected $selectpageFields = '*';

    /**
     * 导入文件首行类型
     * 支持comment/name
     * 表示注释或字段名
     */
    protected $importHeadType = 'comment';
    /**
     * 不验证权限的
     * @var array
     */
    protected $noNeedRight = ['login'];

    /**
     * 不验证登录
     * @var array
     */
    protected $noNeedLogin = ['login'];

    public function __construct()
    {

        $this->_initialize();

    }

    protected function _initialize()
    {
        Admin::setNature([
            'noNeedRight' => $this->noNeedRight,
            'noNeedLogin' => $this->noNeedLogin,
        ]);
        $this->auth = Auth::instance();
        $this->middleware('admin.auth');
        $this->request = request();
    }


}