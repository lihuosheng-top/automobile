<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/20
 * Time: 15:34
 */
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;

class Operation extends Controller{


    /**
     * 紧急救援
     * 陈绪
     */
    public function rescue(Request $request){

        if($request->isPost()){
            $address = $request->only(["address"])["address"];
            $user_id = Session::get("user");
            $user = db("user")->where("id",$user_id)->find();
            $data["account"] = $user["phone_num"];
            $data["user_name"] = $user["user_name"];
            $data["user_address"] = $address;
            $data["user_phone"] = $user["phone_num"];
            $data["status"] = 1;
            $data["user_id"] = $user_id;
            $bool = db("rescue")->insert($data);
            if($bool){
                return ajax_success("存储成功");
            }else {
                return ajax_error("存储失败");
            }
        }
        return view("rescue_index");

    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:IOS测试地址
     **************************************
     * @return \think\response\View
     */

    public function  ios(){
        return view("ios");
    }


}