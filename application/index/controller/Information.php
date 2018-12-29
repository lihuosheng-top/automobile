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
            $order_data = db("order_parts")->where("user_id", $user_id)->select();
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
     **************李火生*******************
     * @param Request $request
     * Notes:系统消息页面详情
     **************************************
     * @return \think\response\View
     */
    public function information_system(){
        return view('information_system');
    }



}