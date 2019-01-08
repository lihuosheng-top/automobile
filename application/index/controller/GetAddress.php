<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/8 0008
 * Time: 9:36
 */
namespace  app\index\controller;


use think\Controller;
use think\Request;
use  think\Session;
use think\Db;

class GetAddress extends  Controller{

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:h5端获取安卓传过来的定位信息
     **************************************
     * @param Request $request
     */
    public function get_address_information(Request $request){
        $user_id = Session::get("user");//用户id

    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:从安卓端获取定位信息保存
     **************************************
     * @param Request $request
     */
    public function save_address_information(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");//用户id
            $data =$request->only("address_data")["address_data"];
            if(!empty($data)){
                $datas =[
                    "address_data"=>$data,
                    "user_id"=>$user_id
                ];
             $res = Db::name("h5_address")->insert($datas);
             if($res){
                ajax_success("保存成功",$datas);
             }else{
                 ajax_error("保存失败",$datas);
             }
            }
        }
    }



}