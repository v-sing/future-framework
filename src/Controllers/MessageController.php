<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/12 0012
 * Time: 16:54
 */

namespace Future\Admin\Controllers;

/**
 * 信息控制类
 * Class MessageController
 * @package App\Modules\Admin\Http\Controllers
 */
class MessageController extends BackendController
{
    protected $noNeedRight = '*';
    protected $noNeedLogin = '*';
    protected $layout = null;
    public function index()
    {

        return $this->view();
    }
}