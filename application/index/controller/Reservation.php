<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/22
 * Time: 14:41
 */
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;

class Reservation extends Controller{


    /**
     * 服务类型
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function service_type(Request $request)
    {

        if($request->isPost()){
            $serve = db("service_setting")->where("service_setting_status","<>",0)->select();
            $serve_goods = [];
            foreach ($serve as $key=>$value){
                $serve_goods[$key]["service_setting_id"] = $value["service_setting_id"];
                $serve_goods[$key]["service_setting_name"] = $value["service_setting_name"];
                $serve_goods[$key]["service_setting_images"] = $value["service_setting_calss_img"];
            }
            if(isset($serve_goods)){
                return ajax_success("获取成功",$serve_goods);
            }else{
                return ajax_error("获取成功");
            }

        }
        return view("service_type");
    }



    /**
     * 预约服务
     * 陈绪
     */
    public function reservation(Request $request)
    {

        if($request->isPost()) {
            $service_setting_id = $request->only(["service_setting_id"])["service_setting_id"];
            $user_id = Session::get("user");
            $user_car = db("user_car")->where("user_id",$user_id)->where("status",1)->find();
            $car_series = db("car_series")->where("brand",$user_car["brand"])->where("series",$user_car["series"])->where("year",$user_car["production_time"])->where("displacement",$user_car["displacement"])->field("vehicle_model")->find();
            $serve_goods = db("serve_goods")->where("vehicle_model",$car_series["vehicle_model"])->where("service_setting_id",$service_setting_id)->select();
            foreach ($serve_goods as $key=>$value){
                $serve_goods[$key]["serve_name"] = db("store")->where("store_id",$value["store_id"])->find();
                $serve_goods[$key]["service_setting_name"] = db("service_setting")->where("service_setting_id",$value["service_setting_id"])->value("service_setting_name");
            }

            if ($serve_goods) {
                return ajax_success("获取成功", $serve_goods);
            } else {
                return ajax_error("获取失败");
            }
        }

        return view("reservation");
    }




    /**
     * 预约服务 详情
     * 陈绪
     */
    public function reservation_detail(Request $request)
    {

        if($request->isPost()){
            $user_id = Session::get("user");
            $user_car = db("user_car")->where("user_id",$user_id)->where("status",1)->find();
            $car_series = db("car_series")->where("brand",$user_car["brand"])->where("series",$user_car["series"])->where("year",$user_car["production_time"])->where("displacement",$user_car["displacement"])->field("vehicle_model")->find();
            $serve_goods_id = $request->only(["id"])["id"];
            $goods = db("goods")->where("store_id",$serve_goods_id)->select();
            $store = db("store")->where("store_id",$serve_goods_id)->select();
            foreach ($store as $key=>$value){
                $store[$key]["goods"] = db("serve_goods")->where("store_id",$value["store_id"])->where("vehicle_model",$car_series["vehicle_model"])->select();
                $store[$key]["serve_name"] = db("service_setting")->where("service_setting_id",$store[$key]["goods"][0]["service_setting_id"])->value("service_setting_name");
            }
            return ajax_success("获取成功",array("goods"=>$goods,"store"=>$store));
        }
        return view("reservation_detail");
    }




    /**
     * 填写预约信息
     * 陈绪
     */
    public function reservation_info(Request $request)
    {
        if($request->isPost()) {
            $serve_goods_id = $request->only(["serve_goods_id"])["serve_goods_id"];
            $user_id = Session::get("user");
            $user = db("user")->where("id", $user_id)->select();
            $user_car = db("user_car")->where("user_id", $user_id)->where("status", 1)->find();
            $serve_goods = db("serve_goods")->where("id", $serve_goods_id)->select();
            if ($serve_goods) {
                return ajax_success("获取成功", array("user" => $user, "user_car" => $user_car, "serve_goods" => $serve_goods));
            } else {
                return ajax_error("获取失败");
            }
        }
        return view("reservation_info");
    }

}