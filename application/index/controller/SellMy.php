<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/12 0012
 * Time: 14:46
 */
namespace app\index\controller;


use think\Controller;
use think\Request;
use think\Session;
use think\Db;

class  SellMy extends Controller{

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家我的页面
     **************************************
     */
    public function sell_my_index(Request $request){
        if($request->isPost()){
            //店铺的id
            $role_name_store_id = Session::get("role_name_store_id");
            if(empty($role_name_store_id)){
                exit(json_encode(array("status" => 2, "info" => "请重新登录","data"=>["status"=>0])));
            }
            //店铺信息
            $store_info = Db::name("store")
                ->field("store_logo_images,store_id,store_name,user_id")
                ->where("store_id",$role_name_store_id["store_id"])
                ->find();
            if(empty($store_info)){
                exit(json_encode(array("status" => 2, "info" => "请重新登录","data"=>["status"=>0])));
            }else{
                exit(json_encode(array("status" => 1, "info" => "店铺信息返回成功","data"=> $store_info)));
            }
            //今月账单
            $timetoday = strtotime(date("Y",time()));//今月时间戳
            $time2 = time() + 3600*24;//今天24点的时间点，两个值之间即为今天一天内的数据
            $time_condition  = "order_create_time>{$timetoday} and order_create_time< {$time2}";

            //上月账单

        }
        return view("sell_my_index");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:暂存订单信息
     **************************************
     * @param Request $request
     */
    public function  store_order_save(Request $request){
        if($request->isPost()){
            $role_name_store_id = Session::get("role_name_store_id");
            if(empty($role_name_store_id)){
                exit(json_encode(array("status" => 2, "info" => "请重新登录","data"=>["status"=>0])));
            }
           $get_numbers =$request->only("get_number")["get_number"];
           if(!empty($get_numbers)){
               //今日订单
                Session::set("get_number",$get_numbers);
               exit(json_encode(array("status" => 1, "info" => "存储成功","data"=>$get_numbers)));
           }else{
               exit(json_encode(array("status" => 0, "info" => "请重新请求","data"=>["status"=>0])));
           }
        }
    }




    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家服务订单
     **************************************
     */
    public function sell_service_order(Request $request){
        if($request->isPost()){
            $role_name_store_id = Session::get("role_name_store_id");
            if(empty($role_name_store_id)){
                exit(json_encode(array("status" => 2, "info" => "请重新登录","data"=>["status"=>0])));
            }
            $get_number =  Session::get("get_number");

            $store_id =intval($role_name_store_id["store_id"]);
            if($get_number ==1){
                //今日订单
                $timetoday = strtotime(date("Y-m-d",time()));//今天0点的时间点
                $time2 = time() + 3600*24;//今天24点的时间点，两个值之间即为今天一天内的数据
                $time_condition  = "create_time>$timetoday and create_time< $time2";
                $data =Db::name("order_service")
                    ->where($time_condition)
                    ->where("store_id",$store_id)
                    ->select();
                if(!empty($data)){
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$data)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有订单信息","data"=>["status"=>0])));
                }
            }else if($get_number ==2){

            }else if($get_number ==3){

            }else if($get_number ==4){
                //待服务
                $condition ="`status` = '2' or `status` = '3'";
                $data =Db::name("order_service")
                    ->field("service_order_number,status,service_goods_name,got_to_time,id,store_name,service_real_pay")
                    ->where($condition)
                    ->where("store_id",$role_name_store_id["store_id"])
                    ->select();
                if(!empty($data)){
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$data)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有订单信息","data"=>["status"=>0])));
                }
            }else if($get_number ==5){
                //取消订单
                $condition ="`status` = '0' or `status` = '9' or `status` = '10'";
                $data =Db::name("order_service")
                    ->field("service_order_number,status,service_goods_name,got_to_time,id,store_name,service_real_pay")
                    ->where($condition)
                    ->where("store_id",$role_name_store_id["store_id"])
                    ->select();
                if(!empty($data)){
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$data)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有订单信息","data"=>["status"=>0])));
                }
            }else if($get_number ==6){
                //已完成
                $data =Db::name("order_service")
                    ->field("service_order_number,status,service_goods_name,got_to_time,id,store_name,service_real_pay")
                    ->where('status',6)
                    ->where("store_id",$role_name_store_id["store_id"])
                    ->select();
                if(!empty($data)){
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$data)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有订单信息","data"=>["status"=>0])));
                }
            }else if($get_number ==7){

            }else if($get_number ==8){
                //新订单
//                $time1 =time();
//                $time2 = time() + 3600;//1小时后的时间点，两个值之
//                $time_condition  = "create_time>$time1 and create_time< $time2";

                $timetoday = strtotime(date("Y-m-d H:i:s",time()));//今天0点的时间点
                $time2 = strtotime(date("Y-m-d H:i:s",strtotime('-5 hour')));
                $time_condition  = "  create_time > $time2  and  create_time < $timetoday";
//                exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$time_condition)));
                $data =Db::name("order_service")
                    ->field("service_order_number,status,service_goods_name,got_to_time,id,store_name,service_real_pay")
                    ->where("store_id",$role_name_store_id["store_id"])
                    ->where($time_condition)
                    ->where("status",2)
                    ->select();
                if(!empty($data)){
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$data)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有新订单信息","data"=>["status"=>0])));
                }
            }

        }
        return view("sell_service_order");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家商品订单
     **************************************
     */
    public function sell_parts_order(Request $request){
        if($request->isPost()){
            $role_name_store_id = Session::get("role_name_store_id");
            if(empty($role_name_store_id)){
                exit(json_encode(array("status" => 2, "info" => "请重新登录","data"=>["status"=>0])));
            }
            $get_number = Session::get("get_number");
            if($get_number ==1){
                $timetoday = strtotime(date("Y-m-d",time()));//今天0点的时间点
                $time2 = time() + 3600*24;//今天24点的时间点，两个值之间即为今天一天内的数据
                $time_condition  = "order_create_time>{$timetoday} and order_create_time< {$time2}";
                $data =Db::name("order_parts")
                    ->where($time_condition)
                    ->where("store_id",$role_name_store_id["store_id"])
                    ->select();
                if(!empty($data)){
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$data)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有订单信息","data"=>["status"=>0])));
                }
            }else if($get_number ==2){

            }else if($get_number ==3){

            }else if($get_number ==4){

            }else if($get_number ==5){

            }else if($get_number ==6){

            }else if($get_number ==7){

            }else if($get_number ==8){

            }
            $data =Db::name("order_service")
                ->where("store_id",$role_name_store_id["store_id"])
                ->find();
        }
        return view("sell_parts_order");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家服务订单详情
     **************************************
     */
    public function sell_service_order_detail(){
        return view("sell_service_order_detail");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家商品订单详情
     **************************************
     */
    public function sell_parts_order_detail(){
        return view("sell_parts_order_detail");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家服务记录
     **************************************
     * @return \think\response\View
     */
    public function sell_service_record(){
        return view("sell_service_record");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家商品记录
     **************************************
     * @return \think\response\View
     */
    public function sell_parts_record(){
        return view("sell_parts_record");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家账单
     **************************************
     * @return \think\response\View
     */
    public function sell_order_bill(){
        return view("sell_order_bill");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家钱包
     **************************************
     * @return \think\response\View
     */
    public function sell_wallet(){
        return view("sell_wallet");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家提现申请
     **************************************
     * @return \think\response\View
     */
    public function sell_application(){
        return view("sell_application");
    }






}