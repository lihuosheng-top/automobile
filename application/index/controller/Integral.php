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
            $data =Db::name("integral_discount_settings")->order("consumption_full","asc")->find();
            if(!empty($data)){
                return ajax_success("积分信息返回成功",$data);
            }else{
                return ajax_error("没有积分设置",["status"=>0]);
            }
        }
    }


}