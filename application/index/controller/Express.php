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
     **************陈绪*******************
     * @param Request $request
     * Notes:快递员退出登录
     **************************************
     * @param Request $request
     */
    public function express_logout(Request $request){
        if($request->isPost()){
            Session('delivery_id',null);
            return ajax_success('退出成功');
        }
    }


    /**
     **************陈绪*******************
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
            $where ="`is_hot_store` = '1' and `store_is_button` = '1' and `del_status` = '1' and `operation_status` = '1' ";
            $store = db("store")->where("store_city_address", $delivery_data[0]["area"])->where($where)->select();
            $delivery = [];
            foreach ($store as $key => $value) {
                $order = db("order_parts")->where("store_id", $value["store_id"])->where("status",3)->select();
                foreach ($order as $val) {
                    $delivery[] = array(
                        "store_name" => $value["store_name"],
                        "store_address" => $value["store_detailed_address"],
                        "order_id" => $val["id"],
                        "order_address" => $val["harvester_address"],
                        "order_user_number" => $val["user_phone_number"],
                        "store_number" => db("user")->where("id",$value["user_id"])->value("phone_num"),
                        "order_create_time"=>$val["order_create_time"],
                        "parts_order_number"=>$val["parts_order_number"],
                        "store_id"=>$value["store_id"],
                        "order_status"=>$val["status"]

                    );
                }
            }
            $service = db("service")->select();
            if(!empty($service)) {
                foreach ($service as $k => $v) {
                    if ($v["status"] == 2) {
                        foreach ($store as $v_1) {
                            $order = db("order_parts")->where("id", $v["order_id"])->where("store_id", $v_1["store_id"])->where("status",13)->select();
                            foreach ($order as $v_2) {
                                $delivery[] = array("store_name" => $v_1["store_name"],
                                    "store_address" => $v_1["store_detailed_address"],
                                    "order_id" => $v_2["id"],
                                    "order_address" => $v_2["harvester_address"],
                                    "order_user_number" => $v_2["user_phone_number"],
                                    "store_number" => db("user")->where("id", $v_1["user_id"])->value("phone_num"),
                                    "order_create_time" => $v_2["order_create_time"],
                                    "parts_order_number" => $v_2["parts_order_number"],
                                    "store_id" => $v_1["store_id"],
                                    "order_status" => $v_2["status"]
                                );
                            }
                        }
                    }

                }
            }
            if($delivery) {
                foreach ($delivery as $k=>$v){
                    $store_info=Db::name("store")->field("longitude,latitude")->where("store_id",$v["store_id"])->find();
                    $delivery[$k]["longitude"] =$store_info["longitude"];
                    $delivery[$k]["latitude"] =$store_info["latitude"];
                }
                return ajax_success("获取成功",array("delivery"=>$delivery,"delivery_data"=>$delivery_data));
            }else{
                return ajax_error("你服务的区域没有匹配到相应的订单",array("delivery"=>$delivery,"delivery_data"=>$delivery_data));
            }
        }
        return view("express_wait_for_order");
    }



    /**
     * 待接单入库
     * 陈绪
     * @param Request $request
     */
    public function express_order_save(Request $request){
        if($request->isPost()) {
            $express_data = $request->param();
            $express_data["status"] = 1;
            unset($express_data["order_status"]);
            $is_express =Db::name("delivery_order")
                ->where("order_id",$express_data["order_id"])
                ->find();
            if(!empty($is_express)){
                return ajax_error("此单已被抢");
            }
            $bool = db("delivery_order")->insert($express_data);
            $order_status = $request->only(["order_status"])["order_status"];
            if ($bool) {
                if($order_status == 13){
                    Session::set("order_status",$order_status);
                    db("order_parts")->where("id",$express_data["order_id"])->update(["status"=>15]);
                }else{
                    db("order_parts")->where("id",$express_data["order_id"])->update(["status"=>4]);
                }
                return ajax_success("入库成功");
            } else {
                return ajax_error("入库失败");
            }
        }
    }



    /**
     **************陈绪*******************
     * @param Request $request
     * Notes:待取货页面
     **************************************
     * @return \think\response\View
     */
    public function express_wait_for_take(Request $request){
        if($request->isPost()) {
            $delivery_id = Session::get("delivery_id");
            $delivery_data = db("delivery")->where("id", $delivery_id)->select();
            $express = db("delivery_order")->where("delivery_id", $delivery_id)->where("status", 1)->select();
            if ($express) {
                foreach ($express as $k=>$v){
                    $store_info=Db::name("store")->field("longitude,latitude")->where("store_id",$v["store_id"])->find();
                    $express[$k]["longitude"] =$store_info["longitude"];
                    $express[$k]["latitude"] =$store_info["latitude"];
                }
                return ajax_success("获取成功",array("express"=>$express,"delivery_data"=>$delivery_data));
            } else {
                return ajax_error("获取失败",$delivery_data);
            }
        }
        return view("express_wait_for_take");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:IOS泡泡界面所有数据返回
     **************************************
     * @param Request $request
     * @return \think\response\View|void
     */
    public function express_information_return(Request $request){
        if($request->isPost()) {
            $delivery_id = Session::get("delivery_id");
            $delivery_data = db("delivery")->where("id", $delivery_id)->select();
            $express = db("delivery_order")->where("delivery_id", $delivery_id)->select();
            if ($express) {
                foreach ($express as $k=>$v){
                    $store_info=Db::name("store")->field("longitude,latitude")->where("store_id",$v["store_id"])->find();
                    $express[$k]["longitude"] =$store_info["longitude"];
                    $express[$k]["latitude"] =$store_info["latitude"];
                }
                return ajax_success("获取成功",array("express"=>$express,"delivery_data"=>$delivery_data));
            } else {
                return ajax_error("获取失败",$delivery_data);
            }
        }
    }

    /**
     * 快递人员已取货
     * 陈绪
     * @param Request $request
     */
    public function express_order_get(Request $request){

        if($request->isPost()){
            $id = $request->only(["id"])["id"];
            $order = db("delivery_order")->where("id",$id)->value("order_id");
            $bool = db("delivery_order")->where("id",$id)->update(["status"=>2]);
            if($bool){
                $order_status = Session::get("order_status");
                if($order_status == 13){
                    db("order_parts")->where("id",$order)->update(["status"=>15]);
                }

                return ajax_success("已取货");
            }else{
                return ajax_error("取货失败");
            }
        }

    }


    /**
     **************陈绪*******************
     * @param Request $request
     * Notes:配送中页面
     **************************************
     * @return \think\response\View
     */
    public function express_distribution(Request $request){

        if($request->isPost()) {
            $delivery_id = Session::get("delivery_id");
            $delivery_data = db("delivery")->where("id", $delivery_id)->select();
            $express = db("delivery_order")->where("delivery_id", $delivery_id)->where("status", 2)->select();
            if ($express) {
                foreach ($express as $k=>$v){
                    $store_info=Db::name("store")->field("longitude,latitude")->where("store_id",$v["store_id"])->find();
                    $express[$k]["longitude"] =$store_info["longitude"];
                    $express[$k]["latitude"] =$store_info["latitude"];
                }
                return ajax_success("获取成功", array("express"=>$express,"delivery_data"=>$delivery_data));
            } else {
                return ajax_error("获取失败",$delivery_data);
            }
        }
        return view("express_distribution");
    }




    /**
     * 配送中状态修改
     * 陈绪
     */
    public function express_distribution_edit(Request $request){

        if($request->isPost()){
            $id = $request->only(["id"])["id"];
            $order = db("delivery_order")->where("id",$id)->value("order_id");
            $bool = db("delivery_order")->where("id",$id)->update(["status"=>3]);
            if($bool){
                $order_status = Session::get("order_status");
                if($order_status == 13){
                    db("order_parts")->where("id",$order)->update(["status"=>12]);
                }else{
                    db("order_parts")->where("id",$order)->update(["status"=>5]);
                }
                return ajax_success("已取货");
            }else{
                return ajax_error("取货失败");
            }
        }
    }



    /**
     **************陈绪*******************
     * @param Request $request
     * Notes:已完成页面
     **************************************
     * @return \think\response\View
     */
    public function express_completed(Request $request){
        if($request->isPost()) {
            $delivery_id = Session::get("delivery_id");
            $delivery_data = db("delivery")->where("id", $delivery_id)->select();
            $express = db("delivery_order")->where("delivery_id", $delivery_id)->where("status",3)->select();
            if ($express) {
                foreach ($express as $k=>$v){
                    $store_info=Db::name("store")->field("longitude,latitude")->where("store_id",$v["store_id"])->find();
                    $express[$k]["longitude"] =$store_info["longitude"];
                    $express[$k]["latitude"] =$store_info["latitude"];
                }
                return ajax_success("获取成功",array("express"=>$express,"delivery_data"=>$delivery_data));
            } else {
                return ajax_error("获取失败",$delivery_data);
            }
        }
        return view("express_completed");
    }


 /**
     **************陈绪*******************
     * @param Request $request
     * Notes:快递详情页面
     **************************************
     * @return \think\response\View
     */
    public function express_detail(Request $request){
        if($request->isPost()){
            $order_id = $request->only(["order_id"])["order_id"];
            $order = db("order_parts")->where("id",$order_id)->select();
            foreach ($order as $key=>$value){
                $order[$key]["store"] = db("store")->where("store_id",$value["store_id"])->find();
            }
            if($order){
                return ajax_success("获取成功",$order);
            }else{
                return ajax_error("失败");
            }

        }
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



    /**
     * 快递单删除
     * 陈绪
     */
    public function express_distribution_del(Request $request){

        if($request->isPost()) {
            $id = $request->only(["id"])["id"];
            $bool = db("delivery_order")->where("id", $id)->delete();
            if ($bool) {
                return ajax_success("成功");
            } else {
                return ajax_error("失败");
            }
        }

    }

}