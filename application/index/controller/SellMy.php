<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/12 0012
 * Time: 14:46
 */
namespace app\index\controller;


use think\Cache;
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
            $user_id =Session::get("user");//用户id
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
            //本月账单
//            $timetoday = date("Y-m",time());//今月时间戳
//            $condition = " `operation_time` like '%{$timetoday}%' ";
            $now_data = Db::name("wallet")
                ->where("is_business",2)
                ->where("user_id",$user_id)
//                ->where($condition)
                ->sum("wallet_operation");
            if(!empty($now_data)){
                $store_info["now_bill"] =round($now_data,2);
            }else{
                $store_info["now_bill"] =0;
            }
            //上月账单
//            $last_time = date("Y-m",strtotime("-1 month"));//今月时间戳
//            $last_condition = " `operation_time` like '%{$last_time}%' ";
            $last_data = Db::name("wallet")
                ->where("is_business",2)
                ->where("user_id",$user_id)
//                ->where($last_condition)
                ->sum("wallet_operation");
            if(!empty($now_data)){
                $store_info["last_bill"] =round($last_data,2);
            }else{
                $store_info["last_bill"] =0;
            }
            if(empty($store_info)){
                exit(json_encode(array("status" => 2, "info" => "请重新登录","data"=>["status"=>0])));
            }else{
                exit(json_encode(array("status" => 1, "info" => "店铺信息返回成功","data"=> $store_info)));
            }
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
     * Notes:获取点击的按钮是哪一个
     **************************************
     */
    public function store_order_get(Request $request){
        if($request->isPost()){
            $number =Session::get("get_number");
            if(!empty($number)){
                exit(json_encode(array("status" => 1, "info" => "成功","data"=>$number)));
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
                //已服务
                $condition  = "status = 4 or status = 5";
                $data =Db::name("order_service")
                    ->where($condition)
                    ->where("store_id",$store_id)
                    ->select();
                if(!empty($data)){
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$data)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有订单信息","data"=>["status"=>0])));
                }
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
                //月销
                $month_start = strtotime(date("Y-m-01"));
                $month_end = strtotime("+1 month -1 seconds", $month_start);
                $time_condition  = "create_time>$month_start and create_time< $month_end";
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
            }else if($get_number ==8){
                //新订单
                $timetoday = strtotime(date("Y-m-d H:i:s",time()));//今天0点的时间点
                $time2 = strtotime(date("Y-m-d H:i:s",strtotime('-6 hour')));
                $time_condition  = "  create_time > $time2  and  create_time < $timetoday";
                

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
     * Notes:卖家（买家未付款）取消订单接口(ajax)
     **************************************
     * @param Request $request
     */
    public function  sell_order_service_no_pay_cancel(Request $request){
        if($request->isPost()){
            $order_id =$_POST['order_id'];
            if(!empty($order_id)){
                $res =Db::name('order_service')
                    ->where('id',$order_id)
                    ->update(['status'=>9]);
                if($res){
                    //取消订单退回积分到积余额
                    $is_use_integral=Db::name("order_service")
                        ->where('id',$order_id)
                        ->field("integral_discount_setting_id,id,integral_deductible_num,service_order_number,user_id")
                        ->find();
                    if(!empty($is_use_integral)){
//                            foreach ($is_use_integral as $keys=>$values){
                        if(!empty($is_use_integral["integral_deductible_num"])){
                            $user_info = Db::name("user")
                                ->field("user_integral_wallet,user_integral_wallet_consumed")
                                ->where("id",$is_use_integral["user_id"])->find();
                            $update_data =[
                                "user_integral_wallet"=>$user_info["user_integral_wallet"] + $is_use_integral["integral_deductible_num"],
                                "user_integral_wallet_consumed"=>$user_info["user_integral_wallet_consumed"] - $is_use_integral["integral_deductible_num"]
                            ];
                            Db::name("user")->where("id",$is_use_integral["user_id"])->update($update_data); //积分增加
                            $integral_data =[
                                "user_id"=>$is_use_integral["user_id"],//用户ID
                                "integral_operation"=>"+".$is_use_integral["integral_deductible_num"],//积分操作
                                "integral_balance"=>$user_info["user_integral_wallet"] + $is_use_integral["integral_deductible_num"],//积分余额
                                "integral_type"=> 1,//积分类型
                                "operation_time"=>date("Y-m-d H:i:s") ,//操作时间
                                "integral_remarks"=>"订单号:".$is_use_integral['service_order_number']."取消退回".$is_use_integral["integral_deductible_num"]."积分",//积分备注
                            ];
                            Db::name("integral")->insert($integral_data); //插入积分消费记录
                        }
//                            }
                    }
                    return ajax_success('订单取消成功',['status'=>1]);
                }else{
                    return ajax_error('订单取消失败',['status'=>0]);
                }
            }
        }
    }



    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商确认服务（确认收货）
     **************************************
     * @param Request $request
     */
    public function sell_order_service_already_served(Request $request){
        if($request->isPost()){
            $order_id =$_POST['order_id'];
            if(!empty($order_id)){
                $res =Db::name('order_service')
                    ->where('id',$order_id)
                    ->update(['status'=>5]);
                if($res){
                    //需要加入到商家余额里面
                    $order_info = Db::name("order_service")
                        ->field("service_real_pay,store_id,service_order_number,service_goods_name,pay_type_content,service_order_amount")
                        ->where("id",$order_id)
                        ->find();
                    $business_id =Db::name("store")->where("store_id",$order_info["store_id"])->value("user_id");
                    //                        TODO:对这个收入进行存储
                    $business_data =[
                        "user_id" =>$business_id,//商家用户id
                        "create_time"=>time(), //时间戳
                        "status"=>1, //状态值（1正常状态，-1申请提现但未处理，方便拒绝修改回状态，2提现已成功）
                        "order_num"=>$order_info["service_order_number"],//订单编号
                        "type" =>"服务商", //服务类型(配件商，服务商）
                        "money"=>$order_info["service_order_amount"], //进账的钱
                        "is_pay"=>1, //(判断是否1收入，还是-1支出）
                        "is_deduction"=>1,//正常的流程
                    ];
                    $arr =Db::name("business_wallet")->insertGetId($business_data);
                    //服务商配件商身份
                    $arr_condition ="`status` = '1' and `is_deduction` = '1' and `user_id` = ".$business_id;
                    $business_wallet =Db::name("business_wallet")
                        ->where($arr_condition)
                        ->sum("money");
                    //商家原本的钱包余额
                    //车主的身份
                    $owner_wallet =Db::name("user")
                        ->where("id",$business_id)
                        ->value("user_wallet");
                    if(!empty($business_wallet)){
                        $new_wallet =$business_wallet +$owner_wallet;
                    }else{
                        $new_wallet =$owner_wallet;
                    }
                    //添加消费记录
                    if($arr){
                        $data=[
                            "user_id"=>$business_id,
                            "wallet_operation"=>$order_info["service_order_amount"],
                            "wallet_type"=>1,
                            "operation_time"=>date("Y-m-d H:i:s"),
                            "wallet_remarks"=>"订单号：".$order_info['service_order_number']."，完成交易，收入".$order_info['service_order_amount']."元",
                            "wallet_img"=>"index/image/money2.png",
                            "title"=>$order_info["service_goods_name"],
                            "order_nums"=>$order_info["service_order_number"],
                            "pay_type"=>$order_info["pay_type_content"], //支付方式
                            "wallet_balance"=>$new_wallet,
                            "is_business"=>2,//判断是车主消费还是商家消费（1车主消费，2商家消费）
                        ];
                        Db::name("wallet")->insert($data);
                    }
                    return ajax_success('确认服务成功',['status'=>1]);
                }else{
                    return ajax_error('确认服务失败',['status'=>0]);
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商面议修改价格
     **************************************
     */
    public function sell_service_price_change(Request $request){
        if($request->isPost()){
            $id =$request->only("id")["id"];
            $price =$request->only("price")["price"];//价钱
            $bool =Db::name("order_service")
                ->where("id",$id)
                ->update(["service_real_pay"=>$price,"order_amount"=>$price,"service_order_amount"=>$price,"status"=>1]);
            if($bool){
                return ajax_success("保存价格成功",$price);
            }else{
                return ajax_error("请重新进入填写价格",["status"=>0]);
            }
        }
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
                //今日订单
                $timetoday = strtotime(date("Y-m-d",time()));//今天0点的时间点
                $time2 = time() + 3600*24;//今天24点的时间点，两个值之间即为今天一天内的数据
                $time_condition  = "order_create_time>$timetoday and order_create_time< $time2";
                $data =Db::name("order_parts")
                    ->field('parts_order_number,order_create_time,group_concat(id) order_id')
                    ->where($time_condition)
                    ->where("store_id",$role_name_store_id["store_id"])
                    ->group('parts_order_number')
                    ->select();
                foreach ($data as $key=>$value) {
                    if (strpos($value["order_id"], ",")) {
                        $order_id = explode(',', $value["order_id"]);
                        foreach ($order_id as $k=>$v){
                            $return_data_info[] = Db::name('order_parts')
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where('id', $v)
                                ->find();
                        }
                        foreach ($return_data_info as $ke => $item) {
                            $parts_order_number_all[$ke] = $item['parts_order_number'];
                        }
                        $unique_order_number = array_merge(array_unique($parts_order_number_all));

                        foreach ($unique_order_number as $da_k =>$da_v){
                            $order_data['info'][$da_k] = Db::name('order_parts')
                                ->where('parts_order_number', $da_v)
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->order('order_create_time', 'desc')
                                ->select();
                            $names = Db::name("order_parts")
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where("parts_order_number", $da_v)
                                ->find();
                            $order_data['status'][$da_k] = $names['status'];
                            $order_data["parts_order_number"][$da_k] = $names["parts_order_number"];
                            $order_data["all_order_real_pay"][$da_k] = $names["order_real_pay"];
                            $order_data["order_create_time"][$da_k] = $names["order_create_time"];
                            $order_data["store_name"][$da_k] = $names["store_name"];
                            $order_data["store_id"][$da_k] = $names["store_id"];
                            foreach ($order_data["info"] as $kk => $vv) {
                                $order_data["all_numbers"][$kk] = array_sum(array_map(create_function('$vals', 'return $vals["order_quantity"];'), $vv));
                            }
                        }
                    }
                    else {
                        $return_data = Db::name('order_parts')
                            ->where("store_id",$role_name_store_id["store_id"])
                            ->where('id', $value['order_id'])
                            ->find();
                        $data_information["all_order_real_pay"][] = $return_data["order_real_pay"];
                        $data_information["all_numbers"][] = $return_data["order_quantity"];
                        $data_information['status'][] = $return_data['status'];
                        $data_information['parts_order_number'][] = $return_data['parts_order_number'];
                        $data_information['order_create_time'][] = $value['order_create_time'];
                        $data_information['store_id'][] = $return_data['store_id'];
                        $data_information['store_name'][] =$return_data['store_name'];
                        $data_information['all'][] = Db::name('order_parts')
                            ->where("store_id",$role_name_store_id["store_id"])
                            ->where('id', $value['order_id'])
                            ->find();
                    }
                }
                if(!empty($order_data)){
                    //所有信息
                    foreach ($order_data["info"] as $i=>$j){
                        if(!empty($j)){
                            $new_arr[] =$j;
                        }
                    }
                    foreach ($new_arr as $i=>$j){
                        $end_info[$i]["info"] =$j;
                    }
                    //状态值
                    foreach ($order_data['status'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_status[] = $j;
                        }
                    }
                    foreach ($new_arr_status as $i=>$j){
                        $end_info[$i]['status'] = $j;
                    }
                    //实际支付的金额
                    foreach ($order_data['all_order_real_pay'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_pay[] =$j;
                        }
                    }
                    foreach ($new_arr_pay as $i=>$j){
                        $end_info[$i]['all_order_real_pay'] = $j;
                    }
                    //总数量
                    foreach ($order_data['all_numbers'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_numbers[] =$j;
                        }
                    }
                    foreach ($new_arr_all_numbers as $i=>$j){
                        $end_info[$i]['all_numbers'] = $j;
                    }

                    //订单编号
                    foreach ($order_data['parts_order_number'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_order_number[] =$j;
                        }
                    }
                    foreach ($new_arr_all_order_number as $i=>$j){
                        $end_info[$i]['parts_order_number'] = $j;
                    }
                    //订单创建时间
                    foreach ($order_data['order_create_time'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_order_create_time[] =$j;
                        }
                    }
                    foreach ($new_arr_order_create_time as $i=>$j){
                        $end_info[$i]['order_create_times'] = $j;
                    }
                    //店铺id
                    foreach ($order_data['store_id'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_store_id[] =$j;
                        }
                    }
                    foreach ($new_arr_all_store_id as $i=>$j){
                        $end_info[$i]['store_id'] = $j;
                    }
                    //店铺名字
                    foreach ($order_data['store_name'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_store_name[] =$j;
                        }
                    }
                    foreach ($new_arr_all_store_name as $i=>$j){
                        $end_info[$i]['store_name'] = $j;
                    }
                }
                if(!empty($data_information)){
                    if(!empty($new_arr)){
                        $count =count($new_arr);
                    }else{
                        $count =0;
                    }
                    //支付状态
                    foreach ($data_information['status'] as $a=>$b){
                        $end_info[$a+$count]['status'] = $b;
                    }
                    //总支付
                    foreach ($data_information['all_order_real_pay'] as $a=>$b){
                        $end_info[$a+$count]['all_order_real_pay'] = $b;
                    }
                    //所有数量
                    foreach ($data_information['all_numbers'] as $a=>$b){
                        $end_info[$a+$count]['all_numbers'] = $b;
                    }
                    //订单编号
                    foreach ($data_information['parts_order_number'] as $a=>$b){
                        $end_info[$a+$count]['parts_order_number'] = $b;
                    }
                    //所有信息

                    foreach ($data_information['all'] as $a=>$b){
                        $end_info[$a+$count]['info'][] = $b;
                    }
                    //创建订单时间
                    foreach ($data_information['order_create_time'] as $a=>$b){
                        $end_info[$a+$count]['order_create_times'] = $b;
                    }

                    //店铺id
                    foreach ($data_information['store_id'] as $a=>$b){
                        $end_info[$a+$count]['store_id'] = $b;
                    }

                    //店铺名字
                    foreach ($data_information['store_name'] as $a=>$b){
                        $end_info[$a+$count]['store_name'] = $b;
                    }
                }
                if(!empty($end_info)){
                    $ords =array();
                    foreach ($end_info as $vl){
                        $ords[] =$vl["order_create_times"];
                    }
                    array_multisort($ords,SORT_DESC,$end_info);
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$end_info)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有订单信息","data"=>["status"=>0])));
                }
            }else if($get_number ==2){
                //已发货
                $condition =" `status` = '4' or `status` = '5'";
                $data =Db::name("order_parts")
                    ->field('parts_order_number,order_create_time,group_concat(id) order_id')
                    ->where($condition)
                    ->where("store_id",$role_name_store_id["store_id"])
                    ->group('parts_order_number')
                    ->select();
                foreach ($data as $key=>$value) {
                    if (strpos($value["order_id"], ",")) {
                        $order_id = explode(',', $value["order_id"]);
                        foreach ($order_id as $k=>$v){
                            $return_data_info[] = Db::name('order_parts')
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where($condition)
                                ->where('id', $v)
                                ->find();
                        }
                        foreach ($return_data_info as $ke => $item) {
                            $parts_order_number_all[$ke] = $item['parts_order_number'];
                        }
                        $unique_order_number = array_merge(array_unique($parts_order_number_all));

                        foreach ($unique_order_number as $da_k =>$da_v){
                            $order_data['info'][$da_k] = Db::name('order_parts')
                                ->where('parts_order_number', $da_v)
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where($condition)
                                ->order('order_create_time', 'desc')
                                ->select();
                            $names = Db::name("order_parts")
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where($condition)
                                ->where("parts_order_number", $da_v)
                                ->find();
                            $order_data['status'][$da_k] = $names['status'];
                            $order_data["parts_order_number"][$da_k] = $names["parts_order_number"];
                            $order_data["all_order_real_pay"][$da_k] = $names["order_real_pay"];
                            $order_data["order_create_time"][$da_k] = $names["order_create_time"];
                            $order_data["store_name"][$da_k] = $names["store_name"];
                            $order_data["store_id"][$da_k] = $names["store_id"];
                            foreach ($order_data["info"] as $kk => $vv) {
                                $order_data["all_numbers"][$kk] = array_sum(array_map(create_function('$vals', 'return $vals["order_quantity"];'), $vv));
                            }
                        }
                    }
                    else {
                        $return_data = Db::name('order_parts')
                            ->where("store_id",$role_name_store_id["store_id"])
                            ->where('id', $value['order_id'])
                            ->where($condition)
                            ->find();
                        $data_information["all_order_real_pay"][] = $return_data["order_real_pay"];
                        $data_information["all_numbers"][] = $return_data["order_quantity"];
                        $data_information['status'][] = $return_data['status'];
                        $data_information['parts_order_number'][] = $return_data['parts_order_number'];
                        $data_information['order_create_time'][] = $value['order_create_time'];
                        $data_information['store_id'][] = $return_data['store_id'];
                        $data_information['store_name'][] =$return_data['store_name'];
                        $data_information['all'][] = Db::name('order_parts')
                            ->where("store_id",$role_name_store_id["store_id"])
                            ->where('id', $value['order_id'])
                            ->where($condition)
                            ->find();
                    }
                }
                if(!empty($order_data)){
                    //所有信息
                    foreach ($order_data["info"] as $i=>$j){
                        if(!empty($j)){
                            $new_arr[] =$j;
                        }
                    }
                    foreach ($new_arr as $i=>$j){
                        $end_info[$i]["info"] =$j;
                    }
                    //状态值
                    foreach ($order_data['status'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_status[] = $j;
                        }
                    }
                    foreach ($new_arr_status as $i=>$j){
                        $end_info[$i]['status'] = $j;
                    }
                    //实际支付的金额
                    foreach ($order_data['all_order_real_pay'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_pay[] =$j;
                        }
                    }
                    foreach ($new_arr_pay as $i=>$j){
                        $end_info[$i]['all_order_real_pay'] = $j;
                    }
                    //总数量
                    foreach ($order_data['all_numbers'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_numbers[] =$j;
                        }
                    }
                    foreach ($new_arr_all_numbers as $i=>$j){
                        $end_info[$i]['all_numbers'] = $j;
                    }

                    //订单编号
                    foreach ($order_data['parts_order_number'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_order_number[] =$j;
                        }
                    }
                    foreach ($new_arr_all_order_number as $i=>$j){
                        $end_info[$i]['parts_order_number'] = $j;
                    }
                    //订单创建时间
                    foreach ($order_data['order_create_time'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_order_create_time[] =$j;
                        }
                    }
                    foreach ($new_arr_order_create_time as $i=>$j){
                        $end_info[$i]['order_create_times'] = $j;
                    }
                    //店铺id
                    foreach ($order_data['store_id'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_store_id[] =$j;
                        }
                    }
                    foreach ($new_arr_all_store_id as $i=>$j){
                        $end_info[$i]['store_id'] = $j;
                    }
                    //店铺名字
                    foreach ($order_data['store_name'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_store_name[] =$j;
                        }
                    }
                    foreach ($new_arr_all_store_name as $i=>$j){
                        $end_info[$i]['store_name'] = $j;
                    }
                }
                if(!empty($data_information)){
                    if(!empty($new_arr)){
                        $count =count($new_arr);
                    }else{
                        $count =0;
                    }
                    //支付状态
                    foreach ($data_information['status'] as $a=>$b){
                        $end_info[$a+$count]['status'] = $b;
                    }
                    //总支付
                    foreach ($data_information['all_order_real_pay'] as $a=>$b){
                        $end_info[$a+$count]['all_order_real_pay'] = $b;
                    }
                    //所有数量
                    foreach ($data_information['all_numbers'] as $a=>$b){
                        $end_info[$a+$count]['all_numbers'] = $b;
                    }
                    //订单编号
                    foreach ($data_information['parts_order_number'] as $a=>$b){
                        $end_info[$a+$count]['parts_order_number'] = $b;
                    }
                    //所有信息

                    foreach ($data_information['all'] as $a=>$b){
                        $end_info[$a+$count]['info'][] = $b;
                    }
                    //创建订单时间
                    foreach ($data_information['order_create_time'] as $a=>$b){
                        $end_info[$a+$count]['order_create_times'] = $b;
                    }

                    //店铺id
                    foreach ($data_information['store_id'] as $a=>$b){
                        $end_info[$a+$count]['store_id'] = $b;
                    }

                    //店铺名字
                    foreach ($data_information['store_name'] as $a=>$b){
                        $end_info[$a+$count]['store_name'] = $b;
                    }
                }
                if(!empty($end_info)){
                    $ords =array();
                    foreach ($end_info as $vl){
                        $ords[] =$vl["pay_time"];
                    }
                    array_multisort($ords,SORT_DESC,$end_info);
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$end_info)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有订单信息","data"=>["status"=>0])));
                }
            }else if($get_number ==3){
                //已退货
                $condition =" `status` = '12'";
                $data =Db::name("order_parts")
                    ->field('parts_order_number,order_create_time,group_concat(id) order_id')
                    ->where($condition)
                    ->where("store_id",$role_name_store_id["store_id"])
                    ->group('parts_order_number')
                    ->select();
                foreach ($data as $key=>$value) {
                    if (strpos($value["order_id"], ",")) {
                        $order_id = explode(',', $value["order_id"]);
                        foreach ($order_id as $k=>$v){
                            $return_data_info[] = Db::name('order_parts')
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where('id', $v)
                                ->where($condition)
                                ->find();
                        }
                        foreach ($return_data_info as $ke => $item) {
                            $parts_order_number_all[$ke] = $item['parts_order_number'];
                        }
                        $unique_order_number = array_merge(array_unique($parts_order_number_all));

                        foreach ($unique_order_number as $da_k =>$da_v){
                            $order_data['info'][$da_k] = Db::name('order_parts')
                                ->where('parts_order_number', $da_v)
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where($condition)
                                ->order('order_create_time', 'desc')
                                ->select();
                            $names = Db::name("order_parts")
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where($condition)
                                ->where("parts_order_number", $da_v)
                                ->find();
                            $order_data['status'][$da_k] = $names['status'];
                            $order_data["parts_order_number"][$da_k] = $names["parts_order_number"];
                            $order_data["all_order_real_pay"][$da_k] = $names["order_real_pay"];
                            $order_data["order_create_time"][$da_k] = $names["order_create_time"];
                            $order_data["store_name"][$da_k] = $names["store_name"];
                            $order_data["store_id"][$da_k] = $names["store_id"];
                            foreach ($order_data["info"] as $kk => $vv) {
                                $order_data["all_numbers"][$kk] = array_sum(array_map(create_function('$vals', 'return $vals["order_quantity"];'), $vv));
                            }
                        }
                    }
                    else {
                        $return_data = Db::name('order_parts')
                            ->where("store_id",$role_name_store_id["store_id"])
                            ->where($condition)
                            ->where('id', $value['order_id'])
                            ->find();
                        $data_information["all_order_real_pay"][] = $return_data["order_real_pay"];
                        $data_information["all_numbers"][] = $return_data["order_quantity"];
                        $data_information['status'][] = $return_data['status'];
                        $data_information['parts_order_number'][] = $return_data['parts_order_number'];
                        $data_information['order_create_time'][] = $value['order_create_time'];
                        $data_information['store_id'][] = $return_data['store_id'];
                        $data_information['store_name'][] =$return_data['store_name'];
                        $data_information['all'][] = Db::name('order_parts')
                            ->where("store_id",$role_name_store_id["store_id"])
                            ->where($condition)
                            ->where('id', $value['order_id'])
                            ->find();
                    }
                }
                if(!empty($order_data)){
                    //所有信息
                    foreach ($order_data["info"] as $i=>$j){
                        if(!empty($j)){
                            $new_arr[] =$j;
                        }
                    }
                    foreach ($new_arr as $i=>$j){
                        $end_info[$i]["info"] =$j;
                    }
                    //状态值
                    foreach ($order_data['status'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_status[] = $j;
                        }
                    }
                    foreach ($new_arr_status as $i=>$j){
                        $end_info[$i]['status'] = $j;
                    }
                    //实际支付的金额
                    foreach ($order_data['all_order_real_pay'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_pay[] =$j;
                        }
                    }
                    foreach ($new_arr_pay as $i=>$j){
                        $end_info[$i]['all_order_real_pay'] = $j;
                    }
                    //总数量
                    foreach ($order_data['all_numbers'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_numbers[] =$j;
                        }
                    }
                    foreach ($new_arr_all_numbers as $i=>$j){
                        $end_info[$i]['all_numbers'] = $j;
                    }

                    //订单编号
                    foreach ($order_data['parts_order_number'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_order_number[] =$j;
                        }
                    }
                    foreach ($new_arr_all_order_number as $i=>$j){
                        $end_info[$i]['parts_order_number'] = $j;
                    }
                    //订单创建时间
                    foreach ($order_data['order_create_time'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_order_create_time[] =$j;
                        }
                    }
                    foreach ($new_arr_order_create_time as $i=>$j){
                        $end_info[$i]['order_create_times'] = $j;
                    }
                    //店铺id
                    foreach ($order_data['store_id'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_store_id[] =$j;
                        }
                    }
                    foreach ($new_arr_all_store_id as $i=>$j){
                        $end_info[$i]['store_id'] = $j;
                    }
                    //店铺名字
                    foreach ($order_data['store_name'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_store_name[] =$j;
                        }
                    }
                    foreach ($new_arr_all_store_name as $i=>$j){
                        $end_info[$i]['store_name'] = $j;
                    }
                }
                if(!empty($data_information)){
                    if(!empty($new_arr)){
                        $count =count($new_arr);
                    }else{
                        $count =0;
                    }
                    //支付状态
                    foreach ($data_information['status'] as $a=>$b){
                        $end_info[$a+$count]['status'] = $b;
                    }
                    //总支付
                    foreach ($data_information['all_order_real_pay'] as $a=>$b){
                        $end_info[$a+$count]['all_order_real_pay'] = $b;
                    }
                    //所有数量
                    foreach ($data_information['all_numbers'] as $a=>$b){
                        $end_info[$a+$count]['all_numbers'] = $b;
                    }
                    //订单编号
                    foreach ($data_information['parts_order_number'] as $a=>$b){
                        $end_info[$a+$count]['parts_order_number'] = $b;
                    }
                    //所有信息

                    foreach ($data_information['all'] as $a=>$b){
                        $end_info[$a+$count]['info'][] = $b;
                    }
                    //创建订单时间
                    foreach ($data_information['order_create_time'] as $a=>$b){
                        $end_info[$a+$count]['order_create_times'] = $b;
                    }

                    //店铺id
                    foreach ($data_information['store_id'] as $a=>$b){
                        $end_info[$a+$count]['store_id'] = $b;
                    }

                    //店铺名字
                    foreach ($data_information['store_name'] as $a=>$b){
                        $end_info[$a+$count]['store_name'] = $b;
                    }
                }
                if(!empty($end_info)){
                    $ords =array();
                    foreach ($end_info as $vl){
                        $ords[] =$vl["pay_time"];
                    }
                    array_multisort($ords,SORT_DESC,$end_info);
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$end_info)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有订单信息","data"=>["status"=>0])));
                }
            }else if($get_number ==4){
                //待服务
            }else if($get_number ==5){
                //已取消
                $condition =" `status` = '0' or `status` = '9'  or `status` = '10' ";
                $data =Db::name("order_parts")
                    ->field('parts_order_number,order_create_time,group_concat(id) order_id')
                    ->where($condition)
                    ->where("store_id",$role_name_store_id["store_id"])
                    ->group('parts_order_number')
                    ->select();
                foreach ($data as $key=>$value) {
                    if (strpos($value["order_id"], ",")) {
                        $order_id = explode(',', $value["order_id"]);
                        foreach ($order_id as $k=>$v){
                            $return_data_info[] = Db::name('order_parts')
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where($condition)
                                ->where('id', $v)
                                ->find();
                        }
                        foreach ($return_data_info as $ke => $item) {
                            $parts_order_number_all[$ke] = $item['parts_order_number'];
                        }
                        $unique_order_number = array_merge(array_unique($parts_order_number_all));

                        foreach ($unique_order_number as $da_k =>$da_v){
                            $order_data['info'][$da_k] = Db::name('order_parts')
                                ->where('parts_order_number', $da_v)
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where($condition)
                                ->select();
                            $names = Db::name("order_parts")
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where($condition)
                                ->where("parts_order_number", $da_v)
                                ->find();
                            $order_data['status'][$da_k] = $names['status'];
                            $order_data["parts_order_number"][$da_k] = $names["parts_order_number"];
                            $order_data["all_order_real_pay"][$da_k] = $names["order_real_pay"];
                            $order_data["order_create_time"][$da_k] = $names["order_create_time"];
                            $order_data["store_name"][$da_k] = $names["store_name"];
                            $order_data["store_id"][$da_k] = $names["store_id"];
                            foreach ($order_data["info"] as $kk => $vv) {
                                $order_data["all_numbers"][$kk] = array_sum(array_map(create_function('$vals', 'return $vals["order_quantity"];'), $vv));
                            }
                        }
                    }
                    else {
                        $return_data = Db::name('order_parts')
                            ->where("store_id",$role_name_store_id["store_id"])
                            ->where($condition)
                            ->where('id', $value['order_id'])
                            ->find();
                        $data_information["all_order_real_pay"][] = $return_data["order_real_pay"];
                        $data_information["all_numbers"][] = $return_data["order_quantity"];
                        $data_information['status'][] = $return_data['status'];
                        $data_information['parts_order_number'][] = $return_data['parts_order_number'];
                        $data_information['order_create_time'][] = $value['order_create_time'];
                        $data_information['store_id'][] = $return_data['store_id'];
                        $data_information['store_name'][] =$return_data['store_name'];
                        $data_information['all'][] = Db::name('order_parts')
                            ->where("store_id",$role_name_store_id["store_id"])
                            ->where($condition)
                            ->where('id', $value['order_id'])
                            ->find();
                    }
                }

                if(!empty($order_data)){
                    //所有信息
                    foreach ($order_data["info"] as $i=>$j){
                        if(!empty($j)){
                            $new_arr[] =$j;
                        }
                    }
                    foreach ($new_arr as $i=>$j){
                        $end_info[$i]["info"] =$j;
                    }
                    //状态值
                    foreach ($order_data['status'] as $i => $j) {
                            $new_arr_status[] = $j;
                    }
                    foreach ($new_arr_status as $i=>$j){
                        $end_info[$i]['status'] = $j;
                    }
                    //实际支付的金额
                    foreach ($order_data['all_order_real_pay'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_pay[] =$j;
                        }
                    }

                    foreach ($new_arr_pay as $i=>$j){
                        $end_info[$i]['all_order_real_pay'] = $j;
                    }
                    //总数量
                    foreach ($order_data['all_numbers'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_numbers[] =$j;
                        }
                    }
                    foreach ($new_arr_all_numbers as $i=>$j){
                        $end_info[$i]['all_numbers'] = $j;
                    }

                    //订单编号
                    foreach ($order_data['parts_order_number'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_order_number[] =$j;
                        }
                    }
                    foreach ($new_arr_all_order_number as $i=>$j){
                        $end_info[$i]['parts_order_number'] = $j;
                    }
                    //订单创建时间
                    foreach ($order_data['order_create_time'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_order_create_time[] =$j;
                        }
                    }
                    foreach ($new_arr_order_create_time as $i=>$j){
                        $end_info[$i]['order_create_times'] = $j;
                    }
                    //店铺id
                    foreach ($order_data['store_id'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_store_id[] =$j;
                        }
                    }
                    foreach ($new_arr_all_store_id as $i=>$j){
                        $end_info[$i]['store_id'] = $j;
                    }
                    //店铺名字
                    foreach ($order_data['store_name'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_store_name[] =$j;
                        }
                    }
                    foreach ($new_arr_all_store_name as $i=>$j){
                        $end_info[$i]['store_name'] = $j;
                    }
                }
                if(!empty($data_information)){
                    if(!empty($new_arr)){
                        $count =count($new_arr);
                    }else{
                        $count =0;
                    }
                    //支付状态
                    foreach ($data_information['status'] as $a=>$b){
                        $end_info[$a+$count]['status'] = $b;
                    }
                    //总支付
                    foreach ($data_information['all_order_real_pay'] as $a=>$b){
                        $end_info[$a+$count]['all_order_real_pay'] = $b;
                    }
                    //所有数量
                    foreach ($data_information['all_numbers'] as $a=>$b){
                        $end_info[$a+$count]['all_numbers'] = $b;
                    }
                    //订单编号
                    foreach ($data_information['parts_order_number'] as $a=>$b){
                        $end_info[$a+$count]['parts_order_number'] = $b;
                    }
                    //所有信息

                    foreach ($data_information['all'] as $a=>$b){
                        $end_info[$a+$count]['info'][] = $b;
                    }
                    //创建订单时间
                    foreach ($data_information['order_create_time'] as $a=>$b){
                        $end_info[$a+$count]['order_create_times'] = $b;
                    }

                    //店铺id
                    foreach ($data_information['store_id'] as $a=>$b){
                        $end_info[$a+$count]['store_id'] = $b;
                    }

                    //店铺名字
                    foreach ($data_information['store_name'] as $a=>$b){
                        $end_info[$a+$count]['store_name'] = $b;
                    }
                }
                if(!empty($end_info)){
                    $ords =array();
                    foreach ($end_info as $vl){
                        $ords[] =$vl["order_create_times"];
                    }
                    array_multisort($ords,SORT_DESC,$end_info);
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$end_info)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有订单信息","data"=>["status"=>0])));
                }
            }else if($get_number ==6){
                //已完成
                $condition =" `status` = '8' ";
                $data =Db::name("order_parts")
                    ->field('parts_order_number,pay_time,group_concat(id) order_id')
                    ->where($condition)
                    ->where("store_id",$role_name_store_id["store_id"])
                    ->group('parts_order_number')
                    ->select();
                foreach ($data as $key=>$value) {
                    if (strpos($value["order_id"], ",")) {
                        $order_id = explode(',', $value["order_id"]);
                        foreach ($order_id as $k=>$v){
                            $return_data_info[] = Db::name('order_parts')
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where($condition)
                                ->where('id', $v)
                                ->find();
                        }
                        foreach ($return_data_info as $ke => $item) {
                            $parts_order_number_all[$ke] = $item['parts_order_number'];
                        }
                        $unique_order_number = array_merge(array_unique($parts_order_number_all));

                        foreach ($unique_order_number as $da_k =>$da_v){
                            $order_data['info'][$da_k] = Db::name('order_parts')
                                ->where('parts_order_number', $da_v)
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where($condition)
                                ->order('pay_time', 'desc')
                                ->select();
                            $names = Db::name("order_parts")
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where($condition)
                                ->where("parts_order_number", $da_v)
                                ->find();
                            $order_data['status'][$da_k] = $names['status'];
                            $order_data["parts_order_number"][$da_k] = $names["parts_order_number"];
                            $order_data["all_order_real_pay"][$da_k] = $names["order_real_pay"];
                            $order_data["pay_time"][$da_k] = $names["pay_time"];
                            $order_data["store_name"][$da_k] = $names["store_name"];
                            $order_data["store_id"][$da_k] = $names["store_id"];
                            foreach ($order_data["info"] as $kk => $vv) {
                                $order_data["all_numbers"][$kk] = array_sum(array_map(create_function('$vals', 'return $vals["order_quantity"];'), $vv));
                            }
                        }
                    }
                    else {
                        $return_data = Db::name('order_parts')
                            ->where("store_id",$role_name_store_id["store_id"])
                            ->where($condition)
                            ->where('id', $value['order_id'])
                            ->find();
                        $data_information["all_order_real_pay"][] = $return_data["order_real_pay"];
                        $data_information["all_numbers"][] = $return_data["order_quantity"];
                        $data_information['status'][] = $return_data['status'];
                        $data_information['parts_order_number'][] = $return_data['parts_order_number'];
                        $data_information['pay_time'][] = $value['pay_time'];
                        $data_information['store_id'][] = $return_data['store_id'];
                        $data_information['store_name'][] =$return_data['store_name'];
                        $data_information['all'][] = Db::name('order_parts')
                            ->where("store_id",$role_name_store_id["store_id"])
                            ->where($condition)
                            ->where('id', $value['order_id'])
                            ->find();
                    }
                }
                if(!empty($order_data)){
                    //所有信息
                    foreach ($order_data["info"] as $i=>$j){
                        if(!empty($j)){
                            $new_arr[] =$j;
                        }
                    }
                    foreach ($new_arr as $i=>$j){
                        $end_info[$i]["info"] =$j;
                    }
                    //状态值
                    foreach ($order_data['status'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_status[] = $j;
                        }
                    }
                    foreach ($new_arr_status as $i=>$j){
                        $end_info[$i]['status'] = $j;
                    }
                    //实际支付的金额
                    foreach ($order_data['all_order_real_pay'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_pay[] =$j;
                        }
                    }
                    foreach ($new_arr_pay as $i=>$j){
                        $end_info[$i]['all_order_real_pay'] = $j;
                    }
                    //总数量
                    foreach ($order_data['all_numbers'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_numbers[] =$j;
                        }
                    }
                    foreach ($new_arr_all_numbers as $i=>$j){
                        $end_info[$i]['all_numbers'] = $j;
                    }

                    //订单编号
                    foreach ($order_data['parts_order_number'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_order_number[] =$j;
                        }
                    }
                    foreach ($new_arr_all_order_number as $i=>$j){
                        $end_info[$i]['parts_order_number'] = $j;
                    }
                    //订单创建时间
                    foreach ($order_data['pay_time'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_order_create_time[] =$j;
                        }
                    }
                    foreach ($new_arr_order_create_time as $i=>$j){
                        $end_info[$i]['pay_time'] = $j;
                    }
                    //店铺id
                    foreach ($order_data['store_id'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_store_id[] =$j;
                        }
                    }
                    foreach ($new_arr_all_store_id as $i=>$j){
                        $end_info[$i]['store_id'] = $j;
                    }
                    //店铺名字
                    foreach ($order_data['store_name'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_store_name[] =$j;
                        }
                    }
                    foreach ($new_arr_all_store_name as $i=>$j){
                        $end_info[$i]['store_name'] = $j;
                    }
                }
                if(!empty($data_information)){
                    if(!empty($new_arr)){
                        $count =count($new_arr);
                    }else{
                        $count =0;
                    }
                    //支付状态
                    foreach ($data_information['status'] as $a=>$b){
                        $end_info[$a+$count]['status'] = $b;
                    }
                    //总支付
                    foreach ($data_information['all_order_real_pay'] as $a=>$b){
                        $end_info[$a+$count]['all_order_real_pay'] = $b;
                    }
                    //所有数量
                    foreach ($data_information['all_numbers'] as $a=>$b){
                        $end_info[$a+$count]['all_numbers'] = $b;
                    }
                    //订单编号
                    foreach ($data_information['parts_order_number'] as $a=>$b){
                        $end_info[$a+$count]['parts_order_number'] = $b;
                    }
                    //所有信息

                    foreach ($data_information['all'] as $a=>$b){
                        $end_info[$a+$count]['info'][] = $b;
                    }
                    //创建订单时间
                    foreach ($data_information['pay_time'] as $a=>$b){
                        $end_info[$a+$count]['pay_time'] = $b;
                    }

                    //店铺id
                    foreach ($data_information['store_id'] as $a=>$b){
                        $end_info[$a+$count]['store_id'] = $b;
                    }

                    //店铺名字
                    foreach ($data_information['store_name'] as $a=>$b){
                        $end_info[$a+$count]['store_name'] = $b;
                    }
                }
                if(!empty($end_info)){
                    $ords =array();
                    foreach ($end_info as $vl){
                        $ords[] =$vl["pay_time"];
                    }
                    array_multisort($ords,SORT_DESC,$end_info);
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$end_info)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有订单信息","data"=>["status"=>0])));
                }
            }else if($get_number ==7){
                //月销
                $month_start = strtotime(date("Y-m-01"));
                $month_end = strtotime("+1 month -1 seconds", $month_start);
                $time_condition  = "order_create_time>$month_start and order_create_time< $month_end";
                $data =Db::name("order_parts")
                    ->field('parts_order_number,order_create_time,group_concat(id) order_id')
                    ->where($time_condition)
                    ->where("store_id",$role_name_store_id["store_id"])
                    ->group('parts_order_number')
                    ->select();
                foreach ($data as $key=>$value) {
                    if (strpos($value["order_id"], ",")) {
                        $order_id = explode(',', $value["order_id"]);
                        foreach ($order_id as $k=>$v){
                            $return_data_info[] = Db::name('order_parts')
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where('id', $v)
                                ->find();
                        }
                        foreach ($return_data_info as $ke => $item) {
                            $parts_order_number_all[$ke] = $item['parts_order_number'];
                        }
                        $unique_order_number = array_merge(array_unique($parts_order_number_all));

                        foreach ($unique_order_number as $da_k =>$da_v){
                            $order_data['info'][$da_k] = Db::name('order_parts')
                                ->where('parts_order_number', $da_v)
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->order('order_create_time', 'desc')
                                ->select();
                            $names = Db::name("order_parts")
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where("parts_order_number", $da_v)
                                ->find();
                            $order_data['status'][$da_k] = $names['status'];
                            $order_data["parts_order_number"][$da_k] = $names["parts_order_number"];
                            $order_data["all_order_real_pay"][$da_k] = $names["order_real_pay"];
                            $order_data["order_create_time"][$da_k] = $names["order_create_time"];
                            $order_data["store_name"][$da_k] = $names["store_name"];
                            $order_data["store_id"][$da_k] = $names["store_id"];
                            foreach ($order_data["info"] as $kk => $vv) {
                                $order_data["all_numbers"][$kk] = array_sum(array_map(create_function('$vals', 'return $vals["order_quantity"];'), $vv));
                            }
                        }
                    }
                    else {
                        $return_data = Db::name('order_parts')
                            ->where("store_id",$role_name_store_id["store_id"])
                            ->where('id', $value['order_id'])
                            ->find();
                        $data_information["all_order_real_pay"][] = $return_data["order_real_pay"];
                        $data_information["all_numbers"][] = $return_data["order_quantity"];
                        $data_information['status'][] = $return_data['status'];
                        $data_information['parts_order_number'][] = $return_data['parts_order_number'];
                        $data_information['order_create_time'][] = $value['order_create_time'];
                        $data_information['store_id'][] = $return_data['store_id'];
                        $data_information['store_name'][] =$return_data['store_name'];
                        $data_information['all'][] = Db::name('order_parts')
                            ->where("store_id",$role_name_store_id["store_id"])
                            ->where('id', $value['order_id'])
                            ->find();
                    }
                }
                if(!empty($order_data)){
                    //所有信息
                    foreach ($order_data["info"] as $i=>$j){
                        if(!empty($j)){
                            $new_arr[] =$j;
                        }
                    }
                    foreach ($new_arr as $i=>$j){
                        $end_info[$i]["info"] =$j;
                    }
                    //状态值
                    foreach ($order_data['status'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_status[] = $j;
                        }
                    }
                    foreach ($new_arr_status as $i=>$j){
                        $end_info[$i]['status'] = $j;
                    }
                    //实际支付的金额
                    foreach ($order_data['all_order_real_pay'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_pay[] =$j;
                        }
                    }
                    foreach ($new_arr_pay as $i=>$j){
                        $end_info[$i]['all_order_real_pay'] = $j;
                    }
                    //总数量
                    foreach ($order_data['all_numbers'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_numbers[] =$j;
                        }
                    }
                    foreach ($new_arr_all_numbers as $i=>$j){
                        $end_info[$i]['all_numbers'] = $j;
                    }

                    //订单编号
                    foreach ($order_data['parts_order_number'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_order_number[] =$j;
                        }
                    }
                    foreach ($new_arr_all_order_number as $i=>$j){
                        $end_info[$i]['parts_order_number'] = $j;
                    }
                    //订单创建时间
                    foreach ($order_data['order_create_time'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_order_create_time[] =$j;
                        }
                    }
                    foreach ($new_arr_order_create_time as $i=>$j){
                        $end_info[$i]['order_create_times'] = $j;
                    }
                    //店铺id
                    foreach ($order_data['store_id'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_store_id[] =$j;
                        }
                    }
                    foreach ($new_arr_all_store_id as $i=>$j){
                        $end_info[$i]['store_id'] = $j;
                    }
                    //店铺名字
                    foreach ($order_data['store_name'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_store_name[] =$j;
                        }
                    }
                    foreach ($new_arr_all_store_name as $i=>$j){
                        $end_info[$i]['store_name'] = $j;
                    }
                }
                if(!empty($data_information)){
                    if(!empty($new_arr)){
                        $count =count($new_arr);
                    }else{
                        $count =0;
                    }
                    //支付状态
                    foreach ($data_information['status'] as $a=>$b){
                        $end_info[$a+$count]['status'] = $b;
                    }
                    //总支付
                    foreach ($data_information['all_order_real_pay'] as $a=>$b){
                        $end_info[$a+$count]['all_order_real_pay'] = $b;
                    }
                    //所有数量
                    foreach ($data_information['all_numbers'] as $a=>$b){
                        $end_info[$a+$count]['all_numbers'] = $b;
                    }
                    //订单编号
                    foreach ($data_information['parts_order_number'] as $a=>$b){
                        $end_info[$a+$count]['parts_order_number'] = $b;
                    }
                    //所有信息

                    foreach ($data_information['all'] as $a=>$b){
                        $end_info[$a+$count]['info'][] = $b;
                    }
                    //创建订单时间
                    foreach ($data_information['order_create_time'] as $a=>$b){
                        $end_info[$a+$count]['order_create_times'] = $b;
                    }

                    //店铺id
                    foreach ($data_information['store_id'] as $a=>$b){
                        $end_info[$a+$count]['store_id'] = $b;
                    }

                    //店铺名字
                    foreach ($data_information['store_name'] as $a=>$b){
                        $end_info[$a+$count]['store_name'] = $b;
                    }
                }
                if(!empty($end_info)){
                    $ords =array();
                    foreach ($end_info as $vl){
                        $ords[] =$vl["pay_time"];
                    }
                    array_multisort($ords,SORT_DESC,$end_info);
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$end_info)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有订单信息","data"=>["status"=>0])));
                }
            }else if($get_number ==8){
                //新订单
                $timetoday = strtotime(date("Y-m-d H:i:s",time()));//今天0点的时间点
                $time2 = strtotime(date("Y-m-d H:i:s",strtotime('-6 hour')));
                $time_condition  = "pay_time > $time2  and  pay_time < $timetoday";
                $data =Db::name("order_parts")
                    ->field('parts_order_number,pay_time,group_concat(id) order_id')
                    ->where($time_condition)
                    ->where("store_id",$role_name_store_id["store_id"])
                    ->group('parts_order_number')
                    ->select();
                foreach ($data as $key=>$value) {
                    if (strpos($value["order_id"], ",")) {
                        $order_id = explode(',', $value["order_id"]);
                        foreach ($order_id as $k=>$v){
                            $return_data_info[] = Db::name('order_parts')
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where('id', $v)
                                ->find();
                        }
                        foreach ($return_data_info as $ke => $item) {
                            $parts_order_number_all[$ke] = $item['parts_order_number'];
                        }
                        $unique_order_number = array_merge(array_unique($parts_order_number_all));
                        foreach ($unique_order_number as $da_k =>$da_v){
                            $order_data['info'][$da_k] = Db::name('order_parts')
                                ->where('parts_order_number', $da_v)
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->select();
                            $names = Db::name("order_parts")
                                ->where("store_id",$role_name_store_id["store_id"])
                                ->where("parts_order_number", $da_v)
                                ->find();
                            $order_data['status'][$da_k] = $names['status'];
                            $order_data["parts_order_number"][$da_k] = $names["parts_order_number"];
                            $order_data["all_order_real_pay"][$da_k] = $names["order_real_pay"];
                            $order_data["pay_time"][$da_k] = $names["pay_time"];
                            $order_data["store_name"][$da_k] = $names["store_name"];
                            $order_data["store_id"][$da_k] = $names["store_id"];
                            foreach ($order_data["info"] as $kk => $vv) {
                                $order_data["all_numbers"][$kk] = array_sum(array_map(create_function('$vals', 'return $vals["order_quantity"];'), $vv));
                            }
                        }
                    }
                    else {
                        $return_data = Db::name('order_parts')
                            ->where("store_id",$role_name_store_id["store_id"])
                            ->where('id', $value['order_id'])
                            ->find();
                        $data_information["all_order_real_pay"][] = $return_data["order_real_pay"];
                        $data_information["all_numbers"][] = $return_data["order_quantity"];
                        $data_information['status'][] = $return_data['status'];
                        $data_information['parts_order_number'][] = $return_data['parts_order_number'];
                        $data_information['pay_time'][] = $value['pay_time'];
                        $data_information['store_id'][] = $return_data['store_id'];
                        $data_information['store_name'][] =$return_data['store_name'];
                        $data_information['all'][] = Db::name('order_parts')
                            ->where("store_id",$role_name_store_id["store_id"])
                            ->where('id', $value['order_id'])
                            ->find();
                    }
                }
                if(!empty($order_data)){
                    //所有信息
                    foreach ($order_data["info"] as $i=>$j){
                        if(!empty($j)){
                            $new_arr[] =$j;
                        }
                    }
                    foreach ($new_arr as $i=>$j){
                        $end_info[$i]["info"] =$j;
                    }
                    //状态值
                    foreach ($order_data['status'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_status[] = $j;
                        }
                    }
                    foreach ($new_arr_status as $i=>$j){
                        $end_info[$i]['status'] = $j;
                    }
                    //实际支付的金额
                    foreach ($order_data['all_order_real_pay'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_pay[] =$j;
                        }
                    }
                    foreach ($new_arr_pay as $i=>$j){
                        $end_info[$i]['all_order_real_pay'] = $j;
                    }
                    //总数量
                    foreach ($order_data['all_numbers'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_numbers[] =$j;
                        }
                    }
                    foreach ($new_arr_all_numbers as $i=>$j){
                        $end_info[$i]['all_numbers'] = $j;
                    }

                    //订单编号
                    foreach ($order_data['parts_order_number'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_order_number[] =$j;
                        }
                    }
                    foreach ($new_arr_all_order_number as $i=>$j){
                        $end_info[$i]['parts_order_number'] = $j;
                    }
                    //订单创建时间
                    foreach ($order_data['pay_time'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_order_create_time[] =$j;
                        }
                    }
                    foreach ($new_arr_order_create_time as $i=>$j){
                        $end_info[$i]['pay_time'] = $j;
                    }
                    //店铺id
                    foreach ($order_data['store_id'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_store_id[] =$j;
                        }
                    }
                    foreach ($new_arr_all_store_id as $i=>$j){
                        $end_info[$i]['store_id'] = $j;
                    }
                    //店铺名字
                    foreach ($order_data['store_name'] as $i => $j) {
                        if(!empty($j)){
                            $new_arr_all_store_name[] =$j;
                        }
                    }
                    foreach ($new_arr_all_store_name as $i=>$j){
                        $end_info[$i]['store_name'] = $j;
                    }
                }
                if(!empty($data_information)){
                    if(!empty($new_arr)){
                        $count =count($new_arr);
                    }else{
                        $count =0;
                    }
                    //支付状态
                    foreach ($data_information['status'] as $a=>$b){
                        $end_info[$a+$count]['status'] = $b;
                    }
                    //总支付
                    foreach ($data_information['all_order_real_pay'] as $a=>$b){
                        $end_info[$a+$count]['all_order_real_pay'] = $b;
                    }
                    //所有数量
                    foreach ($data_information['all_numbers'] as $a=>$b){
                        $end_info[$a+$count]['all_numbers'] = $b;
                    }
                    //订单编号
                    foreach ($data_information['parts_order_number'] as $a=>$b){
                        $end_info[$a+$count]['parts_order_number'] = $b;
                    }
                    //所有信息

                    foreach ($data_information['all'] as $a=>$b){
                        $end_info[$a+$count]['info'][] = $b;
                    }
                    //创建订单时间
                    foreach ($data_information['pay_time'] as $a=>$b){
                        $end_info[$a+$count]['pay_time'] = $b;
                    }

                    //店铺id
                    foreach ($data_information['store_id'] as $a=>$b){
                        $end_info[$a+$count]['store_id'] = $b;
                    }

                    //店铺名字
                    foreach ($data_information['store_name'] as $a=>$b){
                        $end_info[$a+$count]['store_name'] = $b;
                    }
                }
                if(!empty($end_info)){
                    $ords =array();
                    foreach ($end_info as $vl){
                        $ords[] =$vl["pay_time"];
                    }
                    array_multisort($ords,SORT_DESC,$end_info);
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$end_info)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有订单信息","data"=>["status"=>0])));
                }

            }
        }
        return view("sell_parts_order");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商订单保存订单编号给订单详情
     **************************************
     * @param Request $request
     */
    public function sell_order_service_save_record(Request $request){
        if($request->isPost()){
            $service_order_number =$request->only("service_order_number")["service_order_number"];//配件商订单编号
            if(!empty($service_order_number)){
                Session::set("sell_service_order_number", $service_order_number);
                return ajax_success("暂存成功",['status'=>1]);
            }else{
                return ajax_error("没有这个订单编号",["status"=>0]);
            }
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家服务订单详情
     **************************************
     */
    public function sell_service_order_detail(Request $request){
        if($request->isPost()){
            $order_id=Session::get("sell_service_order_number");
            $data =Db::name("order_service")
                ->where("service_order_number",$order_id)
                ->find();
            if(!empty($data)){
                return ajax_success("订单信息返回成功",$data);
            }else{
                return ajax_error("没有对应的订单信息",["status"=>0]);
            }
        }
        return view("sell_service_order_detail");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家进入订单详情首先保存的信息
     **************************************
     * @param Request $request
     */
    public function sell_parts_save_record(Request $request){
        if($request->isPost()){
            $store_id =$request->only("sell_store_id")["sell_store_id"];//店铺id
            $parts_order_number =$request->only("sell_parts_order_number")["sell_parts_order_number"];//配件商订单编号
            if(!empty($store_id)){
                Session::set("sell_store_id",$store_id);
                Session::set("sell_parts_order_number",$parts_order_number);
                return ajax_success("暂存成功",['status'=>1]);
            }else{
                return ajax_error("店铺id不能为空",["status"=>0]);
            }
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家商品订单详情
     **************************************
     */
    public function sell_parts_order_detail(Request $request){
        if($request->isPost()){
            $store_id = Session::get("sell_store_id");
            $parts_order_number = Session::get("sell_parts_order_number");//订单编号
            $condition =  " `store_id` = " . $store_id . " and `parts_order_number` = " . $parts_order_number;
            $data = Db::name("order_parts")
                ->where($condition)
                ->select();
            if (!empty($data)) {
                $datas["store_id"] = $data[0]["store_id"];//店铺id
                $datas["buy_message"] = $data[0]["buy_message"];//买家留言
                $datas["store_name"] = $data[0]["store_name"];//店铺名称
                $datas["create_time"] = $data[0]["order_create_time"];//订单创建时间
                $datas["parts_order_number"] = $data[0]["parts_order_number"];//订单编号
                $datas["pay_time"] = $data[0]["pay_time"]; //支付时间
                $datas["harvester"] = $data[0]["harvester"];//收货人
                $datas["harvest_phone_num"] = $data[0]["harvest_phone_num"];//收件人电话
                $datas["harvester_address"] = $data[0]["harvester_address"];//收件人地址
                $datas["status"] = $data[0]["status"];//状态
                foreach ($data as $ks=>$vs){
                    $datas["all_goods_money"][] =$vs["goods_money"]*$vs["order_quantity"];
                }
                $datas["all_goods_pays"] =array_sum($datas["all_goods_money"]); //商品总额（商品*数量）
                $datas["normal_future_time"] = $data[0]["normal_future_time"];//正常订单未付款自动关闭的时间
                $datas["all_order_real_pay"] = $data[0]["order_real_pay"];//订单实际支付
                $datas["all_numbers"] = array_sum(array_map(create_function('$vals', 'return $vals["order_quantity"];'), $data));//订单总数量
                $datas["integral_deductible"] = $data[0]["integral_deductible"];//抵扣积分钱
                $datas["info"] = $data;
                if (!empty($datas)) {
                    return ajax_success("数据返回成功", $datas);
                } else {
                    return ajax_error("没有数据信息", ["status" => 0]);
                }
            } else {
                return ajax_error("订单信息错误", ["status" => 0]);
            }
        }
        return view("sell_parts_order_detail");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家服务记录
     **************************************
     * @return \think\response\View
     */
    public function sell_service_record(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");//用户id
            $now_time_one =date("Y");
            $condition = " `operation_time` like '%{$now_time_one}%' ";
            $data = Db::name("wallet")
                ->where("user_id",$user_id)
                ->where("is_business",2)
                ->where($condition)
                ->order("operation_time","desc")
                ->select();
            $datas =array(
                "january"=>[
                    "name"=>[]
                ],
                "february"=>[
                    "name"=>[]
                ],
                "march"=>[
                    "name"=>[]
                ],
                "april"=>[
                    "name"=>[]
                ],
                "may"=>[
                    "name"=>[]
                ],
                "june"=>[
                    "name"=>[]
                ],
                "july"=>[
                    "name"=>[]
                ],
                "august"=>[
                    "name"=>[]
                ],
                "september"=>[
                    "name"=>[]
                ],
                "october"=>[
                    "name"=>[]
                ],
                "november"=>[
                    "name"=>[]
                ],
                "december"=>[
                    "name"=>[]
                ],
            );
            foreach ($data as $ks=>$vs){
                if(strpos($vs["operation_time"],$now_time_one."-01") !==false){
                    $datas["january"]["name"][] =$vs;
                    if($vs["wallet_type"]== -1){
                        $data_res[0]["expenditure"][] =$vs["wallet_operation"]; //支出
                    }else if($vs["wallet_type"]== 1){
                        $data_res[0]["income"][] =$vs["wallet_operation"]; //收入
                    }
                } else if(strpos($vs["operation_time"],$now_time_one."-02")  !==false){
                    $datas["sebruary"]["name"][] =$vs;
                    if($vs["wallet_type"]== -1){
                        $data_res[1]["expenditure"][] =$vs["wallet_operation"]; //支出
                    }else if($vs["wallet_type"]== 1){
                        $data_res[1]["income"][] =$vs["wallet_operation"]; //收入
                    }
                }else if(strpos($vs["operation_time"],$now_time_one."-03")  !==false){
                    $datas["march"]["name"][]=$vs;
                    if($vs["wallet_type"]== -1){
                        $data_res[2]["expenditure"][] =$vs["wallet_operation"]; //支出
                    }else if($vs["wallet_type"]== 1){
                        $data_res[2]["income"][] =$vs["wallet_operation"]; //收入
                    }
                }else if(strpos($vs["operation_time"],$now_time_one."-04")  !==false){
                    $datas["april"]["name"][]=$vs;
                    if($vs["wallet_type"]== -1){
                        $data_res[3]["expenditure"][] =$vs["wallet_operation"]; //支出
                    }else if($vs["wallet_type"]== 1){
                        $data_res[3]["income"][] =$vs["wallet_operation"]; //收入
                    }
                }else if(strpos($vs["operation_time"],$now_time_one."-05")  !==false){
                    $datas["may"]["name"][]=$vs;
                    if($vs["wallet_type"]== -1){
                        $data_res[4]["expenditure"][] =$vs["wallet_operation"]; //支出
                    }else if($vs["wallet_type"]== 1){
                        $data_res[4]["income"][] =$vs["wallet_operation"]; //收入
                    }
                }else if(strpos($vs["operation_time"],$now_time_one."-06")  !==false){
                    $datas["june"]["name"][] =$vs;
                    if($vs["wallet_type"]== -1){
                        $data_res[5]["expenditure"][] =$vs["wallet_operation"]; //支出
                    }else if($vs["wallet_type"]== 1){
                        $data_res[5]["income"][] =$vs["wallet_operation"]; //收入
                    }
                }else if(strpos($vs["operation_time"],$now_time_one."-07")  !==false){
                    $datas["july"]["name"][] =$vs;
                    if($vs["wallet_type"]== -1){
                        $data_res[6]["expenditure"][] =$vs["wallet_operation"]; //支出
                    }else if($vs["wallet_type"]== 1){
                        $data_res[6]["income"][] =$vs["wallet_operation"]; //收入
                    }
                }else if(strpos($vs["operation_time"],$now_time_one."-08")  !==false){
                    $datas["august"]["name"][] =$vs;
                    if($vs["wallet_type"]== -1){
                        $data_res[7]["expenditure"][] =$vs["wallet_operation"]; //支出
                    }else if($vs["wallet_type"]== 1){
                        $data_res[7]["income"][] =$vs["wallet_operation"]; //收入
                    }
                }else if(strpos($vs["operation_time"],$now_time_one."-09") !==false){
                    $datas["september"]["name"][] =$vs;
                    if($vs["wallet_type"]== -1){
                        $data_res[8]["expenditure"][] =$vs["wallet_operation"]; //支出
                    }else if($vs["wallet_type"]== 1){
                        $data_res[8]["income"][] =$vs["wallet_operation"]; //收入
                    }
                }else if(strpos($vs["operation_time"],$now_time_one."-10") !==false){
                    $datas["october"]["name"][]=$vs;
                    if($vs["wallet_type"]== -1){
                        $data_res[9]["expenditure"][] =$vs["wallet_operation"]; //支出
                    }else if($vs["wallet_type"]== 1){
                        $data_res[9]["income"][] =$vs["wallet_operation"]; //收入
                    }
                }else if(strpos($vs["operation_time"],$now_time_one."-11") !==false){
                    $datas["november"]["name"][] =$vs;
                    if($vs["wallet_type"]== -1){
                        $data_res[10]["expenditure"][] =$vs["wallet_operation"]; //支出
                    }else if($vs["wallet_type"]== 1){
                        $data_res[10]["income"][] =$vs["wallet_operation"]; //收入
                    }
                }else if(strpos($vs["operation_time"],$now_time_one."-12") !==false){
                    $datas["december"]["name"][] =$vs;
                    if($vs["wallet_type"]== -1){
                        $data_res[11]["expenditure"][] =$vs["wallet_operation"]; //支出
                    }else if($vs["wallet_type"]== 1){
                        $data_res[11]["income"][] =$vs["wallet_operation"]; //收入
                    }
                }
            }
            if(!empty($data_res)){
                foreach ($data_res as $ks=>$vs){
                    if(!empty($vs["income"])){
                        if($ks==0){
                            $datas["january"]["income"] =array_sum($data_res[$ks]["income"]);
                        }elseif ($ks==1){
                            $datas["sebruary"]["income"] =array_sum($data_res[$ks]["income"]);
                        }elseif ($ks==2){
                            $datas["march"]["income"] =array_sum($data_res[$ks]["income"]);
                        }elseif ($ks==3){
                            $datas["april"]["income"] =array_sum($data_res[$ks]["income"]);
                        }elseif ($ks==4){
                            $datas["may"]["income"] =array_sum($data_res[$ks]["income"]);
                        }elseif ($ks==5){
                            $datas["june"]["income"] =array_sum($data_res[$ks]["income"]);
                        }elseif ($ks==6){
                            $datas["july"]["income"] =array_sum($data_res[$ks]["income"]);
                        }elseif ($ks==7){
                            $datas["august"]["income"] =array_sum($data_res[$ks]["income"]);
                        }elseif ($ks==8){
                            $datas["september"]["income"] =array_sum($data_res[$ks]["income"]);
                        }elseif ($ks==9){
                            $datas["october"]["income"] =array_sum($data_res[$ks]["income"]);
                        }elseif ($ks==10){
                            $datas["november"]["income"] =array_sum($data_res[$ks]["income"]);
                        }elseif ($ks==11){
                            $datas["december"]["income"] =array_sum($data_res[$ks]["income"]);
                        }
                    }else{
                        if($ks==0){
                            $datas["january"]["income"] =0;
                        }elseif ($ks==1){
                            $datas["sebruary"]["income"] =0;
                        }elseif ($ks==2){
                            $datas["march"]["income"] =0;
                        }elseif ($ks==3){
                            $datas["april"]["income"] =0;
                        }elseif ($ks==4){
                            $datas["may"]["income"] =0;
                        }elseif ($ks==5){
                            $datas["june"]["income"] =0;
                        }elseif ($ks==6){
                            $datas["july"]["income"] =0;
                        }elseif ($ks==7){
                            $datas["august"]["income"] =0;
                        }elseif ($ks==8){
                            $datas["september"]["income"] =0;
                        }elseif ($ks==9){
                            $datas["october"]["income"] =0;
                        }elseif ($ks==10){
                            $datas["november"]["income"] =0;
                        }elseif ($ks==11){
                            $datas["december"]["income"] =0;
                        }
                    }
                    if(!empty($vs["expenditure"])){
                        if($ks==0){
                            $datas["january"]["expenditure"] =array_sum($data_res[$ks]["expenditure"]);
                        }elseif ($ks==1){
                            $datas["sebruary"]["expenditure"] =array_sum($data_res[$ks]["expenditure"]);
                        }elseif ($ks==2){
                            $datas["march"]["expenditure"] =array_sum($data_res[$ks]["expenditure"]);
                        }elseif ($ks==3){
                            $datas["april"]["expenditure"] =array_sum($data_res[$ks]["expenditure"]);
                        }elseif ($ks==4){
                            $datas["may"]["expenditure"] =array_sum($data_res[$ks]["expenditure"]);
                        }elseif ($ks==5){
                            $datas["june"]["expenditure"] =array_sum($data_res[$ks]["expenditure"]);
                        }elseif ($ks==6){
                            $datas["july"]["expenditure"] =array_sum($data_res[$ks]["expenditure"]);
                        }elseif ($ks==7){
                            $datas["august"]["expenditure"] =array_sum($data_res[$ks]["expenditure"]);
                        }elseif ($ks==8){
                            $datas["september"]["expenditure"] =array_sum($data_res[$ks]["expenditure"]);
                        }elseif ($ks==9){
                            $datas["october"]["expenditure"] =array_sum($data_res[$ks]["expenditure"]);
                        }elseif ($ks==10){
                            $datas["november"]["expenditure"] =array_sum($data_res[$ks]["expenditure"]);
                        }elseif ($ks==11){
                            $datas["december"]["expenditure"] =array_sum($data_res[$ks]["expenditure"]);
                        }
                    }else{
                        if($ks==0){
                            $datas["january"]["expenditure"] =0;
                        }elseif ($ks==1){
                            $datas["sebruary"]["expenditure"] =0;
                        }elseif ($ks==2){
                            $datas["march"]["expenditure"] =0;
                        }elseif ($ks==3){
                            $datas["april"]["expenditure"] =0;
                        }elseif ($ks==4){
                            $datas["may"]["expenditure"] =0;
                        }elseif ($ks==5){
                            $datas["june"]["expenditure"] =0;
                        }elseif ($ks==6){
                            $datas["july"]["expenditure"] =0;
                        }elseif ($ks==7){
                            $datas["august"]["expenditure"] =0;
                        }elseif ($ks==8){
                            $datas["september"]["expenditure"] =0;
                        }elseif ($ks==9){
                            $datas["october"]["expenditure"] =0;
                        }elseif ($ks==10){
                            $datas["november"]["expenditure"] =0;
                        }elseif ($ks==11){
                            $datas["december"]["expenditure"] =0;
                        }
                    }
                }

            }
            $res =[
                "wallet_record"=>$datas
            ];
            if(!empty($data)){
                return ajax_success("消费细节返回成功",$res);
            }else{
                return ajax_error("暂无消费记录",["status"=>0]);
            }
        }

        return view("sell_service_record");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家商品账单(已写到一块)
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
    public function sell_order_bill(Request $request){
        if($request->isPost()){
            $wallet_id =Session::get("wallet_id");
            if(!empty($wallet_id)){
                $data =Db::name("wallet")
                    ->where("wallet_id",$wallet_id)
                    ->find();
                if(!empty($data)){
                    return ajax_success("消费详情返回成功",$data);
                }
            }else{
                return ajax_error("请重新刷新",["status"=>0]);
            }
        }
        return view("sell_order_bill");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家钱包
     **************************************
     * @return \think\response\View
     */
    public function sell_wallet(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");//用户的id
            if(!empty($user_id)){
               //这是商家所有余额
                $arr_condition ="`status` = '1' and `is_deduction` = '1' and `user_id` = ".$user_id;
                $money =Db::name("business_wallet")
                    ->where($arr_condition)
                    ->sum("able_money");
                if(!empty($money)){
                    //这是可提现资金（客户要求只能体现上两周的资金）
//                    $two_weekds_ago = mktime(0,0,0,date("m"),date("d")-14,date("Y")); //时间戳
//                    $two_condition ="`status` = '1' and `is_deduction` = '1' and `user_id` = ".$user_id;
//                    $tow_weeks_money =Db::name("business_wallet")
//                        ->where($two_condition)
//                        ->where("create_time","<",$two_weekds_ago)
//                        ->sum("money");

                    //这是可提现资金（客户要求只能体现上两周的资金）
                    $two_weekds_ago = mktime(0,0,0,date("m"),date("d")-7,date("Y")); //时间戳
                    $two_condition ="`status` = '1' and `is_deduction` = '1'and `is_pay` = '1' and `user_id` = ".$user_id;
                    $moneys =Db::name("business_wallet")
                        ->where($two_condition)
                        ->where("create_time","<",$two_weekds_ago)
                        ->sum("able_money");
                    $pays_condition ="`status` = '1' and   `is_pay` = '-1' and  `is_deduction` = '1' and `user_id` = ".$user_id;
                    $business_wallet_pay =Db::name("business_wallet")
                        ->where($pays_condition)
                        ->where("create_time","<",time())
                        ->sum("able_money");
                    $tow_weeks_money =$moneys + $business_wallet_pay;
                    if($tow_weeks_money>0){
                        $tow_weeks_money =round($tow_weeks_money,2);
                    }else{
                        $tow_weeks_money =0;
                    }
                    exit(json_encode(array("status" => 1, "info" => "我的钱包余额返回成功","data"=>["tow_weeks_money"=>$tow_weeks_money,"user_wallet"=>round($money,2)])));
                }else{
                    exit(json_encode(array("status" => 1, "info" => "我的钱包余额返回成功","data"=>["tow_weeks_money"=>0,"user_wallet"=>0])));
                }
            }else{
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
        }
        return view("sell_wallet");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家提现申请（此接口已不用）
     **************************************
     * @return \think\response\View
     */
    public function sell_application(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");//用户的id
            if(!empty($user_id)){
                //这是可提现资金（客户要求只能体现上周的资金）
                $two_weekds_ago = mktime(0,0,0,date("m"),date("d")-7,date("Y")); //时间戳
                $two_condition ="`status` = '1' and `is_deduction` = '1' and `user_id` = ".$user_id;
                $moneys =Db::name("business_wallet")
                    ->where($two_condition)
                    ->where("create_time","<",$two_weekds_ago)
                    ->sum("money");
                $pays_condition ="`status` = '1' and   `is_pay` = '-1' and  `is_deduction` = '1' and `user_id` = ".$user_id;
                $business_wallet_pay =Db::name("business_wallet")
                    ->where($pays_condition)
                    ->where("create_time","<",time())
                    ->sum("money");
                $money =round($moneys + $business_wallet_pay,2);
            }else{
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
            $apply_money =$request->only("apply_money")["apply_money"];   //申请提现的金额
            $apply_member =$request->only("apply_member")["apply_member"];  //开户名
            $apply_bank =$request->only("apply_bank")["apply_bank"];   //开户银行
            $apply_bank_code =$request->only("apply_bank_code")["apply_bank_code"];  //开户银行卡号
            if($apply_money > $money){
                exit(json_encode(array("status" => 0, "info" =>"提现金额不能大于可提现余额")));
            }
            $time=date("Y-m-d",time());
            $v=explode('-',$time);
            $time_second=date("H:i:s",time());
            $vs=explode(':',$time_second);
            $parts_order_number =$v[0].$v[1].$v[2].$vs[0].$vs[1].$vs[2].rand(1000,9999).$user_id; //订单编号
            $data =[
                "user_id"=>$user_id,
                "operation_time"=>date("Y-m-d H:i:s"),
                "operation_type"=>-1,
                "operation_amount"=>$apply_money,
                "pay_type_content"=>"银行卡",
                "money_status"=>2,
                "recharge_describe"=>"申请提现".$apply_money."元",
                "img_url"=>"index/image/back.png",
                "back_member"=>$apply_member,//用户名
                "bank_card"=>$apply_bank_code,//开户银行卡
                "back_name"=>$apply_bank,//开户银行
                "status"=>2
            ];
           $res = Db::name("recharge_reflect")->insertGetId($data);
           if($res){
               //余额状态修改为-1 （状态值（1正常状态，-1申请提现但未处理，方便拒绝修改回状态，2提现已成功）
               $business_wallet_data =Db::name("business_wallet")
                   ->where($two_condition)
                   ->where("create_time","<",$two_weekds_ago)
                   ->select();
               //把状态都改为体现待审核状态
               if(!empty($business_wallet_data)){
                   foreach ($business_wallet_data as $key=>$value){
                       $business_wallet_ids[] =$value["id"]; //存起来方便提现不通过的时候修改状态为正常状态1
                       Db::name("business_wallet")
                           ->where("id",$value["id"])
                           ->update(["status"=>-1]);
                   }
                   $wallet_income_ids = implode(",",$business_wallet_ids);
                 Db::name("recharge_reflect")->where("id",$res)->update(["wallet_income_ids"=>$wallet_income_ids]);
               }
               //商家余额消费进行状态修改（支出部分，即用商家角色进行购买商品(当前时间之前的都进行修改))
               $pay_condition ="`status` = '1' and   `is_pay` = '-1' and  `is_deduction` = '1' and `user_id` = ".$user_id;
               $business_wallet_pay =Db::name("business_wallet")
                   ->where($pay_condition)
                   ->where("create_time","<",time())
                   ->select();
               if(!empty($business_wallet_pay)){
                   foreach ($business_wallet_pay as $k=>$v){
                       $business_wallet_id[]  =$v["id"]; //存起来方便提现不通过的时候修改状态为正常状态1
                       Db::name("business_wallet")
                           ->where("id",$v["id"])
                           ->update(["is_deduction"=>-1]);
                   }
                   $wallet_expenditure_ids = implode(",",$business_wallet_id);
                   Db::name("recharge_reflect")->where("id",$res)->update(["wallet_expenditure_ids"=>$wallet_expenditure_ids]);
               }

               $new_condition = "`status` = '1' and `is_deduction` = '1'  and  `user_id` = " . $user_id;
               $business_wallets = Db::name("business_wallet")
                   ->where($new_condition)
                   ->sum("money");
               $owner_wallets = Db::name("user")->where("id", $user_id)->value("user_wallet");
               $new_wallet = $business_wallets+$owner_wallets;
               //进行消费记录
               $wallet_data =[
                   "user_id"=>$user_id,
                   "wallet_operation"=> -$apply_money,//消费金额
                   "wallet_type"=>-1, //消费类型（1获得，-1消费）
                   "operation_time"=>date("Y-m-d H:i:s"),//操作时间
                   "wallet_remarks"=>"提现申请".$apply_money."元",
                   "wallet_img"=>"index/image/back.png",
                   "title"=>"提现",
                   "order_nums"=>$parts_order_number,//订单编号
                   "pay_type"=>"余额抵扣", //支付宝微信支付
                   "wallet_balance"=>$new_wallet, //余额
                   "is_business"=>2,
               ];
               Db::name("wallet")->insert($wallet_data);
               exit(json_encode(array("status" => 1, "info" =>"提现成功")));
           }else{
               exit(json_encode(array("status" => 0, "info" =>"提现失败")));
           }

        }
        return view("sell_application");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家申请提现
     **************************************
     * @param Request $request
     */
    public function sell_cash_withdrawal(){
        return view("sell_cash_withdrawal");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:微信提现
     **************************************
     */
    public function  sell_withdrawal_by_wechat(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");//用户的id
            $is_type =$request->only(["is_type"])["is_type"];//提现方式：1为微信，2为支付宝，3为银行卡
            if(!empty($user_id)){
                //这是可提现资金（客户要求只能提现上周的资金）
                $two_weekds_ago = mktime(0,0,0,date("m"),date("d")-7,date("Y")); //时间戳
                $two_condition ="`status` = '1' and `is_deduction` = '1' and `user_id` = ".$user_id;
                $moneys =Db::name("business_wallet")
                    ->where($two_condition)
                    ->where("create_time","<",$two_weekds_ago)
                    ->sum("able_money");
                $pays_condition ="`status` = '1' and   `is_pay` = '-1' and  `is_deduction` = '1' and `user_id` = ".$user_id;
                $business_wallet_pay =Db::name("business_wallet")
                    ->where($pays_condition)
                    ->where("create_time","<",time())
                    ->sum("able_money");
                $money =round($moneys + $business_wallet_pay,2);
            }else{
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
            $apply_money =$request->only("apply_money")["apply_money"];   //申请提现的金额
            $wechat_count =$request->only("wechat_count")["wechat_count"];  //微信账号
            if($apply_money > $money){
                exit(json_encode(array("status" => 0, "info" =>"提现金额不能大于可提现余额")));
            }
            $time=date("Y-m-d",time());
            $v=explode('-',$time);
            $time_second=date("H:i:s",time());
            $vs=explode(':',$time_second);
            $parts_order_number =$v[0].$v[1].$v[2].$vs[0].$vs[1].$vs[2].rand(1000,9999).$user_id; //订单编号
            $data =[
                "user_id"=>$user_id,
                "operation_time"=>date("Y-m-d H:i:s"),
                "operation_type"=>-1,
                "operation_amount"=>$apply_money,
                "pay_type_content"=>"微信",
                "money_status"=>2,
                "recharge_describe"=>"申请提现".$apply_money."元",
                "img_url"=>"index/image/back.png",
                "wechat_count"=>$wechat_count,//微信账号
                "status"=>2,
                "is_type"=>$is_type
            ];
            $res = Db::name("recharge_reflect")->insertGetId($data);
            if($res){
                //余额状态修改为-1 （状态值（1正常状态，-1申请提现但未处理，方便拒绝修改回状态，2提现已成功）
                $business_wallet_data =Db::name("business_wallet")
                    ->where($two_condition)
                    ->where("create_time","<",$two_weekds_ago)
                    ->select();
                //把状态都改为体现待审核状态
                if(!empty($business_wallet_data)){
                    foreach ($business_wallet_data as $key=>$value){
                        $business_wallet_ids_arr[] =$value["id"]; //存起来方便提现不通过的时候修改状态为正常状态1
                        Db::name("business_wallet")
                            ->where("id",$value["id"])
                            ->update(["status"=>-1]);
                    }
                    $wallet_income_ids = implode(",",$business_wallet_ids_arr);
                    Db::name("recharge_reflect")->where("id",$res)->update(["wallet_income_ids"=>$wallet_income_ids]);
                    if($apply_money < $money){
                       $result_money =$money - $apply_money; //剩下的那部分钱，需要保存到之前数据里面
                        Db::name("business_wallet")
                            ->where("id",$business_wallet_ids_arr[0])
                            ->update(["is_deduction"=>1,"able_money"=>$result_money,"status"=>1]);
                    }
                }
                //商家余额消费进行状态修改（支出部分，即用商家角色进行购买商品(当前时间之前的都进行修改))
                $pay_condition ="`status` = '1' and   `is_pay` = '-1' and  `is_deduction` = '1' and `user_id` = ".$user_id;
                $business_wallet_pay =Db::name("business_wallet")
                    ->where($pay_condition)
                    ->where("create_time","<",time())
                    ->select();
                if(!empty($business_wallet_pay)){
                    foreach ($business_wallet_pay as $k=>$v){
                        $business_wallet_id[]  =$v["id"]; //存起来方便提现不通过的时候修改状态为正常状态1
                        Db::name("business_wallet")
                            ->where("id",$v["id"])
                            ->update(["is_deduction"=>-1]);
                    }
                    $wallet_expenditure_ids = implode(",",$business_wallet_id);
                  $result =  Db::name("recharge_reflect")->where("id",$res)->update(["wallet_expenditure_ids"=>$wallet_expenditure_ids]);
                }

                $new_condition = "`status` = '1' and `is_deduction` = '1'  and  `user_id` = " . $user_id;
                $business_wallets = Db::name("business_wallet")
                    ->where($new_condition)
                    ->sum("able_money");
                $owner_wallets = Db::name("user")->where("id", $user_id)->value("user_wallet");
                $new_wallet = $business_wallets+$owner_wallets;
                //进行消费记录
                $wallet_data =[
                    "user_id"=>$user_id,
                    "wallet_operation"=> -$apply_money,//消费金额
                    "wallet_type"=>-1, //消费类型（1获得，-1消费）
                    "operation_time"=>date("Y-m-d H:i:s"),//操作时间
                    "wallet_remarks"=>"提现申请".$apply_money."元",
                    "wallet_img"=>"index/image/back.png",
                    "title"=>"提现",
                    "order_nums"=>$parts_order_number,//订单编号
                    "pay_type"=>"余额抵扣", //支付宝微信支付
                    "wallet_balance"=>$new_wallet, //余额
                    "is_business"=>2,
                ];
                Db::name("wallet")->insert($wallet_data);
                exit(json_encode(array("status" => 1, "info" =>"提现成功")));
            }else{
                exit(json_encode(array("status" => 0, "info" =>"提现失败")));
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:支付宝提现
     **************************************
     */
    public function  sell_withdrawal_by_alipay(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");//用户的id
            $is_type =$request->only(["is_type"])["is_type"];//提现方式：1为微信，2为支付宝，3为银行卡
            if(!empty($user_id)){
                //这是可提现资金（客户要求只能提现上周的资金）
                $two_weekds_ago = mktime(0,0,0,date("m"),date("d")-7,date("Y")); //时间戳
                $two_condition ="`status` = '1' and `is_deduction` = '1' and `user_id` = ".$user_id;
                $moneys =Db::name("business_wallet")
                    ->where($two_condition)
                    ->where("create_time","<",$two_weekds_ago)
                    ->sum("money");
                $pays_condition ="`status` = '1' and   `is_pay` = '-1' and  `is_deduction` = '1' and `user_id` = ".$user_id;
                $business_wallet_pay =Db::name("business_wallet")
                    ->where($pays_condition)
                    ->where("create_time","<",time())
                    ->sum("money");
                $money =round($moneys + $business_wallet_pay,2);
            }else{
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
            $apply_money =$request->only("apply_money")["apply_money"];   //申请提现的金额
            $lipay_count =$request->only("alipay_count")["alipay_count"];  //开户名
            if($apply_money > $money){
                exit(json_encode(array("status" => 0, "info" =>"提现金额不能大于可提现余额")));
            }
            $time=date("Y-m-d",time());
            $v=explode('-',$time);
            $time_second=date("H:i:s",time());
            $vs=explode(':',$time_second);
            $parts_order_number =$v[0].$v[1].$v[2].$vs[0].$vs[1].$vs[2].rand(1000,9999).$user_id; //订单编号
            $data =[
                "user_id"=>$user_id,
                "operation_time"=>date("Y-m-d H:i:s"),
                "operation_type"=>-1,
                "operation_amount"=>$apply_money,
                "pay_type_content"=>"银行卡",
                "money_status"=>2,
                "recharge_describe"=>"申请提现".$apply_money."元",
                "img_url"=>"index/image/back.png",
                "lipay_count"=>$lipay_count,//支付宝账号
                "status"=>2,
                "is_type"=>$is_type
            ];
            $res = Db::name("recharge_reflect")->insertGetId($data);
            if($res){
                //余额状态修改为-1 （状态值（1正常状态，-1申请提现但未处理，方便拒绝修改回状态，2提现已成功）
                $business_wallet_data =Db::name("business_wallet")
                    ->where($two_condition)
                    ->where("create_time","<",$two_weekds_ago)
                    ->select();
                //把状态都改为体现待审核状态
                if(!empty($business_wallet_data)){
                    foreach ($business_wallet_data as $key=>$value){
                        $business_wallet_ids_arr[] =$value["id"]; //存起来方便提现不通过的时候修改状态为正常状态1
                        Db::name("business_wallet")
                            ->where("id",$value["id"])
                            ->update(["status"=>-1]);
                    }
                    $wallet_income_ids = implode(",",$business_wallet_ids_arr);
                    Db::name("recharge_reflect")->where("id",$res)->update(["wallet_income_ids"=>$wallet_income_ids]);
                    if($apply_money < $money){
                        $result_money =$money - $apply_money; //剩下的那部分钱，需要保存到之前数据里面
                        Db::name("business_wallet")
                            ->where("id",$business_wallet_ids_arr[0])
                            ->update(["is_deduction"=>1,"able_money"=>$result_money,"status"=>1]);
                    }
                }
                //商家余额消费进行状态修改（支出部分，即用商家角色进行购买商品(当前时间之前的都进行修改))
                $pay_condition ="`status` = '1' and   `is_pay` = '-1' and  `is_deduction` = '1' and `user_id` = ".$user_id;
                $business_wallet_pay =Db::name("business_wallet")
                    ->where($pay_condition)
                    ->where("create_time","<",time())
                    ->select();
                if(!empty($business_wallet_pay)){
                    foreach ($business_wallet_pay as $k=>$v){
                        $business_wallet_id[]  =$v["id"]; //存起来方便提现不通过的时候修改状态为正常状态1
                        Db::name("business_wallet")
                            ->where("id",$v["id"])
                            ->update(["is_deduction"=>-1]);
                    }
                    $wallet_expenditure_ids = implode(",",$business_wallet_id);
                    Db::name("recharge_reflect")->where("id",$res)->update(["wallet_expenditure_ids"=>$wallet_expenditure_ids]);
                }

                $new_condition = "`status` = '1' and `is_deduction` = '1'  and  `user_id` = " . $user_id;
                $business_wallets = Db::name("business_wallet")
                    ->where($new_condition)
                    ->sum("able_money");
                $owner_wallets = Db::name("user")->where("id", $user_id)->value("user_wallet");
                $new_wallet = $business_wallets+$owner_wallets;
                //进行消费记录
                $wallet_data =[
                    "user_id"=>$user_id,
                    "wallet_operation"=> -$apply_money,//消费金额
                    "wallet_type"=>-1, //消费类型（1获得，-1消费）
                    "operation_time"=>date("Y-m-d H:i:s"),//操作时间
                    "wallet_remarks"=>"提现申请".$apply_money."元",
                    "wallet_img"=>"index/image/back.png",
                    "title"=>"提现",
                    "order_nums"=>$parts_order_number,//订单编号
                    "pay_type"=>"余额抵扣", //支付宝微信支付
                    "wallet_balance"=>$new_wallet, //余额
                    "is_business"=>2,
                ];
                Db::name("wallet")->insert($wallet_data);
                exit(json_encode(array("status" => 1, "info" =>"提现成功")));
            }else{
                exit(json_encode(array("status" => 0, "info" =>"提现失败")));
            }

        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:银行卡提现
     **************************************
     */
    public function  sell_withdrawal_by_bank(Request $request){
        if($request->isPost()){
            $is_type =$request->only(["is_type"])["is_type"];//提现方式：1为微信，2为支付宝，3为银行卡
            if(!empty($user_id)){
                //这是可提现资金（客户要求只能提现上周的资金）
                $two_weekds_ago = mktime(0,0,0,date("m"),date("d")-7,date("Y")); //时间戳
                $two_condition ="`status` = '1' and `is_deduction` = '1' and `user_id` = ".$user_id;
                $moneys =Db::name("business_wallet")
                    ->where($two_condition)
                    ->where("create_time","<",$two_weekds_ago)
                    ->sum("money");
                $pays_condition ="`status` = '1' and   `is_pay` = '-1' and  `is_deduction` = '1' and `user_id` = ".$user_id;
                $business_wallet_pay =Db::name("business_wallet")
                    ->where($pays_condition)
                    ->where("create_time","<",time())
                    ->sum("money");
                $money =round($moneys + $business_wallet_pay,2);
            }else{
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
            $apply_money =$request->only("apply_money")["apply_money"];   //申请提现的金额
            $apply_member =$request->only("apply_member")["apply_member"];  //开户名
            $apply_bank =$request->only("apply_bank")["apply_bank"];   //开户银行
            $apply_bank_code =$request->only("apply_bank_code")["apply_bank_code"];  //开户银行卡号
            if($apply_money > $money){
                exit(json_encode(array("status" => 0, "info" =>"提现金额不能大于可提现余额")));
            }
            $time=date("Y-m-d",time());
            $v=explode('-',$time);
            $time_second=date("H:i:s",time());
            $vs=explode(':',$time_second);
            $parts_order_number =$v[0].$v[1].$v[2].$vs[0].$vs[1].$vs[2].rand(1000,9999).$user_id; //订单编号
            $data =[
                "user_id"=>$user_id,
                "operation_time"=>date("Y-m-d H:i:s"),
                "operation_type"=>-1,
                "operation_amount"=>$apply_money,
                "pay_type_content"=>"银行卡",
                "money_status"=>2,
                "recharge_describe"=>"申请提现".$apply_money."元",
                "img_url"=>"index/image/back.png",
                "back_member"=>$apply_member,//用户名
                "bank_card"=>$apply_bank_code,//开户银行卡
                "back_name"=>$apply_bank,//开户银行
                "status"=>2,
                "is_type"=>$is_type
            ];
            $res = Db::name("recharge_reflect")->insertGetId($data);
            if($res){
                //余额状态修改为-1 （状态值（1正常状态，-1申请提现但未处理，方便拒绝修改回状态，2提现已成功）
                $business_wallet_data =Db::name("business_wallet")
                    ->where($two_condition)
                    ->where("create_time","<",$two_weekds_ago)
                    ->select();
                //把状态都改为体现待审核状态
                if(!empty($business_wallet_data)){
                    foreach ($business_wallet_data as $key=>$value){
                        $business_wallet_ids_arr[] =$value["id"]; //存起来方便提现不通过的时候修改状态为正常状态1
                        Db::name("business_wallet")
                            ->where("id",$value["id"])
                            ->update(["status"=>-1]);
                    }
                    $wallet_income_ids = implode(",",$business_wallet_ids_arr);
                    Db::name("recharge_reflect")->where("id",$res)->update(["wallet_income_ids"=>$wallet_income_ids]);
                    if($apply_money < $money){
                        $result_money =$money - $apply_money; //剩下的那部分钱，需要保存到之前数据里面
                        Db::name("business_wallet")
                            ->where("id",$business_wallet_ids_arr[0])
                            ->update(["is_deduction"=>1,"able_money"=>$result_money,"status"=>1]);
                    }
                }
                //商家余额消费进行状态修改（支出部分，即用商家角色进行购买商品(当前时间之前的都进行修改))
                $pay_condition ="`status` = '1' and   `is_pay` = '-1' and  `is_deduction` = '1' and `user_id` = ".$user_id;
                $business_wallet_pay =Db::name("business_wallet")
                    ->where($pay_condition)
                    ->where("create_time","<",time())
                    ->select();
                if(!empty($business_wallet_pay)){
                    foreach ($business_wallet_pay as $k=>$v){
                        $business_wallet_id[]  =$v["id"]; //存起来方便提现不通过的时候修改状态为正常状态1
                        Db::name("business_wallet")
                            ->where("id",$v["id"])
                            ->update(["is_deduction"=>-1]);
                    }
                    $wallet_expenditure_ids = implode(",",$business_wallet_id);
                    Db::name("recharge_reflect")->where("id",$res)->update(["wallet_expenditure_ids"=>$wallet_expenditure_ids]);
                }

                $new_condition = "`status` = '1' and `is_deduction` = '1'  and  `user_id` = " . $user_id;
                $business_wallets = Db::name("business_wallet")
                    ->where($new_condition)
                    ->sum("able_money");
                $owner_wallets = Db::name("user")->where("id", $user_id)->value("user_wallet");
                $new_wallet = $business_wallets+$owner_wallets;
                //进行消费记录
                $wallet_data =[
                    "user_id"=>$user_id,
                    "wallet_operation"=> -$apply_money,//消费金额
                    "wallet_type"=>-1, //消费类型（1获得，-1消费）
                    "operation_time"=>date("Y-m-d H:i:s"),//操作时间
                    "wallet_remarks"=>"提现申请".$apply_money."元",
                    "wallet_img"=>"index/image/back.png",
                    "title"=>"提现",
                    "order_nums"=>$parts_order_number,//订单编号
                    "pay_type"=>"余额抵扣", //支付宝微信支付
                    "wallet_balance"=>$new_wallet, //余额
                    "is_business"=>2,
                ];
                Db::name("wallet")->insert($wallet_data);
                exit(json_encode(array("status" => 1, "info" =>"提现成功")));
            }else{
                exit(json_encode(array("status" => 0, "info" =>"提现失败")));
            }


        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:校验支付密码
     **************************************
     */
    public function check_password(Request $request){
        //验证支付密码
        $user_id = Session::get("user");
        $user_info = Db::name("user")->field("pay_passwd")->where("id", $user_id)->find();//用户信息
        $password = $request->only("passwords")["passwords"]; //输入的密码
        if (password_verify($password,$user_info["pay_passwd"])){
            return ajax_success("支付密码正确",["status"=>1]);
        }else{
            return ajax_error("支付密码错误",["status"=>0]);
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:提现历史记录（保存上一次提现的数据）
     **************************************
     */
    public function withdrawal_history(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");
            $is_type =$request->only(["is_type"])["is_type"];//提现方式：1为微信，2为支付宝，3为银行卡
            $data =Db::name("recharge_reflect")
                ->field("back_member,bank_card,back_name,wechat_count,alipay_count")
                ->where("user_id",$user_id)
                ->where("is_type",$is_type)
                ->order("id","desc")
                ->limit(1)
                ->select();
            if(!empty($data)){
                return ajax_success("信息数据返回成功",$data);
            }else{
                return ajax_error("没有数据返回",["status"=>0]);
            }
        }
    }


}