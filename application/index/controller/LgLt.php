<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14 0014
 * 经度纬度获取及保存
 * Time: 16:29
 */


namespace  app\index\controller;


use think\Controller;
use think\Request;
use think\Db;
use  think\Session;

class  LgLt extends  Controller{
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:保存经度纬度
     **************************************
     */
    public function save(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");
            $longitude =$request->only('longitude')['longitude'];//经度
            $latitude =$request->only('latitude')['latitude'];//纬度
            if((!empty($longitude)) && (!empty($latitude))){
             $res =  Db::name("user")->where("id",$user_id)->update(["longitude"=>$longitude,"latitude"=>$latitude]);
             if($res){
                 return ajax_success("经纬度刷新成功",["status"=>1]);
             }else{
                 return ajax_success("经纬度不变",["status"=>1]);
             }
            }else{
                return ajax_error("经纬度不能为空",["status"=>0]);
            }

        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:读取经纬度
     **************************************
     * @param Request $request
     */
    public function read(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");
            if(!empty($user_id)){
                $res = Db::name("user")->field("longitude,latitude")->where("id",$user_id)->select();
                if($res){
                    return ajax_success("经纬度获取成功",$res);
                }else{
                    return ajax_error("请刷新页面再次获取",["status"=>0]);
                }
            }else{
                return ajax_error("用户未登录",["status"=>0]);
            }
        }
    }
}