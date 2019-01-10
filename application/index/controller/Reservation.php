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
use think\Db;

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
            $store_user_address = $request->only(["store_user_address"])["store_user_address"];
            $num = strpos($store_user_address,"区");
            $store_address = substr($store_user_address,0,$num);
            $user_id = Session::get("user");
            $user_car = db("user_car")->where("user_id",$user_id)->where("status",1)->find();
            $car_series = db("car_series")->where("brand",$user_car["brand"])->where("series",$user_car["series"])->where("year",$user_car["production_time"])->where("displacement",$user_car["displacement"])->field("vehicle_model")->find();
            $serve_goods = db("serve_goods")->where("vehicle_model",$car_series["vehicle_model"])->where("service_setting_id",$service_setting_id)->select();
            foreach ($serve_goods as $key=>$value){
                $where = "`operation_status` = '1' and `store_is_button` = '1' and `del_status` = '1'";
                $address = db("store")->where("store_detailed_address","like","%".$store_address."%")->where($where)->select();;
                foreach ($address as $val){
                    if($val["store_id"] == $value["store_id"]){
                        $serve_goods[$key]["serve_name"] = $val;
                        $serve_goods[$key]["service_setting_name"] = db("service_setting")->where("service_setting_id",$value["service_setting_id"])->value("service_setting_name");
                    }
                }
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
            $goods = db("goods")->where("store_id",$serve_goods_id)->where("goods_status",1)->select();
            $store = db("store")->where("store_id",$serve_goods_id)->select();
            $serve_data = [];
            foreach ($store as $key=>$value){
                $serve_data[$key]["serve_goods"] = db("serve_goods")->where("store_id",$value["store_id"])->where("vehicle_model",$car_series["vehicle_model"])->select();
                $serve_data[$key]["serve_name"] = db("service_setting")->where("service_setting_id",$serve_data[$key]["serve_goods"][0]["service_setting_id"])->value("service_setting_name");
            }
            if($serve_data){
                return ajax_success("获取成功",array("goods"=>$goods,"store"=>$store,"serve_data"=>$serve_data));
            }else{
                return ajax_error("获取成功");
            }
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
            $user_id = Session::get("user");
            if(empty($user_id)){
                exit(json_encode(array("info"=>"请登录","status"=>"2")));
            }else {
                $user_id = Session::get("user");
                $serve_goods_id = $request->only(["id"])["id"];
                $user = db("user")->where("id", $user_id)->select();
                $user_car = db("user_car")->where("user_id", $user_id)->where("status", 1)->find();
                $user_car_message = db("user_car_message")->where("user_car_id",$user_car["id"])->select();
                $serve_goods = db("serve_goods")->where("id", $serve_goods_id)->select();
                $store = db("store")->where("store_id",$serve_goods[0]["store_id"])->select();
                $store_phone = db("user")->where("id",$store[0]["user_id"])->field("phone_num")->select();
                if ($serve_goods) {
                    return ajax_success("获取成功", array("store_phone"=>$store_phone,"user" => $user,"store"=>$store,"user_car_message"=>$user_car_message, "user_car" => $user_car, "serve_goods" => $serve_goods));
                } else {
                    return ajax_error("获取失败");
                }
            }
        }
        return view("reservation_info");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:预约服务全部评价数据
     **************************************
     */
    public function reservation_evaluate_return(Request $request){
        if($request->isPost()){
            $goods_id = $request->only(["goods_id"])["goods_id"];//服务id
            $store_id =$request->only(["store_id"])["store_id"];
            $evaluate_info =db("order_service_evaluate")
                ->where("goods_id",$goods_id)
                ->where("store_id",$store_id)
                ->select();
            dump($evaluate_info);
            foreach ($evaluate_info as $ks=>$vs){
                $evaluate_info[$ks]["images"] = db("order_service_evaluate_images")
                    ->field("images")
                    ->where("evaluate_order_id",$vs["id"])
                    ->select();
                $evaluate_info[$ks]["order_create_time"] =db("order_service")
                    ->where("id",$vs["order_id"])
                    ->value("create_time");
                $evaluate_info[$ks]["user_info"] =db("user")
                    ->where("id",$vs["user_id"])
                    ->field("user_img,phone_num")
                    ->find();
            }
            if(!empty($evaluate_info)){
                return ajax_success("数据返回成功",$evaluate_info);
            }else{
                return ajax_error("没有数据",["status"=>0]);
            }

        }
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:预约服务详全部评价里面的（好评）
     **************************************
     */
    public function reservation_evaluate_good(Request $request){
        if($request->isPost()){
            $goods_id = $request->only(["goods_id"])["goods_id"];//服务id
            $store_id =$request->only(["store_id"])["store_id"];
            $condition ="evaluate_stars = 4 or evaluate_stars = 5";
            $evaluate_info =db("order_service_evaluate")
                ->where("store_id",$store_id)
                ->where($condition)
                ->where("goods_id",$goods_id)
                ->select();
            foreach ($evaluate_info as $ks=>$vs){
                $evaluate_info[$ks]["images"] = db("order_service_evaluate_images")
                    ->field("images")
                    ->where("evaluate_order_id",$vs["id"])
                    ->select();
                $evaluate_info[$ks]["order_create_time"] =db("order_service")
                    ->where("id",$vs["order_id"])
                    ->value("create_time");
                $evaluate_info[$ks]["user_info"] =db("user")
                    ->where("id",$vs["user_id"])
                    ->field("user_img,phone_num")
                    ->find();
            }
            if(!empty($evaluate_info)){
                return ajax_success("数据返回成功",$evaluate_info);
            }else{
                return ajax_error("没有数据",["status"=>0]);
            }

        }
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:预约服务详情全部评价里面的（中评）
     **************************************
     */
    public function reservation_evaluate_secondary(Request $request){
        if($request->isPost()){
            $goods_id = $request->only(["goods_id"])["goods_id"];//服务id
            $store_id =$request->only(["store_id"])["store_id"];
            $condition ="evaluate_stars = 4 or evaluate_stars = 5";
            $evaluate_info =db("order_service_evaluate")
                ->where("store_id",$store_id)
                ->where($condition)
                ->where("goods_id",$goods_id)
                ->select();
            foreach ($evaluate_info as $ks=>$vs){
                $evaluate_info[$ks]["images"] = db("order_service_evaluate_images")
                    ->field("images")
                    ->where("evaluate_order_id",$vs["id"])
                    ->select();
                $evaluate_info[$ks]["order_create_time"] =db("order_service")
                    ->where("id",$vs["order_id"])
                    ->value("create_time");
                $evaluate_info[$ks]["user_info"] =db("user")
                    ->where("id",$vs["user_id"])
                    ->field("user_img,phone_num")
                    ->find();
            }
            if(!empty($evaluate_info)){
                return ajax_success("数据返回成功",$evaluate_info);
            }else{
                return ajax_error("没有数据",["status"=>0]);
            }

        }
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:预约服务详情全部评价里面的（差评）
     **************************************
     */
    public function reservation_evaluate_bad(Request $request){
        if($request->isPost()){
            $goods_id = $request->only(["goods_id"])["goods_id"];//服务id
            $store_id =$request->only(["store_id"])["store_id"];
            $evaluate_info =db("order_service_evaluate")
                ->where("store_id",$store_id)
                ->where("evaluate_stars",1)
                ->where("goods_id",$goods_id)
                ->select();
            foreach ($evaluate_info as $ks=>$vs){
                $evaluate_info[$ks]["images"] = db("order_service_evaluate_images")
                    ->field("images")
                    ->where("evaluate_order_id",$vs["id"])
                    ->select();
                $evaluate_info[$ks]["order_create_time"] =db("order_service")
                    ->where("id",$vs["order_id"])
                    ->value("create_time");
                $evaluate_info[$ks]["user_info"] =db("user")
                    ->where("id",$vs["user_id"])
                    ->field("user_img,phone_num")
                    ->find();
            }
            if(!empty($evaluate_info)){
                return ajax_success("数据返回成功",$evaluate_info);
            }else{
                return ajax_error("没有数据",["status"=>0]);
            }

        }
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:预约服务详情全部评价里面的（有图）
     **************************************
     */
    public function reservation_evaluate_has_img(Request $request){
        if($request->isPost()){
            $goods_id = $request->only(["goods_id"])["goods_id"];//服务id
            $store_id =$request->only(["store_id"])["store_id"];
            $evaluate_info =db("order_service_evaluate")
                ->where("store_id",$store_id)
                ->where("evaluate_stars",1)
                ->where("goods_id",$goods_id)
                ->select();
            foreach ($evaluate_info as $ks=>$vs){
                $img = db("order_service_evaluate_images")
                    ->field("images")
                    ->where("evaluate_order_id",$vs["id"])
                    ->select();
                if(!empty($img)){
                    $evaluate_info[$ks]["images"] =$img;
                }else{
                    $evaluate_info[$ks]["images"] =null;
                }
                $evaluate_info[$ks]["order_create_time"] =db("order_service")
                    ->where("id",$vs["order_id"])
                    ->value("create_time");
                $evaluate_info[$ks]["user_info"] =db("user")
                    ->where("id",$vs["user_id"])
                    ->field("user_img,phone_num")
                    ->find();
            }
            if(!empty($evaluate_info)){
                return ajax_success("数据返回成功",$evaluate_info);
            }else{
                return ajax_error("没有数据",["status"=>0]);
            }

        }
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:预约服务商品详情页面的评价数据查看详情
     **************************************
     */
    public function reservation_evaluate_detail(Request $request){
        if($request->isPost()){
            $evaluate_id = $request->only(["id"])["id"];//评价的id
            $evaluate_info["evaluate_info"] =db("order_service_evaluate")->where("id",$evaluate_id)->find();
            $evaluate_info["images"] =db("order_service_evaluate_images")
                ->field("images")
                ->where("evaluate_order_id",$evaluate_id)
                ->select();
            $evaluate_info["user_info"] = db("user")
                ->where("id", $evaluate_info["evaluate_info"]["user_id"])
                ->field("user_img,phone_num")
                ->find();
            $evaluate_info["order_create_time"] =db("order_parts")
                ->where("id",$evaluate_info["evaluate_info"]["order_id"])
                ->value("order_create_time");
            if(!empty($evaluate_info)){
                return ajax_success("成功返回",$evaluate_info);
            }else{
                return ajax_error("请重新查看",["status"=>0]);
            }

        }
    }


}