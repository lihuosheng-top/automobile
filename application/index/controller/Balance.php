<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/19 0019
 * Time: 17:16
 */

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use app\index\controller\Xgcontent;

class Balance extends Controller
{
    //配件商余额支付
    public function parts_balance_payment(Request $request)
    {
        if ($request->isPost()) {
            $order_num = $request->only(['order_num'])['order_num'];
            //验证支付密码
            $user_id = Session::get("user");
            $user_info = Db::name("user")->field("pay_passwd,user_wallet")->where("id", $user_id)->find();//用户信息
            $password = $request->only("passwords")["passwords"]; //输入的密码
            if (password_verify($password,$user_info["pay_passwd"])) {
                //真实支付的价钱
                $money = Db::name("order_parts")->where("parts_order_number", $order_num)->sum("order_amount");
                if(!empty($money)){
                    $money =round($money,2);
                }else{
                    $money =0;
                }
                //判断是商家角色买还是车主角色进行购买
                $business_store_id = Session::get("role_name_store_id"); //店铺id
                if (!empty($business_store_id["store_id"])) {
                    //商家
                    $business_id = Db::name("store")->where("store_id", $business_store_id["store_id"])->value("user_id");
                    $arr_condition = "`status` = '1' and `is_deduction` = '1'  and  `user_id` = " . $business_id;
                    $user_wallet = Db::name("business_wallet")
                        ->where($arr_condition)
                        ->sum("able_money");
                    if ($money > $user_wallet) {
                        exit(json_encode(array("status" => 3, "info" => "商家余额不足，请换其他方式支付")));
                    } else {
                        $select_data = Db::name("order_parts")
                            ->where("parts_order_number", $order_num)
                            ->select();
                        //对订单状态进行修改
                        $data['status'] = 2;
                        $data["pay_time"] = time();
                        $data['pay_type_content'] = "余额支付";
                        foreach ($select_data as $key => $val) {
                            $result = Db::name('order_parts')
                                ->where("parts_order_number", $val["parts_order_number"])
                                ->update($data);//修改订单状态,支付宝单号到数据库
                        }
                        //如果修改成功则进行钱抵扣
                        if ($result > 0) {
                            foreach ($select_data as $ks => $vs) {
                                $titles[] = $vs["parts_goods_name"];
                            }
                            $title = implode("", $titles);
                            //进行钱包消费记录
                            //   TODO:对这个收入进行存储
                            $business_data = [
                                "user_id" => $business_id,//商家用户id
                                "create_time" => time(), //时间戳
                                "status" => 1, //状态值（1正常状态，-1申请提现但未处理，方便拒绝修改回状态，2提现已成功）
                                "order_num" => $order_num,//订单编号
                                "type" => "配件商", //服务类型(配件商，服务商）
                                "money" => -$money, //进账的钱
                                "able_money" => -$money, //可使用的钱
                                "is_pay" => -1, //(判断是否1收入，还是-1支出）
                                "is_deduction" => 1,//正常的流程
                            ];
                            $arr = Db::name("business_wallet")->insertGetId($business_data);
                            if (!empty($arr)) {
                                $arr_condition = "`status` = '1' and `is_deduction` = '1'  and  `user_id` = " . $business_id;
                                $business_wallet = Db::name("business_wallet")
                                    ->where($arr_condition)
                                    ->sum("able_money");
                                $owner_wallet = Db::name("user")->where("id", $select_data[0]["user_id"])->value("user_wallet");
                                $new_wallet = $business_wallet+$owner_wallet;
                                $datas = [
                                    "user_id" => $select_data[0]["user_id"],//用户ID
                                    "wallet_operation" => -$money,//消费金额
                                    "wallet_type" => -1,//消费操作(1入，-1出)
                                    "operation_time" => date("Y-m-d H:i:s"),//操作时间
                                    "wallet_remarks" => "订单号：" . $order_num . "，余额消费，支出" . $money . "元",//消费备注
                                    "wallet_img" => "index/image/money2.png",//图标
                                    "title" => $title,//标题（消费内容）
                                    "order_nums" => $order_num,//订单编号
                                    "pay_type" => "余额支付", //支付方式
                                    "wallet_balance" => $new_wallet,//此刻钱包余额
                                    "is_business"=>2,//判断是车主消费还是商家消费（1车主消费，2商家消费）
                                ];
                                Db::name("wallet")->insert($datas);
                                foreach ($select_data as $keys => $vals) {
                                    //铃声
                                    $store_id =$vals["store_id"];
                                    $store_user_id =Db::name("store")->where("store_id",$store_id)->value("user_id");
                                    $user_count =Db::name("user")->where("id",$store_user_id)->value("phone_num");
                                    $X = new  Xgcontent;
                                    $X->push_Accountp("来新订单","来新订单了",$user_count);
                                }
                                return ajax_success('支付成功', ['status' => 1]);
                            }

                        } else {
                            return ajax_error('验证失败了', ['status' => 0]);
                        }
                    }

                    } else{
                        $user_wallet = $user_info["user_wallet"];
                        if ($money > $user_wallet) {
                            exit(json_encode(array("status" => 3, "info" => "车主余额不足，请换其他方式支付")));
                        } else {
                            $select_data = Db::name("order_parts")
                                ->where("parts_order_number", $order_num)
                                ->select();
                            //对订单状态进行修改
                            $data['status'] = 2;
                            $data["pay_time"] = time();
                            $data['pay_type_content'] = "余额支付";
                            foreach ($select_data as $key => $val) {
                                $result = Db::name('order_parts')
                                    ->where("parts_order_number", $val["parts_order_number"])
                                    ->update($data);//修改订单状态,支付宝单号到数据库
                            }
                            //如果修改成功则进行钱抵扣
                            if ($result > 0) {
                                foreach ($select_data as $ks => $vs) {
                                    $titles[] = $vs["parts_goods_name"];
                                }
                                $title = implode("", $titles);
                                //进行钱包消费记录
                                Db::name("user")->where("id", $select_data[0]["user_id"])->update(["user_wallet" => $user_info["user_wallet"] - $money]);
                                $owner_wallet = Db::name("user")->where("id", $select_data[0]["user_id"])->value("user_wallet");
                                $arr_condition = "`status` = '1' and `is_deduction` = '1'  and  `user_id` = " . $select_data[0]["user_id"];
                                $business_wallet = Db::name("business_wallet")
                                    ->where($arr_condition)
                                    ->sum("able_money");
                                $new_wallet = $business_wallet+$owner_wallet;
                                $datas = [
                                    "user_id" => $select_data[0]["user_id"],//用户ID
                                    "wallet_operation" => -$money,//消费金额
                                    "wallet_type" => -1,//消费操作(1入，-1出)
                                    "operation_time" => date("Y-m-d H:i:s"),//操作时间
                                    "wallet_remarks" => "订单号：" . $order_num . "，余额消费，支出" . $money . "元",//消费备注
                                    "wallet_img" => "index/image/money2.png",//图标
                                    "title" => $title,//标题（消费内容）
                                    "order_nums" => $order_num,//订单编号
                                    "pay_type" => "余额支付", //支付方式
                                    "wallet_balance" => $new_wallet,//此刻钱包余额
                                    "is_business"=>1,//判断是车主消费还是商家消费（1车主消费，2商家消费）
                                ];
                                Db::name("wallet")->insert($datas);
                                foreach ($select_data as $keys => $vals) {
                                    //铃声
                                    $store_id =$vals["store_id"];
                                    $store_user_id =Db::name("store")->where("store_id",$store_id)->value("user_id");
                                    $user_count =Db::name("user")->where("id",$store_user_id)->value("phone_num");
                                    $X = new  Xgcontent;
                                    $X->push_Accountp("来新订单","来新订单了",$user_count);
                                }
                                return ajax_success('支付成功', ['status' => 1]);
                            } else {
                                return ajax_error('验证失败了', ['status' => 0]);
                            }
                        }
                    }
                } else {
                    return ajax_error('密码错误', ['status' => 0]);
                }
            }
        }
    //服务商余额支付
    public function service_balance_payment(Request $request)
        {
            if ($request->isPost()) {
                $order_num = $request->only(['order_num'])['order_num'];
                //验证支付密码
                $user_id = Session::get("user");
                $user_info = Db::name("user")->field("pay_passwd,user_wallet")->where("id", $user_id)->find();//用户信息
                $password = $request->only("passwords")["passwords"]; //输入的密码
                if (password_verify($password, $user_info["pay_passwd"])) {
                    $money = Db::name("order_service")->where("service_order_number", $order_num)->value("service_real_pay");
                    $is_face =Db::name("order_service")->where("service_order_number",$order_num)->value("is_face");
                    $money =round($money,2);
                    $business_store_id = Session::get("role_name_store_id"); //店铺id
                        if($is_face ==1){
                            //如果不是面议，则直接改为已付款
                            $data['status'] = 2;//状态值
                            $data['pay_time'] = time();//交易号
                            $data['pay_type_content'] = "余额支付";
                        }else{
                            //如果不是面议，则直接改为已付款
                            $data['status'] = 5;//状态值
                            $data['pay_time'] = time();//交易号
                            $data['pay_type_content'] = "余额支付";
                        }
                    if (!empty($business_store_id["store_id"])) {
                        //商家
                        $business_id = Db::name("store")->where("store_id", $business_store_id["store_id"])->value("user_id");
                        $arr_condition = "`status` = '1' and `is_deduction` = '1'  and  `user_id` = " . $business_id;
                        $user_wallet = Db::name("business_wallet")
                            ->where($arr_condition)
                            ->sum("able_money");
                        if($money > $user_wallet){
                            exit(json_encode(array("status" => 3, "info" => "商家余额不足，请换其他方式支付")));
                        }else{
                            $condition['service_order_number'] = $order_num;
                            $result = Db::name('order_service')->where($condition)->update($data);//修改订单状态，到数据库
                            if ($result > 0) {
                                //进行钱包消费记录
                                $parts = Db::name("order_service")
                                    ->field("service_goods_name,service_real_pay,user_id,store_id")
                                    ->where($condition)
                                    ->find();
                                $title = $parts["service_goods_name"];
                                $money = $parts["service_real_pay"];//金额
                                //   TODO:对这个收入进行存储
                                $business_data = [
                                    "user_id" => $business_id,//商家用户id
                                    "create_time" => time(), //时间戳
                                    "status" => 1, //状态值（1正常状态，-1申请提现但未处理，方便拒绝修改回状态，2提现已成功）
                                    "order_num" => $order_num,//订单编号
                                    "type" => "服务商", //服务类型(配件商，服务商）
                                    "money" => -$money, //进账的钱
                                    "able_money" => -$money, //可使用的钱
                                    "is_pay" => -1, //(判断是否1收入，还是-1支出）
                                    "is_deduction" => 1,//正常的流程
                                ];
                                $arr = Db::name("business_wallet")->insertGetId($business_data);
                                if(!empty($arr)){
                                    $arr_condition = "`status` = '1' and `is_deduction` = '1'  and  `user_id` = " . $parts["user_id"];
                                    $business_wallet = Db::name("business_wallet")
                                        ->where($arr_condition)
                                        ->sum("able_money");
                                    $owner_wallet = Db::name("user")->where("id", $parts["user_id"])->value("user_wallet");
                                    $new_wallet = $business_wallet+$owner_wallet;
                                    $datas = [
                                        "user_id" => $parts["user_id"],//用户ID
                                        "wallet_operation" => -$money,//消费金额
                                        "wallet_type" => -1,//消费操作(1入，-1出)
                                        "operation_time" => date("Y-m-d H:i:s"),//操作时间
                                        "wallet_remarks" => "订单号：" . $order_num . "，余额消费，支出" . $money . "元",//消费备注
                                        "wallet_img" => "index/image/money2.png",//图标
                                        "title" => $title,//标题（消费内容）
                                        "order_nums" => $order_num,//订单编号
                                        "pay_type" => "余额支付", //支付方式
                                        "wallet_balance" => $new_wallet,//此刻钱包余额
                                        "is_business" =>2, //判断是车主消费还是商家消费1车主消费，2商家消费

                                    ];
                                    Db::name("wallet")->insert($datas);
                                    //铃声
                                    $store_id =$parts["store_id"];
                                    $store_user_id =Db::name("store")->where("store_id",$store_id)->value("user_id");
                                    $user_count =Db::name("user")->where("id",$store_user_id)->value("phone_num");
                                    $X = new  Xgcontent;
                                    $X->push_Accountp("来新订单","来新订单了",$user_count);
                                    return ajax_success('支付成功', ['status' => 1]);
                                }

                            }
                        }

                    }else{
                        //车主
                        if ($money > $user_info["user_wallet"]) {
                            exit(json_encode(array("status" => 3, "info" => "车主余额不足，请换其他方式支付")));
                        } else {
                            $condition['service_order_number'] = $order_num;
                            $result = Db::name('order_service')->where($condition)->update($data);//修改订单状态，到数据库
                            if ($result > 0) {
                                //进行钱包消费记录
                                $parts = Db::name("order_service")
                                    ->field("service_goods_name,service_real_pay,user_id,store_id")
                                    ->where($condition)
                                    ->find();
                                $title = $parts["service_goods_name"];
                                $money = $parts["service_real_pay"];//金额
                                Db::name("user")->where("id", $parts["user_id"])->update(["user_wallet" => $user_info["user_wallet"] - $money]);

                                $arr_condition = "`status` = '1' and `is_deduction` = '1'  and  `user_id` = " . $parts["user_id"];
                                $business_wallet = Db::name("business_wallet")
                                    ->where($arr_condition)
                                    ->sum("able_money");
                                $owner_wallet = Db::name("user")->where("id", $parts["user_id"])->value("user_wallet");
                                $new_wallet = $business_wallet+$owner_wallet;
                                $datas = [
                                    "user_id" => $parts["user_id"],//用户ID
                                    "wallet_operation" => -$money,//消费金额
                                    "wallet_type" => -1,//消费操作(1入，-1出)
                                    "operation_time" => date("Y-m-d H:i:s"),//操作时间
                                    "wallet_remarks" => "订单号：" . $order_num . "，余额消费，支出" . $money . "元",//消费备注
                                    "wallet_img" => "index/image/alipay.png",//图标
                                    "title" => $title,//标题（消费内容）
                                    "order_nums" => $order_num,//订单编号
                                    "pay_type" => "余额支付", //支付方式
                                    "wallet_balance" => $new_wallet,//此刻钱包余额
                                    "is_business"=>1,//判断是车主消费还是商家消费（1车主消费，2商家消费）
                                ];
                                Db::name("wallet")->insert($datas);
                                //铃声
                                $store_id =$parts["store_id"];
                                $store_user_id =Db::name("store")->where("store_id",$store_id)->value("user_id");
                                $user_count =Db::name("user")->where("id",$store_user_id)->value("phone_num");
                                $X = new  Xgcontent;
                                $X->push_Accountp("来新订单","来新订单了",$user_count);
                                return ajax_success('支付成功', ['status' => 1]);
                            }
                    }

                    }
                } else {
                    return ajax_error('密码错误', ['status' => 0]);
                }
            }
        }
}