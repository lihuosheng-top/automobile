<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17 0017
 * 快递员
 * Time: 17:41
 */

namespace  app\index\controller;


use think\Controller;
use think\Request;
use think\Db;
use think\Session;

class  Express extends  Controller{


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:快递员退出登录
     **************************************
     * @param Request $request
     */
    public function express_logout(Request $request){
        if($request->isPost()){
            Session('delivery_id',null);
            return ajax_success('退出成功',['status'=>1]);
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:设置
     **************************************
     * @return \think\response\View
     */
    public function express_setting(){
        return view("express_setting");
    }



    /**
     **************李火生*******************
     * @param Request $request
     * Notes:待接单页面
     **************************************
     * @return \think\response\View
     */
    public function express_wait_for_order(){

        $delivery_id = Session::get("delivery_id");
        $delivery_data = db("delivery")->select();
        $order_data = db("order_parts")->select();
        $delivery = [];
        $store = db("store")->select();
        foreach ($order_data as $key=>$value){
            foreach ($store as $val){
                if($value["store_id"] == $val["store_id"]){
                    $delivery[$key]["order_id"] = $value["id"];
                    $delivery[$key]["order_address"] = $value["harvester_address"];
                    $delivery[$key]["store_name"] = $val["store_name"];
                    $delivery[$key]["store_address"] = $val["store_street_address"];
                    $delivery[$key]["store_city_address"] = $val["store_city_address"];
                }
            }

        }
        foreach ($delivery as $k=>$v) {
            foreach ($delivery_data as $v_1) {
                if($v["store_city_address"] == $v_1["area"]){
                    $delivery[$k]["delivery_user_id"] = $v_1['id'];
                }
            }
        }
        halt($delivery);
        foreach ($delivery as $k_1=>$v_2){
            if($delivery_id != $v_2["delivery_user_id"]){
                unset($delivery[$k_1]);
            }
        }



        return view("express_wait_for_order");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:待取货页面
     **************************************
     * @return \think\response\View
     */
    public function express_wait_for_take(){
        return view("express_wait_for_take");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配送中页面
     **************************************
     * @return \think\response\View
     */
    public function express_distribution(){
        return view("express_distribution");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:已完成页面
     **************************************
     * @return \think\response\View
     */
    public function express_completed(){
        return view("express_completed");
    }
 /**
     **************李火生*******************
     * @param Request $request
     * Notes:快递详情页面
     **************************************
     * @return \think\response\View
     */
    public function express_detail(){
        return view("express_detail");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:快递员信息返回
     **************************************
     * @param Request $request
     */
    public function express_info(Request $request){
        if($request->isPost()){
           $delivery_id = Session::get("delivery_id");
            $data =Db::name("delivery")->where("id",$delivery_id)->find();
            if(!empty($data)){
                return ajax_success("数据返回成功",$data);
            }else{
                return ajax_error("没有这个快递信息",['status'=>0]);
            }
        }
    }

}