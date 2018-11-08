<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/10/25
 * Time: 11:44
 */

namespace  app\admin\controller;

use think\Controller;
use think\Db;

class Integral extends Controller{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 积分中心
     **************************************
     */
    public function index(){
        $integral_data =Db::table('tb_integral')
            ->field("tb_integral.*,tb_user.user_name tname")
            ->join("tb_user","tb_integral.user_id=tb_user.id",'left')
            ->order('tb_integral.operation_time','desc')
            ->paginate(3);
        return view('center',['integral_data'=>$integral_data]);
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 积分详情
     **************************************
     */
    public function detail(){
        return view('detail');
    }


}