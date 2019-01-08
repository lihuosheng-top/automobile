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
                    array_multisort($end_info,SORT_DESC,$ords);
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
                        $ords[] =$vl["order_create_times"];
                    }
                    array_multisort($end_info,SORT_DESC,$ords);
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
                        $ords[] =$vl["order_create_times"];
                    }
                    array_multisort($end_info,SORT_DESC,$ords);
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
                    array_multisort($end_info,SORT_DESC,$ords);
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$end_info)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有订单信息","data"=>["status"=>0])));
                }
            }else if($get_number ==6){
                //已完成
                $condition =" `status` = '8' ";
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
                        $ords[] =$vl["order_create_times"];
                    }
                    array_multisort($end_info,SORT_DESC,$ords);
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
                        $ords[] =$vl["order_create_times"];
                    }
                    array_multisort($end_info,SORT_DESC,$ords);
                    exit(json_encode(array("status" => 1, "info" => "订单返回成功","data"=>$end_info)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有订单信息","data"=>["status"=>0])));
                }
            }else if($get_number ==8){
                //新订单
                $timetoday = strtotime(date("Y-m-d H:i:s",time()));//今天0点的时间点
                $time2 = strtotime(date("Y-m-d H:i:s",strtotime('-6 hour')));
                $time_condition  = "order_create_time > $time2  and  order_create_time < $timetoday";
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
                    array_multisort($end_info,SORT_DESC,$ords);
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
    public function sell_service_record(){
        return view("sell_service_record");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家商品账单
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
    public function sell_wallet(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");//用户的id
            if(!empty($user_id)){
                $money =Db::name("user")->field("user_wallet")->where("id",$user_id)->find();
                if(!empty($money)){
                    exit(json_encode(array("status" => 1, "info" => "我的钱包余额返回成功","data"=>$money)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "我的钱包余额返回失败")));
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
     * Notes:卖家提现申请
     **************************************
     * @return \think\response\View
     */
    public function sell_application(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");//用户的id
            if(!empty($user_id)){
                $money =Db::name("user")->where("id",$user_id)->value("user_wallet");
            }else{
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
            $apply_money =$request->only("apply_money")["apply_money"];   //申请提现的金额
            $apply_member =$request->only("apply_member")["apply_member"];  //开户名
            $apply_bank =$request->only("apply_bank")["apply_bank"];   //开户银行
            $apply_bank_code =$request->only("apply_bank_code")["apply_bank_code"];  //开户银行卡号
            if($apply_money > $money){
                exit(json_encode(array("status" => 0, "info" =>"提现金额不能大于余额")));
            }

        }
        return view("sell_application");
    }






}