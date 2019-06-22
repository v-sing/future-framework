<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/12 0012
 * Time: 11:30
 */

namespace Future\Admin\Controllers;
use Future\Admin\Future\Date;

use Illuminate\Http\Request;

class DashboardController extends BackendController
{
    public function index(Request $request)
    {
        $seventtime = Date::unixtime('day', -7);
        $paylist    = $createlist = [];
        for ($i = 0; $i < 7; $i++) {
            $day              = date("Y-m-d", $seventtime + ($i * 86400));
            $createlist[$day] = mt_rand(20, 200);
            $paylist[$day]    = mt_rand(1, mt_rand(1, $createlist[$day]));
        }
        $add=[
            'totaluser'        => 35200,
            'totalviews'       => 219390,
            'totalorder'       => 32143,
            'totalorderamount' => 174800,
            'todayuserlogin'   => 321,
            'todayusersignup'  => 430,
            'todayorder'       => 2324,
            'unsettleorder'    => 132,
            'sevendnu'         => '80%',
            'sevendau'         => '32%',
            'paylist'          => $paylist,
            'createlist'       => $createlist,
        ];
        return $this->view($add);
    }
}