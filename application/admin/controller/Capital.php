<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/10/25
 * Time: 11:37
 *
 */

namespace  app\admin\controller;

use think\Controller;
use think\Db;

class Capital extends Controller{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 资金管理首页
     **************************************
     */
    public function index(){
        $user_list = Db::name("user") ->select();
        foreach ($user_list as $key=>$val){
            /*提现*/
            $all_del =Db::name('recharge_reflect')->where('operation_type',"-1")->where('user_id',$val['id'])->sum("operation_amount");
            $user_list[$key]['all_reflect']=round($all_del,2);
            /*充值*/
            $all_add=Db::name('recharge_reflect')->where('operation_type',"1")->where('user_id',$val['id'])->sum("operation_amount");
            $user_list[$key]['all_recharge'] =round($all_add,2);
        }
        return view('index',['user_list'=>$user_list]);
    }



}