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
                    exit(json_encode(array("status" => 1, "info" => "我的钱包余额返回成功")));
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
     * 钱包充值
     * 陈绪
     */
    public function recharge(){
        
        return view("wallet_recharge");
        
    }



    /**
     * 添加银行卡
     * 陈绪
     */
    public function block(){

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