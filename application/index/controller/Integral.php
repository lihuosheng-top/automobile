<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/11 0011
 * 积分
 * Time: 10:17
 */

namespace  app\index\controller;


use think\Controller;
use think\Request;
use  think\Db;
use think\Session;

class Integral extends Controller{


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:消费满3元可使用3积分，3积分抵3元
     * 返回给前端显示
     **************************************
     * @param Request $request
     */
    public function return_integral_information(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");
            if(empty($user_id)){
                exit(json_encode(array("status"=>2,"info"=>"请登录")));
            }
            $integral_wallet_data =Db::name("user")->where("id",$user_id)->field("user_integral_wallet,user_wallet")->find();
            $data =Db::name("integral_discount_settings")->where("integral_can_be_used","<=",$integral_wallet_data["user_integral_wallet"])->order("consumption_full","asc")->find();
            if(!empty($data)){
                return ajax_success("积分信息返回成功",$data);
            }else{
                exit(json_encode(array("status"=>0,"info"=>"没有积分设置")));
            }
        }
    }

}