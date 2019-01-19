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

class Balance extends  Controller{
    //配件商余额支付
    public function  parts_balance_payment(Request $request){
        if($request->isPost()){
            $order_num =$request->only(['order_num'])['order_num'];
            //验证支付密码
            $user_id =Session::get("user");
            $user_info= Db::name("user")->field("password,user_wallet")->where("id",$user_id)->find();//用户信息
            $password =$request->only("password")["password"]; //输入的密码
            if(password_verify($password,$user_info["passwords"])){
                //真实支付的价钱
                $money =Db::name("order_parts")->where("parts_order_number",$order_num)->sum("order_amount");
                if( $money > $user_info["user_wallet"]){
                    return ajax_error("余额不足，请换其他方式支付",["status"=>0]);
                }else{
                    $select_data =Db::name("order_parts")
                        ->where("parts_order_num",$order_num)
                        ->select();
                    //对订单状态进行修改
                    $data['status'] = 2;
                    $data["trade_no"] =time();
                    $data['pay_type_content'] = "余额支付";
                    foreach ($select_data as $key => $val) {
                        $result = Db::name('order_parts')
                            ->where("parts_order_number", $val["parts_order_number"])
                            ->update($data);//修改订单状态,支付宝单号到数据库
                    }
                    //如果修改成功则进行钱抵扣
                    if ($result > 0) {
                        foreach($select_data as $ks=>$vs){
                            $titles[] = $vs["parts_goods_name"];
                        }
                        $title =implode("",$titles);
                        //进行钱包消费记录
                       Db::name("user")->where("id",$select_data[0]["user_id"])->update(["user_wallet"=>$user_info["user_wallet"]-$money]);
                        $new_wallet =Db::name("user")->where("id",$select_data[0]["user_id"])->value("user_wallet");
                        $datas=[
                            "user_id"=>$select_data[0]["user_id"],//用户ID
                            "wallet_operation"=>-$money,//消费金额
                            "wallet_type"=>-1,//消费操作(1入，-1出)
                            "operation_time"=>date("Y-m-d H:i:s"),//操作时间
                            "wallet_remarks"=>"订单号：".$order_num."，余额消费，支出".$money."元",//消费备注
                            "wallet_img"=>"index/image/money2",//图标
                            "title"=>$title,//标题（消费内容）
                            "order_nums"=>$order_num,//订单编号
                            "pay_type"=>"余额支付", //支付方式
                            "wallet_balance"=>$new_wallet,//此刻钱包余额
                        ];
                        Db::name("wallet")->insert($datas);
                        return ajax_success('支付成功', ['status' => 1]);
                    } else {
                        return ajax_error('验证失败了', ['status' => 0]);
                    }
                }
            }else{
                return ajax_error('密码错误',['status'=>0]);
            }
        }
    }

    //服务商余额支付
    public function  service_balance_payment(Request $request){
        if($request->isPost()){
            $order_num =$request->only(['order_num'])['order_num'];
            //验证支付密码
            $user_id =Session::get("user");
            $user_info= Db::name("user")->field("password,user_wallet")->where("id",$user_id)->find();//用户信息
            $password =$request->only("password")["password"]; //输入的密码
            if(password_verify($password,$user_info["passwords"])){
                $money =Db::name("order_service")->where("service_order_number",$order_num)->value("service_real_pay");
                if( $money > $user_info["user_wallet"]){
                    return ajax_error("余额不足，请换其他方式支付",["status"=>0]);
                }else {
                    $data['status'] = 2;//状态值
                    $data['trade_no'] = time();//交易号
                    $data['pay_type_content'] = "余额支付";
                    $condition['service_order_number'] = $order_num;
                    $result = Db::name('order_service')->where($condition)->update($data);//修改订单状态，到数据库
                    if ($result > 0) {
                        //进行钱包消费记录
                        $parts = Db::name("order_service")
                            ->field("service_goods_name,service_real_pay,user_id")
                            ->where($condition)
                            ->find();
                        $title = $parts["service_goods_name"];
                        $money = $parts["service_real_pay"];//金额
                        Db::name("user")->where("id", $parts["user_id"])->update(["user_wallet" => $user_info["user_wallet"] - $money]);
                        $new_wallet = Db::name("user")->where("id", $parts["user_id"])->value("user_wallet");
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
                        ];
                        Db::name("wallet")->insert($datas);
                        return ajax_success('支付成功', ['status' => 1]);
                    }
                }
            }else{
                return ajax_error('密码错误',['status'=>0]);
            }
        }
    }




}