<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/3
 * Time: 10:50
 */
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
class Wallet extends Controller{


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:我的钱包页面和可用金额（接口）
     **************************************
     * @return \think\response\View
     */
    public function index(Request $request){
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
        return view("wallet_index");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:充值金额
     **************************************
     * @return \think\response\View
     */
    public function recharge(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");//用户的id
            if(!empty($user_id)){
               $recharge_money =$request->only("recharge_money")["recharge_money"];
               if(!empty( $recharge_money)){
                   $time=date("Y-m-d",time());
                   $v=explode('-',$time);
                   $time_second=date("H:i:s",time());
                   $vs=explode(':',$time_second);
                   $recharge_order_number =$v[0].$v[1].$v[2].$vs[0].$vs[1].$vs[2].rand(1000,9999).$user_id; //订单编号
                   $data =[
                       "user_id"=>$user_id,
                       "recharge_order_number"=>$recharge_order_number,
                       "recharge_money"=>$recharge_money,
                       "status"=>-1
                   ];
                   $recharge_id =Db::name("recharge_record")->insertGetId($data);
                   if(!empty($recharge_id)){
                       exit(json_encode(array("status" => 1, "info" => "下单成功,返回订单编号","data"=>$recharge_id)));
                   }else{
                       exit(json_encode(array("status" => 0, "info" => "下单不成功")));
                   }
               }else{
                   exit(json_encode(array("status" => 0, "info" => "充值金额不能为空")));
               }
            }else{
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
        }
        return view("wallet_recharge");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:充值下单未付款自动关闭取消删除
     **************************************
     * @param Request $request
     */
    public function recharge_del(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");//用户的id
            if(!empty($user_id)){
                $recharge_id =$request->only("recharge_id")["recharge_id"];//充值订单编号
                if(!empty($recharge_id)){
                    $bool =Db::name("recharge_record")->where("id",$recharge_id)->delete();
                    if($bool){
                        exit(json_encode(array("status" => 1, "info" => "取消成功")));
                    }else{
                        exit(json_encode(array("status" => 0, "info" => "取消失败")));
                    }
                }
            }else{
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:添加银行卡
     **************************************
     * @return \think\response\View
     */
    public function block(Request $request){
        if($request->isPost()){

        }
        return view("wallet_block");

    }



    /**
     * 验证银行卡信息
     * 陈绪
     */
    public function verification(){

        return view("wallet_verification");

    }
}