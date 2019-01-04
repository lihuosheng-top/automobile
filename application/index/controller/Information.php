<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4 0004
 * Time: 11:47
 */
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;

class  Information extends  Controller{

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:消息页面
     **************************************
     * @return \think\response\View
     */
    public function index(){
        $user_id = Session::get("user");
        if(empty($user_id)){
            $this->success("请登录查看",url("index/My/login"));
        }else{
            return view('index');
        }

    }

    /**
     **************陈绪*******************
     * @param Request $request
     * Notes:订单消息页面详情
     **************************************
     * @return \think\response\View
     */
    public function information_details(Request $request){
        if($request->isPost()) {
            $user_id = Session::get("user");
            $order_data = db("order_parts")->where("user_id", $user_id)->where("show_status","<>",0)->select();
            $order = [];
            foreach ($order_data as $value) {
                if ($value["status"] == 1 || $value["status"] == 2 || $value["status"] == 4 || $value["status"] == 10 || $value["status"] == 7 || $value["status"] == 11) {
                    $order[] = $value;
                }
            }
            if($order){
                return ajax_success("获取成功",$order);
            }else{
                return ajax_error("获取失败");
            }
        }
        return view('information_details');
    }



    /**
     **************陈绪*******************
     * @param Request $request
     * Notes:系统消息页面详情
     **************************************
     * @return \think\response\View
     */
    public function information_system(Request $request){

        if($request->isPost()) {
            $system_data = db("message")->where("status","<>",0)->select();
            return ajax_success("获取成功",$system_data);
        }
        return view('information_system');
    }



    /**
     * 系统消息详情
     * 陈绪
     */
    public function information_system_details($id){

        $system_data = db("message")->where("id",$id)->select();
        return view("information_system_details",["system_data"=>$system_data]);

    }



    /**
     * 订单助手详情页面
     * 陈绪
     */
    public function order_information_details($order_id){
        $order = db("order_parts")->where("id",$order_id)->select();
        foreach ($order as $key=>$value){
            $order[$key]["store"] = db("store")->where("store_id",$value["store_id"])->find();
        }
        return view("order_information_details",["order"=>$order]);

    }



}