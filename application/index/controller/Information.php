<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4 0004
 * Time: 11:47
 */
namespace app\index\controller;
use think\Controller;

class  Information extends  Controller{

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:消息页面
     **************************************
     * @return \think\response\View
     */
    public function index(){
        return view('index');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:订单消息页面详情
     **************************************
     * @return \think\response\View
     */
    public function information_details(){
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