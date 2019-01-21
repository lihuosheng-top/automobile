<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * 我要推广
 * Date: 2018/12/11 0011
 * Time: 10:49
 */

namespace  app\index\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Session;
class Extension extends  Controller{
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:我要推广页面
     **************************************
     * @return \think\response\View
     */
    public function spread_index(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");//用户的id
            if(empty($user_id)){
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
            $user_invitation =Db::name("user")->where("id",$user_id)->find();
            if(!empty($user_invitation)){
                $domain_name =config("domain_url.address");//域名
                $reg = 'register';  //注册地址
                $share_url = $domain_name."/".$reg."?a=".$user_invitation["invitation"];
                /*二维码*/
                $share_code ='http://b.bshare.cn/barCode?site=weixin&url='.$share_url;
                    $data =[
                        "invitation"=>$user_invitation["invitation"],//邀请码
                        "invitation_images_url"=> $share_code, //邀请码二维码
                        "register_url"=>$share_url,   //注册链接
                    ];
                   if(!empty($data)){
                       exit(json_encode(array("status" => 1, "info" => "数据返回成功","data"=>$data)));
                   }else{
                       exit(json_encode(array("status" => 0, "info" => "没有该用户信息")));
                   }
            }
        }
        return view("spread_index");
    }
}