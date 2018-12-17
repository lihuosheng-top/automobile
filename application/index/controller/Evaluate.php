<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/13 0013
 * Time: 10:01
 */
namespace  app\index\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Db;

class Evaluate extends  Controller{
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:评价页面
     **************************************
     */
    public function evaluate_index(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");
            $store_id = Session::get("store_id"); //店铺id
            $parts_order_number = Session::get("parts_order_number"); //订单编号
            $condition = "`user_id` = " . $user_id . " and `store_id` = " . $store_id . " and `parts_order_number` = " . $parts_order_number;
            $data = Db::name("order_parts")
                ->where($condition)
                ->select();
            if(!empty($data)){
                return ajax_success("对应的订单信息返回成功",$data);
            }else{
                return ajax_error("没有对应的订单信息",["status"=>0]);
            }
        }
        return view("evaluate_index");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单评价信息的添加
     **************************************
     */
    public function evaluate_parts_add(Request $request){
        if($request->isPost()){
            
        }
    }



}