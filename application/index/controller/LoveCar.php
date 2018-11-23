<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/23
 * Time: 18:13
 */
namespace app\index\controller;
use think\Controller;
use think\Request;

class LoveCar extends Controller{

    /**
     * 我的爱车
     * 陈绪
     */
    public function love_car(Request $request){

        if($request->isGet()){
            $brand = db("car_series")->distinct(true)->field("brand")->select();
            $series = db("car_series")->select();
            if($brand){
                return ajax_success("获取成功",array("brand"=>$brand,"series"=>$series));
            }else{
                return ajax_error("获取失败");
            }

        }

    }




    /**
     * 我的爱车
     * 陈绪
     */
    public function love_list()
    {
        return view("love_list");
    }


}