<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/23
 * Time: 14:05
 */
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;

class Store extends Controller{


    /**
     * 店铺首页
     * 陈绪
     */
    public function index(){

        return view("store_index");

    }



    /**
     * 我要加盟
     * 陈绪
     */
    public function league(Request $request){

        if($request->isPost()){
            $user_id = Session::get("user");
            $user = db("user")->where("id",$user_id)->field("phone_num,sex,real_name")->select();
            $roles = db("role")->where("status","1")->field("id,name")->select();
            foreach ($roles as $key=>$value){
                if($value["id"] == 2){
                    unset($roles[$key]);
                }
            }
            if($roles){
                return ajax_success("获取成功",array("user"=>$user,"roles"=>$roles));
            }else{
                return ajax_error("获取失败");
            }
        }

        return view("store_league");
    }




    /**
     * 身份验证
     * 陈绪
     */
    public function verify(){

        return view("store_verify");

    }



}