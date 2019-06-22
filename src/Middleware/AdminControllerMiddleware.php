<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/4/28 0028
 * Time: 14:27
 */

namespace Future\Admin\Middleware;

use Closure;
use Future\Admin\Facades\Admin;
use Future\Admin\Auth\Database\AuthGroup;
use Future\Admin\Future\Tree;

/**
 * AdminController控制器中间件
 * Class AdminControllerMiddleware
 * @package Future\Admin\Middleware
 */
class AdminControllerMiddleware
{
    protected $childrenAdminIds = [];
    protected $childrenGroupIds = [];

    /**
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $auth                   = Admin::getAssign('auth');
        $this->childrenAdminIds = $auth->getChildrenAdminIds(true);
        $this->childrenGroupIds = $auth->getChildrenGroupIds(true);
        $groupList              = AuthGroup::whereIn('id', $this->childrenGroupIds)->get()->toArray();
        foreach ($groupList as $k =>$v){
            $groupList[$k]['name']=lang($groupList[$k]['name']);
        }

        Tree::instance()->init($groupList);
        $groupdata = [];
        if ($auth->isSuperAdmin()) {
            $result = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0));
            foreach ($result as $k => $v) {
                $groupdata[$v['id']] = lang($v['name']);
            }
        } else {
            $result = [];

            $groups = $auth->getGroups();
            foreach ($groups as $m => $n) {
                $childlist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray($n['id']));

                foreach ($childlist as $k => $v) {
                    $result[$v['id']] =lang($v['name']);
                }
            }
            $groupdata = $result;
        }
        $config          = Admin::getAssign('config');
        $config['admin'] = array_merge($config['admin'], ['id' => $auth->id, 'group_ids' => $auth->getGroupIds()]);
        Admin::setAssign(
            [
                'config'           => $config,
                'groupdata'        => $groupdata,
                'childrenAdminIds' => $this->childrenAdminIds,
                'childrenGroupIds' => $this->childrenGroupIds
            ]
        );
        return $next($request);
    }

}