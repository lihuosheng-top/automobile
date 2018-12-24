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
     **************陈绪*******************
     * @param Request $request
     * Notes:待接单页面
     **************************************
     * @return \think\response\View
     */
    public function express_wait_for_order(Request $request){

        if($request->isPost()) {
            $delivery_id = Session::get("delivery_id");
            $delivery_data = db("delivery")->where("id", $delivery_id)->select();
            $store = db("store")->where("store_city_address", $delivery_data[0]["area"])->select();
            $delivery = [];
            foreach ($store as $key => $value) {
                $order = db("order_parts")->where("store_id", $value["store_id"])->select();
                foreach ($order as $val) {
                    $delivery[] = array("store_name" => $value["store_name"],
                        "store_address" => $value["store_detailed_address"],
                        "order_id" => $val["id"],
                        "order_address" => $val["harvester_address"],
                        "order_user_number" => $val["user_phone_number"],
                        "store_number" => db("user")->where("id",$value["user_id"])->value("phone_num"),
                    );
                }
            }
            if($delivery) {
                return ajax_success("获取成功",array("delivery"=>$delivery,"delivery_data"=>$delivery_data));
            }else{
                return ajax_error("你服务的区域没有匹配到相应的订单");
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