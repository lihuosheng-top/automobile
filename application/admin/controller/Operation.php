<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/7
 * Time: 11:33
 */
namespace app\admin\controller;
use think\Controller;

class Operation extends Controller{


    /**
     * 投诉中心
     * 陈绪
     */
    public function complaint_index(){

        return view("complaint_index");

    }



    /**
     * 紧急管理
     * 陈绪
     */
    public function urgency_index(){

        return view("urgency_index");

    }


}