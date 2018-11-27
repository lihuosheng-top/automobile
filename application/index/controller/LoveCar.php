<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/23
 * Time: 18:13
 */
namespace app\index\controller;
use think\Controller;
use think\Paginator;
use think\Request;
use think\Session;

class LoveCar extends Controller{

    /**
     * 我的爱车
     * 陈绪
     */
    public function love_car(Request $request){

        if($request->isPost()){
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
     * 我的爱车入库
     * 陈绪
     */
    public function love_save(Request $request){

        $user_id = Session::get("user");
        if(!empty($user_id)){
            if($request->isPost()){
                $love = $request->param();
                $love["user_id"] = $user_id;
                $love["status"] = 0;
                $bool = db("user_car")->insert($love);
                if($bool){
                    return ajax_success("入库成功");
                }else{
                    return ajax_error("入库失败");
                }
            }
        }else{
            $this->success("请登录",url("index/My/login"));
        }


    }




    /**
     * 我的爱车列表
     * 陈绪
     */
    public function love_list()
    {
        return view("love_list");
    }


}