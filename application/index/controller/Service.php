<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/1/4
 * Time: 18:41
 */
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;

class Service extends Controller{


    /**
     * 选择服务类型
     * 陈绪
     */
    public function type_index(Request $request){

        if($request->isPost()) {
            $order_id = Session::get("order_id");
            $order = db("order_parts")->where("id",$order_id)->select();
            $service = [];
            foreach ($order as $key=>$value){
                $service[$key]["order_images"] = $value["goods_image"];
                $service[$key]["order_money"] = $value["order_amount"];
                $service[$key]["order_name"] = $value["parts_goods_name"];
                $service[$key]["order_describe"] = $value["goods_describe"];
                $service[$key]["order_id"] = $order_id;
            }
            if($service){
                Session::delete("order_id");
                return ajax_success('获取成功',$service);
            }else{
                return ajax_error("获取失败");
            }

        }
        return view("type_index");

    }



    /**
     * 申请退款
     * 陈绪
     */
    public function refund(Request $request){

        if($request->isPost()){
            $order_id = $request->only(["order_id"])["order_id"];
            $order = db("order_parts")->where("id",$order_id)->select();
            $service = [];
            foreach ($order as $key=>$value){
                $service[$key]["order_images"] = $value["goods_image"];
                $service[$key]["order_money"] = $value["order_amount"];
                $service[$key]["order_name"] = $value["parts_goods_name"];
                $service[$key]["order_describe"] = $value["goods_describe"];
                $service[$key]["order_id"] = $order_id;
            }
            Session::set("order_id",$order_id);
            if($service){
                return ajax_success('获取成功',$service);
            }else{
                return ajax_error("获取失败");
            }

        }
        return view("refund");

    }



    /**
     * 申请退货存储
     * 陈绪
     */
    public function save(Request $request){

        if($request->isPost()){
            $service = $request->param();
            $images = $request->file("service_images");
            if (!empty($images)) {
                $service_images = [];
                foreach ($images as $value) {
                    $image = $value->move(ROOT_PATH . 'public' . DS . 'uploads');
                    $service_images[] = str_replace("\\", "/", $image->getSaveName());
                }
                $service["service_images"] = implode(",",$service_images);
            }
            $service["create_time"] = time();
            $service["status"] = 0;
            $bool = db("service")->insert($service);
            if($bool){
                return ajax_success("反馈成功");
            }else{
                return ajax_error("反馈失败");
            }
        }

    }

}