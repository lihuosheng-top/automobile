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
            $serve_goods = db("serve_goods")->where("service_setting_id", $service_setting_id)->select();
            $where = "`store_is_button` = '1' and `del_status` = '1' and `operation_status` = '1'";
            foreach ($serve_goods as $key=>$value){
                $serve_goods[$key]["name"] = db("service_setting")->where("service_setting_id",$value["service_setting_id"])->value("service_setting_name");
                $serve_goods[$key]["store"] = db("store")->where($where)->where("store_id",$value["store_id"])->find();
                unset($serve_goods[$key]["ruling_money"]);
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
            $serve_goods_id = $request->only(["id"])["id"];
            $store = db("store")->where("store_id",$serve_goods_id)->select();
            foreach ($store as $key=>$value){
                $store[$key]["goods"] = db("serve_goods")->where("store_id",$value["store_id"])->select();
            }
            return view("获取成功",$store);
        }
        return view("reservation_detail");
    }




    /**
     * 填写预约信息
     * 陈绪
     */
    public function reservation_info()
    {
        return view("reservation_info");
    }

}