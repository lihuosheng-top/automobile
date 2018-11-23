<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/23
 * Time: 14:05
 */
namespace app\index\controller;
use think\Controller;

class Store extends Controller{


    /**
     * 店铺首页
     * 陈绪
     */
    public function index(){

        return view("store_index");

    }


}