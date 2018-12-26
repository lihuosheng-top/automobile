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

class Operation extends Controller{


    /**
     * 紧急救援
     * 陈绪
     */
    public function rescue(Request $request){

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