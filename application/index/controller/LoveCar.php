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
                    return ajax_success("添加成功");
                }else{
                    return ajax_error("添加失败");
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
    public function love_list(Request $request)
    {
        if($request->isPost()){
            $user_id = Session::get("user");
            $love = db("user_car")->where("user_id",$user_id)->select();
            return view("获取成功",$love);
        }
        return view("love_list");
    }



    /**
     * 我的爱车状态修改
     * 陈绪
     */
    public function love_status(Request $request){

        if($request->isPost()){
            $user_id = Session::get("user");
            $love = db("user_car")->where("user_id",$user_id)->where("status",1)->field("status")->find();
            if(!empty($love)){
                db("user_car")->where("user_id",$user_id)->where("status",1)->update(["status"=>0]);
            }
            $love_id = $request->only(["id"])["id"];
            $status = $request->only(["status"])["status"];
            $bool = db("user_car")->where("id",$love_id)->update(["status"=>$status]);
            if($bool){
                return ajax_success("设置成功");
            }else{
                return ajax_error("设置失败");
            }
        }

    }





    /**
     * 我的爱车列表删除
     * 陈绪
     */
    public function love_del(Request $request){

        if($request->isPost()){
            $id = $request->only(["id"])["id"];
            $bool = db("user_car")->where("id",$id)->delete();
            if($bool){
                return ajax_success("删除成功 ");
            }else{
                return ajax_error("删除失败");
            }
        }

    }


}