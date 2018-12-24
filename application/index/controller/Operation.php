<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/20
 * Time: 15:34
 */
namespace app\index\controller;
use think\Controller;

class Operation extends Controller{


    /**
     * 投诉中心
     * 陈绪
     */
    public function home(){

        return view("operation_index");

    }



    /**
     * 紧急救援
     * 陈绪
     */
    public function rescue(){

        return view("rescue_index");

    }


}