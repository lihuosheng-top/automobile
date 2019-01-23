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
            $user_id = Session::get("user");
            if(!empty($user_id)){
                $user_car = db("user_car")->where("user_id",$user_id)->where("status",1)->find();
                $car_series = db("car_series")->where("brand",$user_car["brand"])->where("series",$user_car["series"])->field("vehicle_model")->find();
                $serve_goods = db("serve_goods")->where("vehicle_model",$car_series["vehicle_model"])->where("service_setting_id",$service_setting_id)->select();
                foreach ($serve_goods as $key=>$value){
                    $where = "`operation_status` = '1' and `store_is_button` = '1' and `del_status` = '1'";
                    $address = db("store")->where($where)->order("store_order_num")->select();
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
            }else{
                $user_car = db("user_car")->where("status",1)->select();
                $serve_goods = [];
                foreach ($user_car as $k_1=>$v_1){
                    $car_series = db("car_series")->where("brand",$v_1["brand"])->where("series",$v_1["series"])->field("vehicle_model")->find();
                    $serve_goods[] = db("serve_goods")->where("vehicle_model",$car_series["vehicle_model"])->where("service_setting_id",$service_setting_id)->find();
                }
                foreach ($serve_goods as $k_1=>$v_1){
                    if($v_1 == null) {
                        unset($serve_goods[$k_1]);
                    }
                }
                //数组去重
                $serve_goods = unique_arr($serve_goods);
                foreach ($serve_goods as $key=>$value){
                   $where = "`operation_status` = '1' and `store_is_button` = '1' and `del_status` = '1'";
                        $address = db("store")->where($where)->order("store_order_num")->select();
                        foreach ($address as $val) {
                            if ($val["store_id"] == $value["store_id"]) {
                                $serve_goods[$key]["serve_name"] = $val;
                                $serve_goods[$key]["service_setting_name"] = db("service_setting")->where("service_setting_id", $value["service_setting_id"])->value("service_setting_name");
                            }
                        }
                }

                if ($serve_goods) {
                    return ajax_success("获取成功", $serve_goods);
                } else {
                    return ajax_error("获取失败");
                }
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
            $data = Session::get("role_name_store_id");
            $service_setting_id = $request->only(["service_setting_id"])["service_setting_id"];
            $user_car = db("user_car")->where("user_id",$user_id)->where("status",1)->find();
            $car_series = db("car_series")->where("brand",$user_car["brand"])->where("series",$user_car["series"])->field("vehicle_model")->find();
            $serve_goods_id = $request->only(["id"])["id"];
            $goods = db("goods")->where("store_id",$serve_goods_id)->where("goods_status",1)->select();
            if(empty($data)){
                foreach ($goods as $k_1=>$v_1){
                    $goods[$k_1]['goods_adjusted_money'] = db("specail")->where("goods_id", $goods[$k_1]['id'])->min('goods_adjusted_price');
                    if($v_1["goods_standard"] != "通用"){
                        unset($goods[$k_1]);
                    }
                }
            }
            $store = db("store")->where("store_id",$serve_goods_id)->select();
            $serve_data = [];
            foreach ($store as $key=>$value){
                $serve_data[$key]["serve_goods"] = db("serve_goods")->where("service_setting_id",$service_setting_id)->where("store_id",$value["store_id"])->where("vehicle_model",$car_series["vehicle_model"])->select();
                foreach ($serve_data[$key]["serve_goods"] as $val){
                    $serve_data[$key]["serve_name"] = db("service_setting")->where("service_setting_id",$val["service_setting_id"])->value("service_setting_name");
                    $serve_data[$key]["service_setting_id"] = $val["service_setting_id"];
                }
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
     * Notes:全部评价数量，好评数量，中评数量，差评数量，有图数量
     **************************************
     * @param Request $request
     */
    public function  reservation_evaluate_numbers(Request $request){
        if($request->isPost()){
            //前端传setting_id过来识别是那种服务项目类型
            $service_setting_id = $request->only(["setting_id"])["setting_id"];//服务setting_id
            $store_id = $request->only(["store_id"])["store_id"];
            //所有的
            $all_numbers[0] = db("order_service_evaluate")
                ->where("setting_id",$service_setting_id)
                ->where("store_id", $store_id)
                ->count();
            //好评的数量
            $condition ="evaluate_stars = 4 or evaluate_stars = 5";
            $all_numbers[1] = db("order_service_evaluate")
                ->where($condition)
                ->where("setting_id",$service_setting_id)
                ->where("store_id", $store_id)
                ->count();
            //中评
            $min_condition ="evaluate_stars = 2 or evaluate_stars = 3";
            $all_numbers[2] = db("order_service_evaluate")
                ->where($min_condition)
                ->where("setting_id",$service_setting_id)
                ->where("store_id", $store_id)
                ->count();
            //差评
            $bad_condition ="evaluate_stars = 1";
            $all_numbers[3] = db("order_service_evaluate")
                ->where($bad_condition)
                ->where("setting_id",$service_setting_id)
                ->where("store_id", $store_id)
                ->count();

            $all_img= db("order_service_evaluate")
                ->where("setting_id",$service_setting_id)
                ->where("store_id", $store_id)
                ->select();
            $set =[];
            foreach ($all_img as $k=>$v){
                $is_set =Db::name("order_service_evaluate_images")
                    ->where("evaluate_order_id",$v["id"])
                    ->find();
                if(!empty($is_set)){
                    $set[$k] =$is_set["evaluate_order_id"];
                }
            }
            if(!empty($set)){
                $all_numbers[4] =count(array_unique($set));
            }else{
                $all_numbers[4] =0;
            }
            if(!empty($all_numbers)){
                return ajax_success("有数据",$all_numbers);
            }else{
                return ajax_error("没有记录");
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:预约服务全部评价数据
     **************************************
     */
    public function reservation_evaluate_return(Request $request){
        if($request->isPost()) {
            $service_setting_id = $request->only(["goods_id"])["goods_id"];//服务setting_id
            $store_id = $request->only(["store_id"])["store_id"];
            $goods_id_arr = Db::name("serve_goods")->where("service_setting_id", $service_setting_id)->select();
            foreach ($goods_id_arr as $key => $value) {
                $evaluate_info = db("order_service_evaluate")
                    ->where("goods_id", $value["id"])
                    ->where("store_id", $store_id)
                    ->select();
                if (!empty($evaluate_info)) {
                    $evaluate_info_arr[] = $evaluate_info;
                }
            }
            if(!empty($evaluate_info_arr)){
                foreach ($evaluate_info_arr as $kk => $vv) {
                    foreach ($vv as $i => $j) {
                        $evaluate_info_arr[$kk][$i]["images"] = db("order_service_evaluate_images")
                            ->field("images")
                            ->where("evaluate_order_id", $j["id"])
                            ->select();
                        $evaluate_info_arr[$kk][$i]["order_create_time"] = db("order_service")
                            ->where("id", $j["order_id"])
                            ->value("create_time");
                        $evaluate_info_arr[$kk][$i]["user_info"] = db("user")
                            ->where("id", $j["user_id"])
                            ->field("user_img,phone_num")
                            ->find();
                    }
                }
                $evaluate_info_arr = array_reduce($evaluate_info_arr, 'array_merge', array());
            }
            if (!empty($evaluate_info_arr)) {
                $ords =array();
                foreach ($evaluate_info_arr as $vl){
                    $ords[] =intval($vl["create_time"]);
                }
                array_multisort($ords,SORT_DESC,$evaluate_info_arr);
                return ajax_success("数据返回成功", $evaluate_info_arr);
            } else {
                return ajax_error("没有数据", ["status" => 0]);
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
        if($request->isPost()) {
            $service_setting_id = $request->only(["goods_id"])["goods_id"];//服务setting_id
            $store_id = $request->only(["store_id"])["store_id"];
            $goods_id_arr = Db::name("serve_goods")->where("service_setting_id", $service_setting_id)->select();
            $condition ="evaluate_stars = 4 or evaluate_stars = 5";
            foreach ($goods_id_arr as $key => $value) {
                $evaluate_info = db("order_service_evaluate")
                    ->where("goods_id", $value["id"])
                    ->where($condition)
                    ->where("store_id", $store_id)
                    ->select();
                if (!empty($evaluate_info)) {
                    $evaluate_info_arr[] = $evaluate_info;
                }
            }

            if (!empty($evaluate_info_arr)) {
                foreach ($evaluate_info_arr as $kk => $vv) {
                    foreach ($vv as $i => $j) {
                        $evaluate_info_arr[$kk][$i]["images"] = db("order_service_evaluate_images")
                            ->field("images")
                            ->where("evaluate_order_id", $j["id"])
                            ->select();
                        $evaluate_info_arr[$kk][$i]["order_create_time"] = db("order_service")
                            ->where("id", $j["order_id"])
                            ->value("create_time");
                        $evaluate_info_arr[$kk][$i]["user_info"] = db("user")
                            ->where("id", $j["user_id"])
                            ->field("user_img,phone_num")
                            ->find();
                    }
                }
                $evaluate_info_arr = array_reduce($evaluate_info_arr, 'array_merge', array());
                $ords =array();
                foreach ($evaluate_info_arr as $vl){
                    $ords[] =intval($vl["create_time"]);
                }
                array_multisort($ords,SORT_DESC,$evaluate_info_arr);

                return ajax_success("数据返回成功", $evaluate_info_arr);
            } else {
                return ajax_error("没有数据", ["status" => 0]);
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
        if($request->isPost()) {
            $service_setting_id = $request->only(["goods_id"])["goods_id"];//服务setting_id
            $store_id = $request->only(["store_id"])["store_id"];
            $goods_id_arr = Db::name("serve_goods")->where("service_setting_id", $service_setting_id)->select();
            $condition ="evaluate_stars = 2 or evaluate_stars = 3";
            foreach ($goods_id_arr as $key => $value) {
                $evaluate_info = db("order_service_evaluate")
                    ->where("goods_id", $value["id"])
                    ->where($condition)
                    ->where("store_id", $store_id)
                    ->select();
                if (!empty($evaluate_info)) {
                    $evaluate_info_arr[] = $evaluate_info;
                }
            }
            if (!empty($evaluate_info_arr)) {
                foreach ($evaluate_info_arr as $kk => $vv) {
                    foreach ($vv as $i => $j) {
                        $evaluate_info_arr[$kk][$i]["images"] = db("order_service_evaluate_images")
                            ->field("images")
                            ->where("evaluate_order_id", $j["id"])
                            ->select();
                        $evaluate_info_arr[$kk][$i]["order_create_time"] = db("order_service")
                            ->where("id", $j["order_id"])
                            ->value("create_time");
                        $evaluate_info_arr[$kk][$i]["user_info"] = db("user")
                            ->where("id", $j["user_id"])
                            ->field("user_img,phone_num")
                            ->find();
                    }
                }
                $evaluate_info_arr = array_reduce($evaluate_info_arr, 'array_merge', array());
                $ords =array();
                foreach ($evaluate_info_arr as $vl){
                    $ords[] =intval($vl["create_time"]);
                }
                array_multisort($ords,SORT_DESC,$evaluate_info_arr);
                return ajax_success("数据返回成功", $evaluate_info_arr);
            } else {
                return ajax_error("没有数据", ["status" => 0]);
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
        if($request->isPost()) {
            $service_setting_id = $request->only(["goods_id"])["goods_id"];//服务setting_id
            $store_id = $request->only(["store_id"])["store_id"];
            $goods_id_arr = Db::name("serve_goods")->where("service_setting_id", $service_setting_id)->select();
            $condition ="evaluate_stars = 1";
            foreach ($goods_id_arr as $key => $value) {
                $evaluate_info = db("order_service_evaluate")
                    ->where("goods_id", $value["id"])
                    ->where($condition)
                    ->where("store_id", $store_id)
                    ->select();
                if (!empty($evaluate_info)) {
                    $evaluate_info_arr[] = $evaluate_info;
                }
            }
            if (!empty($evaluate_info_arr)) {
                foreach ($evaluate_info_arr as $kk => $vv) {
                    foreach ($vv as $i => $j) {
                        $evaluate_info_arr[$kk][$i]["images"] = db("order_service_evaluate_images")
                            ->field("images")
                            ->where("evaluate_order_id", $j["id"])
                            ->select();
                        $evaluate_info_arr[$kk][$i]["order_create_time"] = db("order_service")
                            ->where("id", $j["order_id"])
                            ->value("create_time");
                        $evaluate_info_arr[$kk][$i]["user_info"] = db("user")
                            ->where("id", $j["user_id"])
                            ->field("user_img,phone_num")
                            ->find();
                    }
                }
                $evaluate_info_arr = array_reduce($evaluate_info_arr, 'array_merge', array());
                $ords =array();
                foreach ($evaluate_info_arr as $vl){
                    $ords[] =intval($vl["create_time"]);
                }
                array_multisort($ords,SORT_DESC,$evaluate_info_arr);
                return ajax_success("数据返回成功", $evaluate_info_arr);
            } else {
                return ajax_error("没有数据", ["status" => 0]);
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
        if($request->isPost()) {
            $service_setting_id = $request->only(["goods_id"])["goods_id"];//服务setting_id
            $store_id = $request->only(["store_id"])["store_id"];
            $goods_id_arr = Db::name("serve_goods")->where("service_setting_id", $service_setting_id)->select();
            foreach ($goods_id_arr as $key => $value) {
                $evaluate_info = db("order_service_evaluate")
                    ->where("goods_id", $value["id"])
                    ->where("store_id", $store_id)
                    ->select();
                if (!empty($evaluate_info)) {
                    $evaluate_info_arr[] = $evaluate_info;
                }
            }
            if (!empty($evaluate_info_arr)) {
                foreach ($evaluate_info_arr as $kk => $vv) {
                    foreach ($vv as $i => $j) {
                        $img = db("order_service_evaluate_images")
                            ->field("images")
                            ->where("evaluate_order_id", $j["id"])
                            ->select();
                        if(!empty($img)){
                            $evaluate_info_array[$kk][$i] =$j;
                            $evaluate_info_array[$kk][$i]["images"] =$img;
                            $evaluate_info_array[$kk][$i]["order_create_time"] = db("order_service")
                                ->where("id", $j["order_id"])
                                ->value("create_time");
                            $evaluate_info_array[$kk][$i]["user_info"] = db("user")
                                ->where("id", $j["user_id"])
                                ->field("user_img,phone_num")
                                ->find();
                        }

                    }
                }
                $evaluate_info_array = array_reduce($evaluate_info_array, 'array_merge', array());
                $ords =array();
                foreach ($evaluate_info_array as $vl){
                    $ords[] =intval($vl["create_time"]);
                }
                array_multisort($ords,SORT_ASC,$evaluate_info_array);
                return ajax_success("数据返回成功",$evaluate_info_array);
            } else {
                return ajax_error("没有数据", ["status" => 0]);
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