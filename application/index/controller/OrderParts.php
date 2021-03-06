<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/26 0026
 * Time: 18:39
 */
namespace app\index\controller;

use app\index\model\shopping_shop;
use think\Controller;
use  think\Request;
use  think\Db;
use think\Session;

class OrderParts extends Controller{
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态全部订单页面
     **************************************
     * @return \think\response\View
     */
    public function order_parts_all(){
        return view('order_parts_all');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单进入详情需要存储的订单编号和店铺id
     **************************************
     */
    public function order_parts_save_record(Request $request){
            if($request->isPost()){
                $store_id =$request->only("store_id")["store_id"];//店铺id
                $parts_order_number =$request->only("parts_order_number")["parts_order_number"];//配件商订单编号
                $parts_status = $request->only("status")["status"];//订单状态
                if(!empty($store_id)){
                    Session::set("store_id",$store_id);//店铺id
                    Session::set("parts_order_number",$parts_order_number);//订单编号
                    Session::set("parts_status",$parts_status); //状态
                    return ajax_success("暂存成功",['status'=>1]);
                }else{
                    return ajax_error("店铺id不能为空",["status"=>0]);
                }
            }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单详情页面
     **************************************
     * @return \think\response\View
     */
    public function order_parts_detail(Request $request){
        if($request->isPost()) {
            $user_id = intval(Session::get("user"));
            $store_id = intval(Session::get("store_id"));
            $parts_order_number = Session::get("parts_order_number");//订单编号
            $parts_status = intval(Session::get("parts_status"));//订单状态
            if($parts_status ==2 || $parts_status==3){
               $status_condition = " `status` = 2 or `status` = 3 " ;
            }else{
                $status_condition = " `status` = ".$parts_status ;
            }
            $condition = "`user_id` = " . $user_id . " and `store_id` = " . $store_id . " and `parts_order_number` = " . $parts_order_number;
            $data = Db::name("order_parts")
                ->where($condition)
                ->where($status_condition)
                ->order("order_amount","desc")
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

                $datas["all_numbers"] = array_sum(array_map(create_function('$vals', 'return $vals["order_quantity"];'), $data));//订单总数量
                $datas["integral_deductible"] = $data[0]["integral_deductible"];//抵扣积分钱
                $datas["info"] = $data;
//                $datas["all_order_real_pay"] = $data[0]["order_real_pay"];//订单实际支付
                if(!empty($data[0]["integral_deductible"])){
                    $datas["all_order_real_pay"] =round((array_sum($datas["all_goods_money"]) - $data[0]["integral_deductible"]),2);//订单实际支付
                }else{
                    $datas["all_order_real_pay"] =round(array_sum($datas["all_goods_money"]),2); //订单实际支付
                }

                if (!empty($datas)) {
                    Session::set("store_id",null);
                    Session::set("parts_order_number",null);
                    Session::set("parts_status",null);
                    return ajax_success("数据返回成功", $datas);
                } else {
                    return ajax_error("没有数据信息", ["status" => 0]);
                }
            }else {
                return ajax_error("订单信息错误", ["status" => 0]);
            }
        }

        return view('order_parts_detail');
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:未付款判断时间是否过了订单设置的时间，过了则进行自动关闭
     **************************************
     * @param Request $request
     */
    public function order_parts_detail_cancel(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");
            $store_id =$request->only('store_id')["store_id"];//店铺id
            $cancel_order_description =$request->only('cancel_order_description')["cancel_order_description"];//取消原因
            $parts_order_number =$request->only("parts_order_number")["parts_order_number"];//订单编号
            if(!empty($store_id)&&!empty($parts_order_number)){
                $res =Db::name("order_parts")
                    ->where("parts_order_number",$parts_order_number)
                    ->where("store_id",$store_id)
                    ->select();
                if(!empty($res)){
                    $normal_future_time =$res[0]["normal_future_time"];//未来时间（超过则自动关闭有积分抵扣退回积分抵扣）
                    $new_time =time();
                    if($new_time >= $normal_future_time){
                        foreach($res as $k=>$v){
                            $is_use_integral[$k] =Db::name("order_parts")
                                ->field("integral_discount_setting_id,id,integral_deductible_num")
                                ->where("id",$v["id"])
                                ->having("integral_discount_setting_id","NEQ",NULL)
                                ->group("integral_discount_setting_id")
                                ->find();
                            $data =[
                                "status"=>9,
                                "cancel_order_description"=>$cancel_order_description
                            ];
                            $bool =Db::name("order_parts")->where("id",$v["id"])->update($data);
                        }
                        if($bool){
                            //取消订单退回积分到积余额
                            if(!empty( $is_use_integral)){
                                if(!empty($is_use_integral[0]["integral_deductible_num"])){
                                    $user_info = Db::name("user")->field("user_integral_wallet,user_integral_wallet_consumed")->where("id",$user_id)->find();
                                    $update_data =[
                                        "user_integral_wallet"=>$user_info["user_integral_wallet"] + $is_use_integral[0]["integral_deductible_num"],
                                        "user_integral_wallet_consumed"=>$user_info["user_integral_wallet_consumed"] - $is_use_integral[0]["integral_deductible_num"]
                                    ];
                                    Db::name("user")->where("id",$user_id)->update($update_data); //积分增加
                                    $integral_data =[
                                        "user_id"=>$user_id,//用户ID
                                        "integral_operation"=>"+".$is_use_integral[0]["integral_deductible_num"],//积分操作
                                        "integral_balance"=>$user_info["user_integral_wallet"] + $is_use_integral[0]["integral_deductible_num"],//积分余额
                                        "integral_type"=> 1,//积分类型
                                        "operation_time"=>date("Y-m-d H:i:s") ,//操作时间
                                        "integral_remarks"=>"订单号:".$parts_order_number."因超时未付款，取消退回".$is_use_integral[0]["integral_deductible_num"]."积分",//积分备注
                                    ];
                                    Db::name("integral")->insert($integral_data); //插入积分消费记录
                                }
                            }
                            return ajax_success("取消成功",["status"=>1]);
                        }else{
                            return ajax_error("取消失败",["status"=>0]);
                        }
                    }else{
                        return ajax_error("还未到达自动取消订单时间",["status"=>0]);
                    }
                }
            }else{
                return ajax_error("所传参数不能为空",["status"=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态全部订单接口
     **************************************
     * @param Request $request
     */
    public function   ios_api_order_parts_all(Request $request)
    {
        if ($request->isPost()) {
            $datas = session('member');
            if (!empty($datas)) {
                $member_id = Db::name('user')->field('id')->where('phone_num', $datas['phone_num'])->find();
                if (!empty($datas)) {
                    $data = Db::name('order_parts')
                        ->field('parts_order_number,group_concat(id) order_parts_id')
                        ->where('user_id', $member_id['id'])
                        ->order('order_create_time', 'desc')
                        ->group('parts_order_number')
                        ->select();
                    foreach ($data as $key => $value) {
                        if (strpos($value['order_parts_id'], ',')) {
                            $order_id = explode(',', $value['order_parts_id']);
                            foreach ($order_id as $k => $v) {
                                $return_data[] = Db::name('order_parts')
                                    ->where('id', $v)
                                    ->where('user_id', $member_id['id'])
                                    ->find();
                            }
                            foreach ($return_data as $ke => $item) {
                                $order_store_id[] = $item['store_id'];
                            }
                            $da_store_id = array_unique($order_store_id); //去重之后的商户
                            foreach ($da_store_id as $da_k => $da_v) {

                                $order_undate['info'][] = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->select();
                                $names = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->find();
                                $order_undate['store_name'][] = $names['store_name'];
                                $order_undate['store_id'][] = $names['store_id'];
                                $order_undate['status'][] = $names['status'];
                                $order_undate["parts_order_number"][] =$names["parts_order_number"];
                                $order_undate["order_create_time"][] = $names["order_create_time"];
                                foreach ($order_undate["info"] as  $kk=>$vv){
                                    $order_undate["all_numbers"][$kk] =array_sum(array_map(create_function('$vals','return $vals["order_quantity"];'),$vv));
                                    $order_undate["all_order_real_pay"][$kk] = array_sum(array_map(create_function('$vals','return $vals["order_amount"];'),$vv));
                                }
                            }
                        }
                        else{
                            $return_datas = Db::name('order_parts')->where('id', $value['order_parts_id'])->find();
                            $data_infomation["all_order_real_pay"][] =$return_datas["order_amount"];
                            $data_infomation["all_numbers"][] =$return_datas["order_quantity"];
                            $data_infomation['name'][]= $return_datas['store_name'];
                            $data_infomation['store_id'][]= $return_datas['store_id'];
                            $data_infomation['status'][] = $return_datas['status'];
                            $data_infomation['parts_order_number'][] = $return_datas['parts_order_number'];
                            $data_infomation['order_create_time'][] = $return_datas['order_create_time'];
                            $data_infomation['all'][] = Db::name('order_parts')->where('id', $value['order_parts_id'])->find();
                        }
                    };
                    if (!empty($order_undate)) {
                        foreach ($order_undate['info'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr[] =$j;
                            }
                        }
                        foreach ($new_arr as $i=>$j){
                            $end_info[$i]['info'] = $j;
                        }

                        foreach ($order_undate['store_name'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_name[] =$j;
                            }
                        }
                        foreach ($new_arr_name as $i=>$j){
                            $end_info[$i]['store_name'] = $j;
                        }

                        foreach ($order_undate['status'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_status[] = $j;
                            }
                        }
                        foreach ($new_arr_status as $i=>$j){
                            $end_info[$i]['status'] = $j;
                        }

                        foreach ($order_undate['all_order_real_pay'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_pay[] =$j;
                            }
                        }
                        foreach ($new_arr_pay as $i=>$j){
                            $end_info[$i]['all_order_real_pay'] = $j;
                        }

                        foreach ($order_undate['all_numbers'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_all_numbers[] =$j;
                            }
                        }
                        foreach ($new_arr_all_numbers as $i=>$j){
                            $end_info[$i]['all_numbers'] = $j;
                        }

                        foreach ($order_undate['store_id'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_all_store_id[] =$j;
                            }
                        }
                        foreach ($new_arr_all_store_id as $i=>$j){
                            $end_info[$i]['store_id'] = $j;
                        }

                        foreach ($order_undate['parts_order_number'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_all_order_number[] =$j;
                            }
                        }
                        foreach ($new_arr_all_order_number as $i=>$j){
                            $end_info[$i]['parts_order_number'] = $j;
                        }
                        foreach ($order_undate['order_create_time'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_order_create_time[] =$j;
                            }
                        }
                        foreach ($new_arr_order_create_time as $i=>$j){
                            $end_info[$i]['order_create_time'] = $j;
                        }


                    }

                    if(!empty($data_infomation)){
                        if(!empty($new_arr)){
                            $coutn =count($new_arr);
                        }else{
                            $coutn =0;
                        }
                        //店铺名字
                        foreach ($data_infomation['name'] as $a=>$b){
                            $end_info[$a+$coutn]['store_name'] = $b;
                        }
                        //支付状态
                        foreach ($data_infomation['status'] as $a=>$b){
                            $end_info[$a+$coutn]['status'] = $b;
                        }
                        //总支付
                        foreach ($data_infomation['all_order_real_pay'] as $a=>$b){
                            $end_info[$a+$coutn]['all_order_real_pay'] = $b;
                        }
                        //所有数量
                        foreach ($data_infomation['all_numbers'] as $a=>$b){
                            $end_info[$a+$coutn]['all_numbers'] = $b;
                        }
                        //订单编号
                        foreach ($data_infomation['parts_order_number'] as $a=>$b){
                            $end_info[$a+$coutn]['parts_order_number'] = $b;
                        }
                        //店铺id
                        foreach ($data_infomation['store_id'] as $a=>$b){
                            $end_info[$a+$coutn]['store_id'] = $b;
                        }
                        //所有信息
                        foreach ($data_infomation['all'] as $a=>$b){
                            $end_info[$a+$coutn]['info'][] = $b;
                        }
                        //时间
                        foreach ($data_infomation['order_create_time'] as $a=>$b){
                            $end_info[$a+$coutn]['order_create_time'] = $b;
                        }
                    }
                    if (!empty($end_info)) {
                        //把退货的和待评价的区分开
//                        foreach ($end_info as $imt => $em){
//                            if(count($em["info"]) >2){
//                                foreach ($em["info"] as $h =>$s){
//
//                                }
//                            }
//                        }

                        $ords =array();
                        foreach ($end_info as $vl){
                            $ords[] =intval($vl["order_create_time"]);
                        }
                        array_multisort($ords,SORT_DESC,$end_info);
                        return ajax_success('数据', $end_info);
                    } else {
                        return ajax_error('没数据');
                    }
                } else {
                    return ajax_error('请登录', ['status' => 0]);
                }
            }
        }
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态待付款页面
     **************************************
     * @return \think\response\View
     */
    public function order_parts_wait_pay(){
        return view('order_parts_wait_pay');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态待付款接口
     **************************************
     * @param Request $request
     */
    public function   ios_api_order_parts_wait_pay(Request $request){
        if ($request->isPost()) {
            $datas = session('member');
            if (!empty($datas)) {
                $member_id = Db::name('user')->field('id')->where('phone_num', $datas['phone_num'])->find();
                if (!empty($datas)) {
                    $data = Db::name('order_parts')
                        ->field('parts_order_number,group_concat(id) order_parts_id')
                        ->where('user_id', $member_id['id'])
                        ->where("status",1)
                        ->order('order_create_time', 'desc')
                        ->group('parts_order_number')
                        ->select();
                    foreach ($data as $key => $value) {
                        if (strpos($value['order_parts_id'], ',')) {
                            $order_id = explode(',', $value['order_parts_id']);
                            foreach ($order_id as $k => $v) {
                                $return_data[] = Db::name('order_parts')
                                    ->where('id', $v)
                                    ->where("status",1)
                                    ->where('user_id', $member_id['id'])
                                    ->find();
                            }
                            foreach ($return_data as $ke => $item) {
                                $order_store_id[] = $item['store_id'];
                            }
                            $da_store_id = array_unique($order_store_id); //去重之后的商户
                            foreach ($da_store_id as $da_k => $da_v) {
                                $order_undate['info'][] = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where("status",1)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->select();

                                $names = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where("status",1)
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->find();
                                $order_undate['store_name'][] = $names['store_name'];
                                $order_undate['store_id'][] = $names['store_id'];
                                $order_undate['status'][] = $names['status'];
                                $order_undate["parts_order_number"][] =$names["parts_order_number"];
                                $order_undate["all_order_real_pay"][] = $names["order_real_pay"];
                                $order_undate["order_create_time"][] = $names["order_create_time"];
                                foreach ($order_undate["info"] as  $kk=>$vv){
                                    $order_undate["all_numbers"][$kk] =array_sum(array_map(create_function('$vals','return $vals["order_quantity"];'),$vv));
                                }
                            }
                        }
                        else{
                            $return_datas = Db::name('order_parts')
                                ->where('id', $value['order_parts_id'])
                                ->where("status",1)
                                ->find();
                            $data_infomation["all_order_real_pay"][] =$return_datas["order_real_pay"];
                            $data_infomation["all_numbers"][] =$return_datas["order_quantity"];
                            $data_infomation['name'][]= $return_datas['store_name'];
                            $data_infomation['store_id'][]= $return_datas['store_id'];
                            $data_infomation['status'][] = $return_datas['status'];
                            $data_infomation['parts_order_number'][] = $return_datas['parts_order_number'];
                            $data_infomation['order_create_time'][] = $return_datas['order_create_time'];
                            $data_infomation['all'][] = Db::name('order_parts')
                                ->where('id', $value['order_parts_id'])
                                ->where("status",1)
                                ->find();
                        }
                    };

                    if (!empty($order_undate)) {
                        foreach ($order_undate['info'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr[] =$j;
                            }
                        }
                        foreach ($new_arr as $i=>$j){
                            $end_info[$i]['info'] = $j;
                        }

                        foreach ($order_undate['store_name'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_name[] =$j;
                            }
                        }
                        foreach ($new_arr_name as $i=>$j){
                            $end_info[$i]['store_name'] = $j;
                        }

                        foreach ($order_undate['status'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_status[] = $j;
                            }
                        }
                        foreach ($new_arr_status as $i=>$j){
                            $end_info[$i]['status'] = $j;
                        }

                        foreach ($order_undate['all_order_real_pay'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_pay[] =$j;
                            }
                        }
                        foreach ($new_arr_pay as $i=>$j){
                            $end_info[$i]['all_order_real_pay'] = $j;
                        }

                        foreach ($order_undate['all_numbers'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_all_numbers[] =$j;
                            }
                        }
                        foreach ($new_arr_all_numbers as $i=>$j){
                            $end_info[$i]['all_numbers'] = $j;
                        }

                        foreach ($order_undate['store_id'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_all_store_id[] =$j;
                            }
                        }
                        foreach ($new_arr_all_store_id as $i=>$j){
                            $end_info[$i]['store_id'] = $j;
                        }

                        foreach ($order_undate['parts_order_number'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_all_order_number[] =$j;
                            }
                        }
                        foreach ($new_arr_all_order_number as $i=>$j){
                            $end_info[$i]['parts_order_number'] = $j;
                        }
                        foreach ($order_undate['order_create_time'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_order_create_time[] =$j;
                            }
                        }
                        foreach ($new_arr_order_create_time as $i=>$j){
                            $end_info[$i]['order_create_time'] = $j;
                        }


                    }

                    if(!empty($data_infomation)){
                        if(!empty($new_arr)){
                            $coutn =count($new_arr);
                        }else{
                            $coutn =0;
                        }
                        //店铺名字
                        foreach ($data_infomation['name'] as $a=>$b){
                            $end_info[$a+$coutn]['store_name'] = $b;
                        }
                        //支付状态
                        foreach ($data_infomation['status'] as $a=>$b){
                            $end_info[$a+$coutn]['status'] = $b;
                        }
                        //总支付
                        foreach ($data_infomation['all_order_real_pay'] as $a=>$b){
                            $end_info[$a+$coutn]['all_order_real_pay'] = $b;
                        }
                        //所有数量
                        foreach ($data_infomation['all_numbers'] as $a=>$b){
                            $end_info[$a+$coutn]['all_numbers'] = $b;
                        }
                        //订单编号
                        foreach ($data_infomation['parts_order_number'] as $a=>$b){
                            $end_info[$a+$coutn]['parts_order_number'] = $b;
                        }
                        //店铺id
                        foreach ($data_infomation['store_id'] as $a=>$b){
                            $end_info[$a+$coutn]['store_id'] = $b;
                        }
                        //所有信息
                        foreach ($data_infomation['all'] as $a=>$b){
                            $end_info[$a+$coutn]['info'][] = $b;
                        }
                        //时间
                        foreach ($data_infomation['order_create_time'] as $a=>$b){
                            $end_info[$a+$coutn]['order_create_time'] = $b;
                        }
                    }
                    if (!empty($end_info)) {
                        $ords =array();
                        foreach ($end_info as $vl){
                            $ords[] =intval($vl["order_create_time"]);
                        }
                        array_multisort($ords,SORT_DESC,$end_info);
                        return ajax_success('数据', $end_info);
                    } else {
                        return ajax_error('没数据');
                    }
                } else {
                    return ajax_error('请登录', ['status' => 0]);
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态待收货
     **************************************
     * @return \think\response\View
     */
    public function order_wait_deliver(){
        return view('order_wait_deliver');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态待收货接口
     **************************************
     * @param Request $request
     */
    public function   ios_api_order_wait_deliver(Request $request){
        if ($request->isPost()) {
            $datas = session('member');
            if (!empty($datas)) {
                $member_id = Db::name('user')->field('id')->where('phone_num', $datas['phone_num'])->find();
                if (!empty($datas)) {
                    $condition ="`status` = '2' or `status` = '3' or `status` = '4' or `status` = '5' ";
                    $data = Db::name('order_parts')
                        ->field('parts_order_number,group_concat(id) order_parts_id')
                        ->where('user_id', $member_id['id'])
                        ->where($condition)
                        ->order('order_create_time', 'desc')
                        ->group('parts_order_number')
                        ->select();
                    foreach ($data as $key => $value) {
                        if (strpos($value['order_parts_id'], ',')) {
                            $order_id = explode(',', $value['order_parts_id']);
                            foreach ($order_id as $k => $v) {
                                $return_data[] = Db::name('order_parts')
                                    ->where('id', $v)
                                    ->where($condition)
                                    ->where('user_id', $member_id['id'])
                                    ->find();
                            }
                            foreach ($return_data as $ke => $item) {
                                $order_store_id[] = $item['store_id'];
                            }
                            $da_store_id = array_unique($order_store_id); //去重之后的商户
                            foreach ($da_store_id as $da_k => $da_v) {
                                $order_undate['info'][] = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->where($condition)
//                                    ->order('order_create_time', 'desc')
                                    ->select();

                                $names = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->where($condition)
                                    ->find();
                                $order_undate['store_name'][] = $names['store_name'];
                                $order_undate['store_id'][] = $names['store_id'];
                                $order_undate['status'][] = $names['status'];
                                $order_undate["parts_order_number"][] =$names["parts_order_number"];
                                $order_undate["all_order_real_pay"][] = $names["order_real_pay"];
                                $order_undate["order_create_time"][] = $names["order_create_time"];
                                foreach ($order_undate["info"] as  $kk=>$vv){
                                    $order_undate["all_numbers"][$kk] =array_sum(array_map(create_function('$vals','return $vals["order_quantity"];'),$vv));
                                }
                            }
                        }
                        else{
                            $return_datas = Db::name('order_parts')
                                ->where('id', $value['order_parts_id'])
                                ->where($condition)
                                ->find();
                            $data_infomation["all_order_real_pay"][] =$return_datas["order_real_pay"];
                            $data_infomation["all_numbers"][] =$return_datas["order_quantity"];
                            $data_infomation['name'][]= $return_datas['store_name'];
                            $data_infomation['store_id'][]= $return_datas['store_id'];
                            $data_infomation['status'][] = $return_datas['status'];
                            $data_infomation['parts_order_number'][] = $return_datas['parts_order_number'];
                            $data_infomation['order_create_time'][] = $return_datas['order_create_time'];
                            $data_infomation['all'][] = Db::name('order_parts')
                                ->where('id', $value['order_parts_id'])
                                ->where($condition)
                                ->find();
                        }
                    };

                    if (!empty($order_undate)) {
                        foreach ($order_undate['info'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr[] =$j;
                            }
                        }
                        foreach ($new_arr as $i=>$j){
                            $end_info[$i]['info'] = $j;
                        }

                        foreach ($order_undate['store_name'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_name[] =$j;
                            }
                        }
                        foreach ($new_arr_name as $i=>$j){
                            $end_info[$i]['store_name'] = $j;
                        }

                        foreach ($order_undate['status'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_status[] = $j;
                            }
                        }
                        foreach ($new_arr_status as $i=>$j){
                            $end_info[$i]['status'] = $j;
                        }

                        foreach ($order_undate['all_order_real_pay'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_pay[] =$j;
                            }
                        }
                        foreach ($new_arr_pay as $i=>$j){
                            $end_info[$i]['all_order_real_pay'] = $j;
                        }

                        foreach ($order_undate['all_numbers'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_all_numbers[] =$j;
                            }
                        }
                        foreach ($new_arr_all_numbers as $i=>$j){
                            $end_info[$i]['all_numbers'] = $j;
                        }

                        foreach ($order_undate['store_id'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_all_store_id[] =$j;
                            }
                        }
                        foreach ($new_arr_all_store_id as $i=>$j){
                            $end_info[$i]['store_id'] = $j;
                        }

                        foreach ($order_undate['parts_order_number'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_all_order_number[] =$j;
                            }
                        }
                        foreach ($new_arr_all_order_number as $i=>$j){
                            $end_info[$i]['parts_order_number'] = $j;
                        }
                        foreach ($order_undate['order_create_time'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_order_create_time[] =$j;
                            }
                        }
                        foreach ($new_arr_order_create_time as $i=>$j){
                            $end_info[$i]['order_create_time'] = $j;
                        }


                    }

                    if(!empty($data_infomation)){
                        if(!empty($new_arr)){
                            $coutn =count($new_arr);
                        }else{
                            $coutn =0;
                        }
                        //店铺名字
                        foreach ($data_infomation['name'] as $a=>$b){
                            $end_info[$a+$coutn]['store_name'] = $b;
                        }
                        //支付状态
                        foreach ($data_infomation['status'] as $a=>$b){
                            $end_info[$a+$coutn]['status'] = $b;
                        }
                        //总支付
                        foreach ($data_infomation['all_order_real_pay'] as $a=>$b){
                            $end_info[$a+$coutn]['all_order_real_pay'] = $b;
                        }
                        //所有数量
                        foreach ($data_infomation['all_numbers'] as $a=>$b){
                            $end_info[$a+$coutn]['all_numbers'] = $b;
                        }
                        //订单编号
                        foreach ($data_infomation['parts_order_number'] as $a=>$b){
                            $end_info[$a+$coutn]['parts_order_number'] = $b;
                        }
                        //店铺id
                        foreach ($data_infomation['store_id'] as $a=>$b){
                            $end_info[$a+$coutn]['store_id'] = $b;
                        }
                        //所有信息
                        foreach ($data_infomation['all'] as $a=>$b){
                            $end_info[$a+$coutn]['info'][] = $b;
                        }
                        //时间
                        foreach ($data_infomation['order_create_time'] as $a=>$b){
                            $end_info[$a+$coutn]['order_create_time'] = $b;
                        }
                    }
                    if (!empty($end_info)) {
                        $ords =array();
                        foreach ($end_info as $vl){
                            $ords[] =intval($vl["order_create_time"]);
                        }
                        array_multisort($ords,SORT_DESC,$end_info);
                        return ajax_success('数据', $end_info);
                    } else {
                        return ajax_error('没数据');
                    }
                } else {
                    return ajax_error('请登录', ['status' => 0]);
                }
            }
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态待评价页面
     **************************************
     * @return \think\response\View
     */
    public function order_wait_evaluate(){
        return view('order_wait_evaluate');
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态待评价接口
     **************************************
     * @param Request $request
     */
    public function   ios_api_order_wait_evaluate(Request $request){
        if ($request->isPost()) {
            $datas = session('member');
            if (!empty($datas)) {
                $member_id = Db::name('user')
                    ->field('id')
                    ->where('phone_num', $datas['phone_num'])
                    ->find();
                $condition ="`status` = '6' or `status` = '7'";
                if (!empty($datas)) {
                    $data = Db::name('order_parts')
                        ->field('parts_order_number,group_concat(id) order_parts_id')
                        ->where('user_id', $member_id['id'])
                        ->where($condition)
                        ->order('order_create_time', 'desc')
                        ->group('parts_order_number')
                        ->select();
                    foreach ($data as $key => $value) {
                        if (strpos($value['order_parts_id'], ',')) {
                            $order_id = explode(',', $value['order_parts_id']);
                            foreach ($order_id as $k => $v) {
                                $return_data[] = Db::name('order_parts')
                                    ->where('id', $v)
                                    ->where('user_id', $member_id['id'])
                                    ->where($condition)
                                    ->find();
                            }
                            foreach ($return_data as $ke => $item) {
                                $order_store_id[] = $item['store_id'];
                            }
                            $da_store_id = array_unique($order_store_id); //去重之后的商户
                            foreach ($da_store_id as $da_k => $da_v) {
                                $order_undate['info'][] = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where($condition)
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->select();
                                $names = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where($condition)
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->find();
                                $order_undate['store_name'][] = $names['store_name'];
                                $order_undate['store_id'][] = $names['store_id'];
                                $order_undate['status'][] = $names['status'];
                                $order_undate["parts_order_number"][] =$names["parts_order_number"];
//                                $order_undate["all_order_real_pay"][] = $names["order_real_pay"];
                                $order_undate["order_create_time"][] = $names["order_create_time"];
                                foreach ($order_undate["info"] as  $kk=>$vv){
                                    $order_undate["all_numbers"][$kk] =array_sum(array_map(create_function('$vals','return $vals["order_quantity"];'),$vv));
                                    $order_undate["all_order_real_pay"][$kk] = array_sum(array_map(create_function('$vals','return $vals["order_amount"];'),$vv));
                                }
                            }
                        }
                        else{
                            $return_datas = Db::name('order_parts')
                                ->where('id', $value['order_parts_id'])
                                ->where($condition)
                                ->find();
                            $data_infomation["all_order_real_pay"][] =$return_datas["order_amount"];
                            $data_infomation["all_numbers"][] =$return_datas["order_quantity"];
                            $data_infomation['name'][]= $return_datas['store_name'];
                            $data_infomation['store_id'][]= $return_datas['store_id'];
                            $data_infomation['status'][] = $return_datas['status'];
                            $data_infomation['parts_order_number'][] = $return_datas['parts_order_number'];
                            $data_infomation['order_create_time'][] = $return_datas['order_create_time'];
                            $data_infomation['all'][] = Db::name('order_parts')
                                ->where('id', $value['order_parts_id'])
                                ->where($condition)
                                ->find();
                        }
                    };
                    if (!empty($order_undate)) {
                        foreach ($order_undate['info'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr[] =$j;
                            }
                        }
                        foreach ($new_arr as $i=>$j){
                            $end_info[$i]['info'] = $j;
                        }

                        foreach ($order_undate['store_name'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_name[] =$j;
                            }
                        }
                        foreach ($new_arr_name as $i=>$j){
                            $end_info[$i]['store_name'] = $j;
                        }

                        foreach ($order_undate['status'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_status[] = $j;
                            }
                        }
                        foreach ($new_arr_status as $i=>$j){
                            $end_info[$i]['status'] = $j;
                        }

                        foreach ($order_undate['all_order_real_pay'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_pay[] =$j;
                            }
                        }
                        foreach ($new_arr_pay as $i=>$j){
                            $end_info[$i]['all_order_real_pay'] = $j;
                        }

                        foreach ($order_undate['all_numbers'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_all_numbers[] =$j;
                            }
                        }
                        foreach ($new_arr_all_numbers as $i=>$j){
                            $end_info[$i]['all_numbers'] = $j;
                        }

                        foreach ($order_undate['store_id'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_all_store_id[] =$j;
                            }
                        }
                        foreach ($new_arr_all_store_id as $i=>$j){
                            $end_info[$i]['store_id'] = $j;
                        }

                        foreach ($order_undate['parts_order_number'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_all_order_number[] =$j;
                            }
                        }
                        foreach ($new_arr_all_order_number as $i=>$j){
                            $end_info[$i]['parts_order_number'] = $j;
                        }
                        foreach ($order_undate['order_create_time'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_order_create_time[] =$j;
                            }
                        }
                        foreach ($new_arr_order_create_time as $i=>$j){
                            $end_info[$i]['order_create_time'] = $j;
                        }


                    }

                    if(!empty($data_infomation)){
                        if(!empty($new_arr)){
                            $coutn =count($new_arr);
                        }else{
                            $coutn =0;
                        }
                        //店铺名字
                        foreach ($data_infomation['name'] as $a=>$b){
                            $end_info[$a+$coutn]['store_name'] = $b;
                        }
                        //支付状态
                        foreach ($data_infomation['status'] as $a=>$b){
                            $end_info[$a+$coutn]['status'] = $b;
                        }
                        //总支付
                        foreach ($data_infomation['all_order_real_pay'] as $a=>$b){
                            $end_info[$a+$coutn]['all_order_real_pay'] = $b;
                        }
                        //所有数量
                        foreach ($data_infomation['all_numbers'] as $a=>$b){
                            $end_info[$a+$coutn]['all_numbers'] = $b;
                        }
                        //订单编号
                        foreach ($data_infomation['parts_order_number'] as $a=>$b){
                            $end_info[$a+$coutn]['parts_order_number'] = $b;
                        }
                        //店铺id
                        foreach ($data_infomation['store_id'] as $a=>$b){
                            $end_info[$a+$coutn]['store_id'] = $b;
                        }
                        //所有信息
                        foreach ($data_infomation['all'] as $a=>$b){
                            $end_info[$a+$coutn]['info'][] = $b;
                        }
                        //时间
                        foreach ($data_infomation['order_create_time'] as $a=>$b){
                            $end_info[$a+$coutn]['order_create_time'] = $b;
                        }
                    }
                    if (!empty($end_info)) {
                        $ords =array();
                        foreach ($end_info as $vl){
                            $ords[] =intval($vl["order_create_time"]);
                        }
                        array_multisort($ords,SORT_DESC,$end_info);
                        return ajax_success('数据', $end_info);
                    } else {
                        return ajax_error('没数据');
                    }
                } else {
                    return ajax_error('请登录', ['status' => 0]);
                }
            }
        }
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态退货
     **************************************
     * @return \think\response\View
     */
    public function order_parts_return_goods(){
        return view('order_parts_return_goods');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态退货接口
     **************************************
     * @param Request $request
     */
    public function ios_api_order_parts_return_goods(Request $request){
        if ($request->isPost()) {
            $datas = session('member');
            if (!empty($datas)) {
                $member_id = Db::name('user')->field('id')->where('phone_num', $datas['phone_num'])->find();
                if (!empty($datas)) {
                    $condition ="`status` = '11' or `status` = '12' or `status` = '13'";
                    $data = Db::name('order_parts')
                        ->field('parts_order_number,group_concat(id) order_parts_id')
                        ->where('user_id', $member_id['id'])
                        ->where($condition)
                        ->order('order_create_time', 'desc')
                        ->group('parts_order_number')
                        ->select();
                    foreach ($data as $key => $value) {
                        if (strpos($value['order_parts_id'], ',')) {
                            $order_id = explode(',', $value['order_parts_id']);
                            foreach ($order_id as $k => $v) {
                                $return_data[] = Db::name('order_parts')
                                    ->where('id', $v)
                                    ->where($condition)
                                    ->where('user_id', $member_id['id'])
                                    ->find();
                            }
                            foreach ($return_data as $ke => $item) {
                                $order_store_id[] = $item['store_id'];
                            }
                            $da_store_id = array_unique($order_store_id); //去重之后的商户
                            foreach ($da_store_id as $da_k => $da_v) {
                                $order_undate['info'][] = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->where($condition)
//                                    ->order('order_create_time', 'desc')
                                    ->select();

                                $names = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->where($condition)
                                    ->find();
                                $order_undate['store_name'][] = $names['store_name'];
                                $order_undate['store_id'][] = $names['store_id'];
                                $order_undate['status'][] = $names['status'];
                                $order_undate["parts_order_number"][] =$names["parts_order_number"];
                                $order_undate["all_order_real_pay"][] = $names["order_real_pay"];
                                $order_undate["order_create_time"][] = $names["order_create_time"];
                                foreach ($order_undate["info"] as  $kk=>$vv){
                                    $order_undate["all_numbers"][$kk] =array_sum(array_map(create_function('$vals','return $vals["order_quantity"];'),$vv));
                                    $order_undate["all_order_real_pay"][$kk] = array_sum(array_map(create_function('$vals','return $vals["order_amount"];'),$vv));
                                }
                            }
                        }
                        else{
                            $return_datas = Db::name('order_parts')
                                ->where('id', $value['order_parts_id'])
                                ->where($condition)
                                ->find();
                            $data_infomation["all_order_real_pay"][] =$return_datas["order_amount"];
                            $data_infomation["all_numbers"][] =$return_datas["order_quantity"];
                            $data_infomation['name'][]= $return_datas['store_name'];
                            $data_infomation['store_id'][]= $return_datas['store_id'];
                            $data_infomation['status'][] = $return_datas['status'];
                            $data_infomation['parts_order_number'][] = $return_datas['parts_order_number'];
                            $data_infomation['order_create_time'][] = $return_datas['order_create_time'];
                            $data_infomation['all'][] = Db::name('order_parts')
                                ->where('id', $value['order_parts_id'])
                                ->where($condition)
                                ->find();
                        }
                    };

                    if (!empty($order_undate)) {
                        foreach ($order_undate['info'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr[] =$j;
                            }
                        }
                        foreach ($new_arr as $i=>$j){
                            $end_info[$i]['info'] = $j;
                        }

                        foreach ($order_undate['store_name'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_name[] =$j;
                            }
                        }
                        foreach ($new_arr_name as $i=>$j){
                            $end_info[$i]['store_name'] = $j;
                        }

                        foreach ($order_undate['status'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_status[] = $j;
                            }
                        }
                        foreach ($new_arr_status as $i=>$j){
                            $end_info[$i]['status'] = $j;
                        }

                        foreach ($order_undate['all_order_real_pay'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_pay[] =$j;
                            }
                        }
                        foreach ($new_arr_pay as $i=>$j){
                            $end_info[$i]['all_order_real_pay'] = $j;
                        }

                        foreach ($order_undate['all_numbers'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_all_numbers[] =$j;
                            }
                        }
                        foreach ($new_arr_all_numbers as $i=>$j){
                            $end_info[$i]['all_numbers'] = $j;
                        }

                        foreach ($order_undate['store_id'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_all_store_id[] =$j;
                            }
                        }
                        foreach ($new_arr_all_store_id as $i=>$j){
                            $end_info[$i]['store_id'] = $j;
                        }

                        foreach ($order_undate['parts_order_number'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_all_order_number[] =$j;
                            }
                        }
                        foreach ($new_arr_all_order_number as $i=>$j){
                            $end_info[$i]['parts_order_number'] = $j;
                        }
                        foreach ($order_undate['order_create_time'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_order_create_time[] =$j;
                            }
                        }
                        foreach ($new_arr_order_create_time as $i=>$j){
                            $end_info[$i]['order_create_time'] = $j;
                        }


                    }

                    if(!empty($data_infomation)){
                        if(!empty($new_arr)){
                            $coutn =count($new_arr);
                        }else{
                            $coutn =0;
                        }
                        //店铺名字
                        foreach ($data_infomation['name'] as $a=>$b){
                            $end_info[$a+$coutn]['store_name'] = $b;
                        }
                        //支付状态
                        foreach ($data_infomation['status'] as $a=>$b){
                            $end_info[$a+$coutn]['status'] = $b;
                        }
                        //总支付
                        foreach ($data_infomation['all_order_real_pay'] as $a=>$b){
                            $end_info[$a+$coutn]['all_order_real_pay'] = $b;
                        }
                        //所有数量
                        foreach ($data_infomation['all_numbers'] as $a=>$b){
                            $end_info[$a+$coutn]['all_numbers'] = $b;
                        }
                        //订单编号
                        foreach ($data_infomation['parts_order_number'] as $a=>$b){
                            $end_info[$a+$coutn]['parts_order_number'] = $b;
                        }
                        //店铺id
                        foreach ($data_infomation['store_id'] as $a=>$b){
                            $end_info[$a+$coutn]['store_id'] = $b;
                        }
                        //所有信息
                        foreach ($data_infomation['all'] as $a=>$b){
                            $end_info[$a+$coutn]['info'][] = $b;
                        }
                        //时间
                        foreach ($data_infomation['order_create_time'] as $a=>$b){
                            $end_info[$a+$coutn]['order_create_time'] = $b;
                        }
                    }
                    if (!empty($end_info)) {
                        $ords =array();
                        foreach ($end_info as $vl){
                            $ords[] =intval($vl["order_create_time"]);
                        }
                        array_multisort($ords,SORT_DESC,$end_info);
                        return ajax_success('数据', $end_info);
                    } else {
                        return ajax_error('没数据');
                    }
                } else {
                    return ajax_error('请登录', ['status' => 0]);
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态修改（未付款买家取消订单）
     **************************************
     * @param Request $request
     */
    public function ios_api_order_parts_no_pay_cancel(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");
            $store_id =$request->only('store_id')["store_id"];//店铺id
            $cancel_order_description =$request->only('cancel_order_description')["cancel_order_description"];//取消原因
            $parts_order_number =$request->only("parts_order_number")["parts_order_number"];//订单编号
            if(!empty($store_id)&&!empty($parts_order_number)){
                $res =Db::name("order_parts")
                    ->where("parts_order_number",$parts_order_number)
                    ->where("store_id",$store_id)
                    ->select();
                if(!empty($res)){
                    foreach($res as $k=>$v){
                        $is_use_integral[$k] =Db::name("order_parts")
                            ->field("integral_discount_setting_id,id,integral_deductible_num")
                            ->where("id",$v["id"])
                            ->having("integral_discount_setting_id","NEQ",NULL)
                            ->group("integral_discount_setting_id")
                            ->find();
                        $data =[
                            "status"=>9,
                            "cancel_order_description"=>$cancel_order_description
                        ];
                        $bool =Db::name("order_parts")->where("id",$v["id"])->update($data);
                    }
                    if($bool){
                        //取消订单退回积分到积余额
                        if(!empty($is_use_integral)){
//                            foreach ($is_use_integral as $keys=>$values){
                                if(!empty($is_use_integral[0]["integral_deductible_num"])){
                                $user_info = Db::name("user")->field("user_integral_wallet,user_integral_wallet_consumed")->where("id",$user_id)->find();
                                $update_data =[
                                    "user_integral_wallet"=>$user_info["user_integral_wallet"] + $is_use_integral[0]["integral_deductible_num"],
                                    "user_integral_wallet_consumed"=>$user_info["user_integral_wallet_consumed"] - $is_use_integral[0]["integral_deductible_num"]
                                ];
                                Db::name("user")->where("id",$user_id)->update($update_data); //积分增加
                                    $integral_data =[
                                    "user_id"=>$user_id,//用户ID
                                    "integral_operation"=>"+".$is_use_integral[0]["integral_deductible_num"],//积分操作
                                    "integral_balance"=>$user_info["user_integral_wallet"] + $is_use_integral[0]["integral_deductible_num"],//积分余额
                                    "integral_type"=> 1,//积分类型
                                    "operation_time"=>date("Y-m-d H:i:s") ,//操作时间
                                    "integral_remarks"=>"订单号:".$parts_order_number."取消退回".$is_use_integral[0]["integral_deductible_num"]."积分",//积分备注
                                ];
                                    Db::name("integral")->insert($integral_data); //插入积分消费记录
                                }
//                            }
                        }
                        return ajax_success("取消成功",["status"=>1]);
                    }else{
                        return ajax_error("取消失败",["status"=>0]);
                    }
                }
            }else{
                return ajax_error("所传参数不能为空",["status"=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:已付款取消订单
     **************************************
     * @param Request $request
     */
    public function ios_api_order_parts_yes_pay_cancel(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");
            $store_id =$request->only('store_id')["store_id"];//店铺id
            $cancel_order_description =$request->only('cancel_order_description')["cancel_order_description"];//取消原因
            $parts_order_number =$request->only("parts_order_number")["parts_order_number"];//订单编号
            if(!empty($store_id)&&!empty($parts_order_number)){
                $res =Db::name("order_parts")
                    ->where("parts_order_number",$parts_order_number)
                    ->where("store_id",$store_id)
                    ->select();
                if(!empty($res)){
                    foreach($res as $k=>$v){
                        $data =[
                            "status"=>9,
                            "cancel_order_description"=>$cancel_order_description
                        ];
                        $bool =Db::name("order_parts")->where("id",$v["id"])->update($data);
                    }
                    if($bool){
                        //取消订单退回积分到积余额
                        if(!empty( $is_use_integral)){

                                if(!empty($is_use_integral[0]["integral_deductible_num"])){
                                $user_info = Db::name("user")->field("user_integral_wallet,user_integral_wallet_consumed")->where("id",$user_id)->find();
                                $update_data =[
                                    "user_integral_wallet"=>$user_info["user_integral_wallet"] +$is_use_integral[0]["integral_deductible_num"],
                                    "user_integral_wallet_consumed"=>$user_info["user_integral_wallet_consumed"] - $is_use_integral[0]["integral_deductible_num"]
                                ];
                                Db::name("user")->where("id",$user_id)->update($update_data); //积分增加
                                    $integral_data =[
                                    "user_id"=>$user_id,//用户ID
                                    "integral_operation"=>"+".$is_use_integral[0]["integral_deductible_num"],//积分操作
                                    "integral_balance"=>$user_info["user_integral_wallet"] + $is_use_integral[0]["integral_deductible_num"],//积分余额
                                    "integral_type"=> 1,//积分类型
                                    "operation_time"=>date("Y-m-d H:i:s") ,//操作时间
                                    "integral_remarks"=>"订单号:".$parts_order_number."取消退回".$is_use_integral[0]["integral_deductible_num"]."积分",//积分备注
                                ];
                                    Db::name("integral")->insert($integral_data); //插入积分消费记录
                                }
                        }
                        return ajax_success("取消成功",["status"=>1]);
                    }else{
                        return ajax_error("取消失败",["status"=>0]);
                    }
                }
            }else{
                return ajax_error("所传参数不能为空",["status"=>0]);
            }
        }
    }




    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商买家删除订单接口(ajax)
     **************************************
     * @param Request $request
     */
    public function  ios_api_order_parts_del(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");//用户id
            $store_id =$request->only('store_id')["store_id"];//店铺id
            $parts_order_number =$request->only("parts_order_number")["parts_order_number"];//订单编号
            if(!empty($store_id)&&!empty($parts_order_number)){
                $res =Db::name("order_parts")
                    ->where("parts_order_number",$parts_order_number)
                    ->where("store_id",$store_id)
                    ->where("user_id",$user_id)
                    ->select();
                if(!empty($res)){
                    foreach($res as $k=>$v){
                        $bool =Db::name("order_parts")
                            ->where("id",$v["id"])
                            ->delete();
                    }
                    if($bool){

                        foreach ($res as $keys=>$values){
                            //如果评价过的则进行评价删除
                            $is_set_evaluate =Db::name("order_parts_evaluate")
                                ->where("order_id",$values["id"])
                                ->value("id");
                            if(!empty($is_set_evaluate)){
                                $is_set_img =Db::name("order_parts_evaluate_images")
                                    ->where("evaluate_order_id",$is_set_evaluate)
                                    ->select();
                                if(!empty($is_set_img)){
                                    foreach ($is_set_img as $ks=>$vs){
                                        unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$vs['images']);
                                        Db::name("order_parts_evaluate_images")
                                            ->where("evaluate_order_id",$vs["id"])
                                            ->delete();
                                    }
                                }
                                Db::name("order_parts_evaluate")
                                    ->where("order_id",$values["id"])
                                    ->delete();
                            }
                        }

                        return ajax_success("删除成功",["status"=>1]);
                    }else{
                        return ajax_error("删除失败",["status"=>0]);
                    }
                }
            }else{
                return ajax_error("所传参数不能为空",["status"=>0]);
            }
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态修改（买家确认收货）
     **************************************
     * @param Request $request
     */
    public function ios_api_order_parts_collect_goods(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");
            $store_id =$request->only('store_id')["store_id"];//店铺id
            $parts_order_number =$request->only("parts_order_number")["parts_order_number"];//订单编号
            if(!empty($store_id)&&!empty($parts_order_number)){
                $res =Db::name("order_parts")
                    ->where("parts_order_number",$parts_order_number)
                    ->where("store_id",$store_id)
                    ->select();
                if(!empty($res)){
                    foreach($res as $k=>$v){
                        $data =[
                            "status"=>7
                        ];
                        $bool =Db::name("order_parts")->where("id",$v["id"])->update($data);
                    }
                    if($bool){
                        //需要加入到商家余额里面
                        $order_info = Db::name("order_parts")
                            ->field("order_real_pay,parts_order_number,parts_goods_name,pay_type_content,order_quantity,goods_business_price")
                            ->where("store_id",$store_id)
                            ->where("parts_order_number",$parts_order_number)
                            ->select();
                        foreach ($order_info as $kk=>$vv){
                            $all_price[] =$vv["goods_business_price"] *$vv["order_quantity"];
                        }
                        $business_all_price =array_sum($all_price); //所有订单里面的总和钱
                        $business_id =Db::name("store")->where("store_id",$store_id)->value("user_id");
//                        TODO:对这个收入进行存储
                        $business_data =[
                            "user_id" =>$business_id,//商家用户id
                            "create_time"=>time(), //时间戳
                            "status"=>1, //状态值（1正常状态，-1申请提现但未处理，方便拒绝修改回状态，2提现已成功）
                            "order_num"=>$parts_order_number,//订单编号
                            "type" =>"配件商", //服务类型(配件商，服务商）
                            "money"=>$business_all_price, //进账的钱
                            "able_money"=>$business_all_price, //这笔可用钱
                            "is_pay"=>1, //(判断是否1收入，还是-1支出）
                            "is_deduction"=>1,//正常的流程
                        ];
                        $arr =Db::name("business_wallet")->insertGetId($business_data);
                        $arr_condition ="`status` = '1' and `is_deduction` = '1'  and  `user_id` = ".$business_id;
                        $business_wallet =Db::name("business_wallet")
                            ->where($arr_condition)
                            ->sum("money");
                       //原本的钱包余额
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
                                "wallet_operation"=>$business_all_price,
                                "wallet_type"=>1,
                                "operation_time"=>date("Y-m-d H:i:s"),
                                "wallet_remarks"=>"订单号：".$parts_order_number."，完成交易，收入".$business_all_price."元",
                                "wallet_img"=>"index/image/money2.png",
                                "title"=>$order_info[0]["parts_goods_name"],
                                "order_nums"=>$parts_order_number,
                                "pay_type"=>$order_info[0]["pay_type_content"], //支付方式
                                "wallet_balance"=>$new_wallet,
                                "is_business"=>2,//判断是车主消费还是商家消费（1车主消费，2商家消费）
                            ];
                            Db::name("wallet")->insert($data);
                        }
                        return ajax_success("确认收货成功",["status"=>1]);
                    }else{
                        return ajax_error("确认收货失败",["status"=>0]);
                    }
                }
            }else{
                return ajax_error("所传参数不能为空",["status"=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商提交订单接口（正常流程过来）
     **************************************
     */
    public function  ios_api_order_parts_button(Request $request){
        if ($request->isPost()) {
            $data = $_POST;
            $user_id =Session::get("user");
            if(empty($user_id)){
                return ajax_error("未登录",['status'=>0]);
            }
            //限制自己的商品自己不能购买
            $is_myself =Db::name("store")->where("store_id",$data["store_id"])->value("user_id");
            if($is_myself ==$user_id){
                return ajax_error('不能购买自己店铺的东西',['status'=>0]);
            }
            //计算一下库存量
            $rm_condition ="`status` != '9' and `status` != '10' and `status` != '0'";
            $rm_number =Db::name("order_parts")
                ->where("special_id",$data["goods_standard_id"])
                ->where($rm_condition)
                ->sum("order_quantity");//已买订单数量总和
            if(empty($rm_number)){
                $rm_number =0;
            }
            $special_stock =Db::name("special")->where("id",$data["goods_standard_id"])->value("stock");
            $rm_surplus =$special_stock - $rm_number;
            if($rm_surplus > 0){
                $user_information =Db::name("user")->where("id",$user_id)->find();
                $store_name =Db::name("store")->where("store_id",$data["store_id"])->find();
                if (empty($data["address_id"]) ) {
                    return ajax_error('请填写收货地址',['status'=>0]);
                }else{
                    $is_address_status = Db::name('user_address')
                        ->where("id",$data["address_id"])
                        ->find();
                    $commodity_id = $_POST['goods_id'];
                    if (!empty($commodity_id)) {
                        $goods_data = Db::name('goods')->where('id', $commodity_id)->find();
                        $create_time = time();//下单时间
                        $normal_time =Db::name("order_parts_setting")->find();//订单设置的时间
                        $normal_future_time =strtotime("+". $normal_time['normal_time']." minute");
                        if (!empty($data)) {
                            $harvest_address_city =str_replace(',','',$is_address_status['address_name']);
                            $harvest_address =$harvest_address_city.$is_address_status['harvester_real_address']; //收货人地址
                            $time=date("Y-m-d",time());
                            $v=explode('-',$time);
                            $time_second=date("H:i:s",time());
                            $vs=explode(':',$time_second);
                            $parts_order_number =$v[0].$v[1].$v[2].$vs[0].$vs[1].$vs[2].rand(1000,9999).$user_id; //订单编号
                            if(!empty($data["buy_message"])){
                                $buy_message =$data["buy_message"];
                            }else{
                                $buy_message = NUll ;
                            }
                            if(!empty($data["setting_id"])){
                                $setting_data =Db::name("integral_discount_settings")->where("setting_id",$data["setting_id"])->find();
                                $integral_deductible =$setting_data["deductible_money"];
                                $integral_discount_setting_id =$data["setting_id"];
                                $integral_deductible_num =$setting_data["integral_full"];
                            }else{
                                $integral_deductible = 0;
                                $integral_discount_setting_id =NULL;
                                $integral_deductible_num =NULL;
                            }
                            //图片
                            $special_data =Db::name("special")
                                ->where("id",$data["goods_standard_id"])
                                ->find();
                            $datas = [
                                "goods_business_price" =>$special_data["price"],//商家自己发布商品时的价格
                                'goods_image' =>  $special_data['images'],//图片
                                "special_id"=>$data["goods_standard_id"],//规格表id（用来查库存）
                                "goods_describe"=>$goods_data["goods_describe"],//卖点
                                'parts_goods_name' => $goods_data['goods_name'],//名字
                                "goods_money"=> $special_data['goods_adjusted_price'],//商品价钱
                                'order_quantity' => $data['order_quantity'],//订单数量
                                'user_id' => $user_id,//用户id
                                "user_account_name"=>$user_information["user_name"],//用户名
                                "user_phone_number"=>$user_information["phone_num"],//用户名手机号
                                'harvester' => $is_address_status['harvester'],
                                'harvest_phone_num' => $is_address_status['harvester_phone_num'],
                                'harvester_address' => $harvest_address,
                                'order_create_time' => $create_time,
                                'order_amount' => $data['order_amount'], //订单金额
                                "order_real_pay"=>$data["order_amount"],//订单实际支付的金额(即积分抵扣之后的价钱）
                                'status' => 1,
                                'goods_id' => $commodity_id,
                                'store_id' => $data['store_id'],
                                'store_name' => $store_name['store_name'],
                                'goods_standard'=>$data["goods_standard"], //商品规格
                                'parts_order_number' => $parts_order_number,//时间+4位随机数+用户id构成订单号
                                "buy_message"=>$buy_message,//买家留言
                                "normal_future_time"=>$normal_future_time,//未来时间
                                "integral_deductible"=>$integral_deductible, //积分抵扣
                                "integral_discount_setting_id"=>$integral_discount_setting_id, //积分抵扣的设置id
                                "integral_deductible_num"=>$integral_deductible_num,//抵扣的积分
                                "show_status"=>1,
                            ];
                            $res = Db::name('order_parts')->insertGetId($datas);
                            if ($res) {
                                $order_datas =Db::name("order_parts")
                                    ->field("goods_money,order_real_pay,parts_goods_name,parts_order_number,order_create_time")
                                    ->where('id',$res)
                                    ->where("user_id",$user_id)
                                    ->find();
                                if(!empty($data["setting_id"])){
                                    //积分消费记录
                                    $user_integral_wallet =$user_information["user_integral_wallet"]; //之前的积分余额
                                    $user_integral_wallets =$user_integral_wallet - $setting_data["integral_full"];//减了之后的积分
                                    $operation_times =date("Y-m-d H:i:s");
                                    $integral_data =[
                                        "user_id"=>$user_id,//用户ID
                                        "integral_operation"=>"-".$setting_data['integral_full'],//积分操作
                                        "integral_balance"=>$user_integral_wallets,//积分余额
                                        "integral_type"=> -1,//积分类型
                                        "operation_time"=>$operation_times ,//操作时间
                                        "integral_remarks"=>"订单号:".$order_datas['parts_order_number']."下单使用积分".$setting_data['integral_full']."抵扣".$setting_data["deductible_money"]."元钱",//积分备注
                                    ];
                                    Db::name("user")->where("id",$user_id)->update(["user_integral_wallet"=>$user_integral_wallets,"user_integral_wallet_consumed"=>$setting_data["integral_full"]+$user_information["user_wallet_consumed"]]);
                                    Db::name("integral")->insert($integral_data); //插入积分消费记录
                                }
                                if(!empty($data["invoice_type"])){
                                    if($data["invoice_type"] == "个人") {
                                        $invoice_data = array([
                                            "order_number" => $order_datas["parts_order_number"],
                                            "user_name" => $user_information["user_name"],
                                            "order_time" => $order_datas["order_create_time"],
                                            "invoice_type" => $data["invoice_type"],
                                            "invoice_money" => $order_datas["order_real_pay"],
                                            "user_id" => $user_id,
                                        ]);
                                    }else{
                                        $invoice_data = array([
                                            "order_number" => $order_datas["parts_order_number"],
                                            "user_name" => $user_information["user_name"],
                                            "order_time" => $order_datas["order_create_time"],
                                            "invoice_type" => $data["invoice_type"],
                                            "invoice_money" => $order_datas["order_real_pay"],
                                            "user_id" => $user_id,
                                            "invoice_rise"=>$data["invoice_rise"],
                                            "company_phone"=>$data["company_phone"],
                                            "company_number"=>$data["company_number"],

                                        ]);
                                    }
                                    db("invoice")->insert($invoice_data);
                                }
                                return ajax_success('下单成功',$order_datas);
                            }else{
                                return ajax_error('失败',['status'=>0]);
                            }
                        }
                    }
                }
            }else{
                return ajax_error('库存不足，无法购买该商品');
            }


        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:购物车提交订单
     **************************************
     * @param Request $request
     */
    public function  ios_api_order_button_by_shop(Request $request){
        if ($request->isPost()) {
            $data = $_POST;
            $user_id =Session::get("user");
            if(empty($user_id)){
                return ajax_error("未登录");
            }
            $user_information =Db::name("user")->where("id",$user_id)->find(); //用户信息
            if (empty($data["address_id"]) ) {
                return ajax_error('请填写收货地址');
            }else{
                //收货地址
                $is_address_status = Db::name('user_address')
                    ->where("id",$data["address_id"])
                    ->find();
                //商id
                $shopping_id =$request->only("shoppingId")["shoppingId"]; //购物车id
                foreach ($shopping_id as $keys=>$values){
                    //购物车表数据
                   $shopping_data[] = Db::name("shopping")
                       ->where("id",$values)
                       ->find();
                }
                $create_time = time();//下单时间
                $harvest_address_city =str_replace(',','',$is_address_status['address_name']);
                $harvest_address =$harvest_address_city.$is_address_status['harvester_real_address']; //收货人地址
                $time=date("Y-m-d",time());
                $v=explode('-',$time);
                $time_second=date("H:i:s",time());
                $vs=explode(':',$time_second);
                $parts_order_number =$v[0].$v[1].$v[2].$vs[0].$vs[1].$vs[2].rand(1000,9999).$user_id; //订单编号
                $normal_time =Db::name("order_parts_setting")->find();//订单设置的时间
                $normal_future_time =strtotime("+". $normal_time['normal_time']." minute");
                //提前判断金额最大的那个（如果是两家店，则计算金额最多的抵扣）
                foreach ($shopping_data as $i=>$j){
                    //计算一下库存量
                    $rm_condition ="`status` != '9' and `status` != '10' and `status` != '0'";
                    $rm_number =Db::name("order_parts")
                        ->where("special_id",$j["goods_standard_id"])
                        ->where($rm_condition)
                        ->sum("order_quantity");//已买订单数量总和
                    if(empty($rm_number)){
                        $rm_number =0;
                    }
                    $special_stock =Db::name("special")
                        ->where("id",$j["goods_standard_id"])
                        ->value("stock");
                    $rm_surplus =$special_stock - $rm_number;
                    if($rm_surplus <= 0){
                        return ajax_error('商品库存不足');
                    }
                    $total_money[$i]["money"] =$j["money"] * $j["goods_unit"];//总额
                   $total_money[$i]["store_id"] =$j["store_id"];
                   $total_money[$i]["id"] =$j["store_id"];
                   $total_money[$i]["goods_business_price"] =$j["goods_business_price"];//商家自己设置的商品价钱
                   $da_store_ids[] = $j["store_id"];
                }
                $da_store_id = array_unique($da_store_ids); //去重之后的商户
                foreach ($total_money as $k=>$v){
                     foreach ($da_store_id as $key=>$val){
                         if($v["store_id"]==$val){
                             $total[$key][] =$v;
                         }
                    }
                }
                //这是重组之后的店铺和总价钱
                foreach ($total as $k=>$v){
                    $information[$k]["all_price"] = array_sum(array_map(function($val){return $val['money'];}, $v)); //总价钱
                    $information[$k]["store_id"] =$v[0]["store_id"]; //店铺的id
                }
                //计算最大的价额进行减积分抵扣
                foreach ($information as $s=>$j){
                    $store_id_price[$j["store_id"]] = $j["all_price"];
                }
                $max =max($store_id_price);//最大的钱
                $all_order_store_id =array_search(max($store_id_price), $store_id_price);//最大钱的store_id
                foreach ($shopping_data as $key=>$val){
                    //积分抵扣
                    if(!empty($data["setting_id"])){
                        $setting_data =Db::name("integral_discount_settings")->where("setting_id",$data["setting_id"])->find();
                        $integral_deductible =$setting_data["deductible_money"]; //抵11元多少
                        $integral_discount_setting_id =$data["setting_id"];
                        $integral_deductible_num =$setting_data["integral_full"];
                        //假设最大的金额总大于（积分抵扣规则）
                            if($val["store_id"] == $all_order_store_id ){
                                $order_real_pay = $max - $setting_data["deductible_money"];//减去积分抵扣之后的价钱(使用积分的情况)
                            }else if($val["store_id"] != $all_order_store_id){
                                $integral_deductible = 0;
                                $integral_discount_setting_id =NULL;
                                $integral_deductible_num =NULL;
                                $order_real_pay =$store_id_price[$val["store_id"]]; //其他的原价
                            }
                    }else{
                        $integral_deductible = 0;
                        $integral_discount_setting_id =NULL;
                        $integral_deductible_num =NULL;
                        $order_real_pay =$store_id_price[$val["store_id"]];//价钱(未使用积分的情况)
                    }
                    $order_amount =$request->only("order_amount")["order_amount"]; //订单总价
                    $buy_messages =$request->only("buy_message")["buy_message"]; //买家留言
                    $commodity_id =$val["goods_id"];//商品id
                    if(!empty($commodity_id)){
                        $goods_data = Db::name('goods')->where('id', $commodity_id)->find();
                        if (!empty($data)) {
                            //买家留言
                        if(!empty($buy_messages)){
                            $buy_message =$buy_messages;
                        }else{
                            $buy_message = NUll ;
                        }
                            $datas = [
                                "goods_business_price" =>$val["goods_business_price"],//商家自己发布商品时的价格
                                'goods_image' => $val['goods_images'],//图片
                                "goods_describe"=>$goods_data["goods_describe"],//卖点
                                'parts_goods_name' => $goods_data['goods_name'],//名字
                                "goods_money"=>$val['money'],//商品价钱（变动）
                                'order_quantity' => $val['goods_unit'],//订单数量（变动）
                                'user_id' => $user_id,//用户id
                                "user_account_name"=>$user_information["user_name"],//用户名
                                "user_phone_number"=>$user_information["phone_num"],//用户名手机号
                                'harvester' => $is_address_status['harvester'],//收货人
                                'harvest_phone_num' => $is_address_status['harvester_phone_num'],//收货人手机
                                'harvester_address' => $harvest_address,//收货人地址
                                'order_create_time' => $create_time,//订单创建时间
                                'order_amount' => $val['money'] * $val['goods_unit'], //订单金额（变动）(减了积分抵扣的钱)
                                "order_real_pay"=>$order_real_pay,//订单实际支付的金额(即积分抵扣之后的价钱）（变动*）
                                'status' => 1,//状态
                                'goods_id' => $commodity_id,//商品id
                                'store_id' => $val['store_id'],//店铺id
                                'store_name' => $val['store_name'],//店铺名称
                                'goods_standard'=>$val["special_name"]." ".$val["goods_delivery"], //商品规格
                                "special_id"=>$val["goods_standard_id"],//规格表id（用来查库存）
                                'parts_order_number' => $parts_order_number,//时间+4位随机数+用户id构成订单号
                                "buy_message"=>$buy_message,//买家留言
                                "normal_future_time"=>$normal_future_time,//未来时间
                                "integral_deductible"=>$integral_deductible, //积分抵扣
                                "integral_discount_setting_id"=>$integral_discount_setting_id, //积分抵扣的设置id
                                "integral_deductible_num"=>$integral_deductible_num,//抵扣的积分
                                "show_status"=>1,
                            ];
                            $res = Db::name('order_parts')->insertGetId($datas);
                        }
                    }
                }
                if ($res) {
                    $order_datas =Db::name("order_parts")
                        ->field("order_real_pay,parts_goods_name,parts_order_number")
                        ->where('id',$res)
                        ->where("user_id",$user_id)
                        ->find();
                    if(!empty($data["setting_id"])){
                        //进行对使用了积分抵扣的进行修改
//                        //计算最大的价额进行减积分抵扣
                        $order_all_data =Db::name("order_parts")
                            ->where("parts_order_number",$order_datas["parts_order_number"])
                            ->order("order_amount","desc")
                            ->select();
                        foreach ($order_all_data as $a =>$b){
                            if($a==0){
                                Db::name("order_parts")->where("id",$b["id"])->update(["order_amount"=>$b["order_amount"]-$b["integral_deductible"]]);
                            }else if($a > 0){
                                Db::name("order_parts")->where("id",$b["id"])->update(["integral_deductible"=>0]);
                            }
                        }
                        //积分消费记录
                        $user_integral_wallet =$user_information["user_integral_wallet"]; //之前的积分余额
                        $user_integral_wallets =$user_integral_wallet - $setting_data["integral_full"];//减了之后的积分
                        $operation_times =date("Y-m-d H:i:s");
                        $integral_data =[
                            "user_id"=>$user_id,//用户ID
                            "integral_operation"=>"-".$setting_data['integral_full'],//积分操作
                            "integral_balance"=>$user_integral_wallets,//积分余额
                            "integral_type"=> -1,//积分类型
                            "operation_time"=>$operation_times ,//操作时间
                            "integral_remarks"=>"订单号:".$order_datas['parts_order_number']."下单使用积分".$setting_data['integral_full']."抵扣".$setting_data["deductible_money"]."元钱",//积分备注
                        ];
                        Db::name("user")->where("id",$user_id)->update(["user_integral_wallet"=>$user_integral_wallets,"user_integral_wallet_consumed"=>$setting_data["integral_full"]+$user_information["user_wallet_consumed"]]);
                        Db::name("integral")->insert($integral_data); //插入积分消费记录
                    }
                    //清空购物车数据
                    if(is_array($data["shoppingId"])){
                        $where ='id in('.implode(',',$data["shoppingId"]).')';
                    }else{
                        $where ='id='.$data["shoppingId"];
                    }
                    $list =  Db::name('shopping')->where($where)->delete();
                    return ajax_success('下单成功',$order_datas);
                }else{
                    return ajax_error('失败');
                }

            }
        }

    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单确认订单页面
     **************************************
     */
    public function ios_api_order_parts_firm_order(){
        return view("order_parts_firm_order");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:立即购买存储数据，方便确定订单提交
     **************************************
     * @param Request $request
     */
    public function get_goods_id_save(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");
            $goods_id =$request->only('goods_id')['goods_id'];//商品id
            $goods_number=$request->only('goods_number')['goods_number'];//数量
            $goods_standard=$request->only('goods_standard')['goods_standard'];//规格
            $goods_standard_id=$request->only('goods_standard_id')['goods_standard_id'];//通用专用规格id
            if(!empty($goods_id)){
                $data =[
                    "goods_id"=>$goods_id,
                    "goods_number"=>$goods_number,
                    "goods_standard"=>$goods_standard,
                    "goods_standard_id"=>$goods_standard_id,
                ];
                Session::set('part_goods_info',$data);
                Session::set("shopping_ids",null);//清空购物车去结算过来的数据
                return ajax_success('保存商品id成功',$data);
            }else{
                return ajax_error('保存商品失败');
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:提交订单页面返回购买页面传过来的数据(h5使用)
     **************************************
     */
    public function return_order_buy_information(Request $request){
        if($request->isPost()){
            //立即购买过来
           $part_goods_info = Session::get("part_goods_info");
           if(!empty($part_goods_info)){
               $goods_id = $part_goods_info["goods_id"];
               $goods = db("goods")->where("id",$goods_id)->select();
               foreach ($goods as $key=>$value){
                   $goods[$key]["goods_standard_name"] = explode(",",$value["goods_standard_name"]);
                   $store_name =Db::name("store")->field("store_name")->where("store_id",$value["store_id"])->find();
                   $goods[$key]["store_name"] =$store_name["store_name"];
                   $goods_standard_value = explode(",",$value["goods_standard_value"]);
                   $goods[$key]["goods_standard_value"] = array_chunk($goods_standard_value,"8");
                   $goods[$key]["goods_brand"] = db("brand")->where("id",$value["goods_brand_id"])->find();
                   $goods[$key]["images"] = db("goods_images")->where("goods_id",$value["id"])->select();
                   $goods[$key]["goods_standard_id"] =db("special")->where("id",$part_goods_info["goods_standard_id"])->find();
               }
               if(!empty($goods)){
                   $part_goods_info['goods'] =$goods;
                   exit(json_encode(array("status" => 1, "info" => "立即购买数据返回成功","data"=>$part_goods_info)));
               }else{
                   return ajax_error("没有数据");
               }
           }
           //购物车进来
           $shopping_id =Session::get("shopping_ids");
           $total_price =Session::get("total_price");
           if(!empty($shopping_id)){
               $user_id = Session::get("user");
               foreach ($shopping_id as $k=>$v){
                   $shopping_data[] =Db::name("shopping")
                       ->where("user_id",$user_id)
                       ->where("id",$v)
                       ->find();
               }
               //店铺id
               foreach ($shopping_data as $key=>$val){
                   $store_all_id[] =$val["store_id"];
               }
               $da_store_id = array_unique($store_all_id); //去重之后的商户
                   foreach ($shopping_data as $k=>$v){
                       foreach ($da_store_id as $keys=>$value){
                            if($v["store_id"]==$value){
                                $order_undate['info'][$keys][] = Db::name('shopping')
                                    ->where('user_id',$user_id)
                                    ->where("store_id",$value)
                                    ->where("id",$v["id"])
                                    ->find();
                                $names = Db::name('shopping')
                                    ->where('user_id',$user_id)
                                    ->where("store_id",$value)
                                    ->where("id",$v["id"])
                                    ->where("goods_standard_id",$v["goods_standard_id"])
                                    ->where("goods_delivery",$v["goods_delivery"])
                                    ->find();
                                $order_undate['store_name'][$keys] = $names['store_name'];
                                $order_undate['store_id'][$keys] = $names['store_id'];
                            }
                       }
               }
               if(!empty($order_undate)){
                   foreach ($order_undate["info"] as $i=>$j){
                       $shopping_info["info"][] = $j;
                   }
                   foreach ($order_undate["store_id"] as $i=>$j){
                       $shopping_info["store_id"][] = $j;
                   }
                   foreach ($order_undate["store_name"] as $i=>$j){
                       $shopping_info["store_name"][] = $j;
                   }
                   foreach ($shopping_info["info"] as $k=>$v){
                       $shopping_information[$k]["info"] =$v;
                       $shopping_information[$k]["total_price"] =$total_price;
                   }
                   foreach ($shopping_info["store_id"] as $k=>$v){
                       $shopping_information[$k]["store_id"] =$v;
                   }
                   foreach ($shopping_info["store_name"] as $k=>$v){
                       $shopping_information[$k]["store_name"] =$v;
                   }
               }
               if(!empty($shopping_info)){
                   exit(json_encode(array("status" => 3, "info" => "购物车数据","data"=> $shopping_information)));
               }else{
                   exit(json_encode(array("status" => 0, "info" => "没有数据")));
               }
           }
           if(empty($part_goods_info)&&empty($shopping_id)){
               exit(json_encode(array("status" => 0, "info" => "没有数据")));
           }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:提交订单页面返回购买页面传过来的数据(正常订单)
     **************************************
     */
    public function ios_order_info_direct(Request $request){
        if($request->isPost()){
            //立即购买过来
            $part_goods_info = Session::get("part_goods_info");
            if(!empty($part_goods_info)){
                $goods_id = $part_goods_info["goods_id"];
                $goods = db("goods")->where("id",$goods_id)->select();
                foreach ($goods as $key=>$value){
                    $goods[$key]["goods_standard_name"] = explode(",",$value["goods_standard_name"]);
                    $store_name =Db::name("store")->field("store_name")->where("store_id",$value["store_id"])->find();
                    $goods[$key]["store_name"] =$store_name["store_name"];
                    $goods_standard_value = explode(",",$value["goods_standard_value"]);
                    $goods[$key]["goods_standard_value"] = array_chunk($goods_standard_value,"8");
                    $goods[$key]["goods_brand"] = db("brand")->where("id",$value["goods_brand_id"])->find();
                    $goods[$key]["images"] = db("goods_images")->where("goods_id",$value["id"])->select();
                    $goods[$key]["goods_standard_id"] =db("special")->where("id",$part_goods_info["goods_standard_id"])->find();
                }
                if(!empty($goods)){
                    $part_goods_info['goods'] =$goods;
                    exit(json_encode(array("status" => 1, "info" => "立即购买数据返回成功","data"=>$part_goods_info)));
                }else{
                    return ajax_error("没有数据");
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:提交订单页面返回购买页面传过来的数据(购物车过来)
     **************************************
     */
    public function  ios_order_info_cart(Request $request){
        if($request->isPost()){
            //购物车进来
            $shopping_id =Session::get("shopping_ids");
            $total_price =Session::get("total_price");
            if(!empty($shopping_id)){
                $user_id = Session::get("user");
                foreach ($shopping_id as $k=>$v){
                    $shopping_data[] =Db::name("shopping")
                        ->where("user_id",$user_id)
                        ->where("id",$v)
                        ->find();
                }
                //店铺id
                foreach ($shopping_data as $key=>$val){
                    $store_all_id[] =$val["store_id"];
                }
                $da_store_id = array_unique($store_all_id); //去重之后的商户
                foreach ($shopping_data as $k=>$v){
                    foreach ($da_store_id as $keys=>$value){
                        if($v["store_id"]==$value){
                            $order_undate['info'][$keys][] = Db::name('shopping')
                                ->where('user_id',$user_id)
                                ->where("store_id",$value)
                                ->where("id",$v["id"])
                                ->find();
                            $names = Db::name('shopping')
                                ->where('user_id',$user_id)
                                ->where("store_id",$value)
                                ->where("id",$v["id"])
                                ->where("goods_standard_id",$v["goods_standard_id"])
                                ->where("goods_delivery",$v["goods_delivery"])
                                ->find();
                            $order_undate['store_name'][$keys] = $names['store_name'];
                            $order_undate['store_id'][$keys] = $names['store_id'];
                        }
                    }
                }
                if(!empty($order_undate)){
                    foreach ($order_undate["info"] as $i=>$j){
                        $shopping_info["info"][] = $j;
                    }
                    foreach ($order_undate["store_id"] as $i=>$j){
                        $shopping_info["store_id"][] = $j;
                    }
                    foreach ($order_undate["store_name"] as $i=>$j){
                        $shopping_info["store_name"][] = $j;
                    }
                    foreach ($shopping_info["info"] as $k=>$v){
                        $shopping_information[$k]["info"] =$v;
                        $shopping_information[$k]["total_price"] =$total_price;
                    }
                    foreach ($shopping_info["store_id"] as $k=>$v){
                        $shopping_information[$k]["store_id"] =$v;
                    }
                    foreach ($shopping_info["store_name"] as $k=>$v){
                        $shopping_information[$k]["store_name"] =$v;
                    }
                }
                if(!empty($shopping_info)){
                    exit(json_encode(array("status" => 3, "info" => "购物车数据","data"=> $shopping_information)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "没有数据")));
                }
            }
            if(empty($part_goods_info)&&empty($shopping_id)){
                exit(json_encode(array("status" => 0, "info" => "没有数据")));
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:生成订单(未用)
     **************************************
     * @param Request $request
     */
    public function ios_return_parts_num(Request $request){
        if($request->isPost()){
            $ios_return_num =$request->only(['num'])['num'];
            $data =Db::name('order_parts')->where('parts_order_number',$ios_return_num)->find();
            if($data){
                return ajax_success('成功',$data);
            }else{
                return ajax_error('失败');
            }
        }
    }







    /**
     * 发票显示页面
     * 陈绪
     */
    public function invoice_index(Request $request){
        if($request->isPost()){
            $invoice_money = $request->only(["invoice_money"])["invoice_money"];
            if(!empty($invoice_money)){
                $receipt = db("receipt")->select();
                $money_data = [];
                foreach ($receipt as $key=>$value){
                    $money_data["poundage"] = round($value["poundage"]/100*$invoice_money,2);
                    $money_data["taxation"] = round($value["taxation"]/100*$invoice_money,2);
                }
                return ajax_success("获取成功",$money_data);
            }

        }
        return view("invoice_index");

    }




    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单列表前端修改状态值
     **************************************
     */
    public function machine_update(Request $request){
        if($request->isPost()){
            $id =$request->only("id")["id"]; //订单号（短的）
            $status =$request->only("status")["status"];//状态值
            if(!empty($id)) {
                $bool = Db::name("order_parts")->where("parts_order_number", $id)->update(["status" => $status]);
                if ($bool) {
                    if ($status == 4) {
                        //对附近的快递员进行铃声提醒
                        $store_id = Db::name("order_parts")
                            ->where("id", $id)
                            ->value("store_id");
                        $store_city_address = Db::name("store")
                            ->where("store_id", $store_id)
                            ->value("store_city_address");
                        $delivery_data = Db::name("delivery")
                            ->field("account")
                            ->where("area", $store_city_address)
                            ->select(); //所有的快递员信息
                        $X = new Xgcontent;
                        foreach ($delivery_data as $k=>$v){
                            $account[] =$v["account"];
                        }
                        if(!empty($account)){
                            $X->push_account_list("来新订单", "来新订单",$account);
                        }
                    }
                    return ajax_success("修改成功", ["status" => 4]);
                } else {
                    return ajax_error("修改失败", ["status" => 0]);
                }

            }

        }
    }


}