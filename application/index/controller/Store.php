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



    /**
     * 我要加盟
     * 陈绪
     */
    public function league(){

        return view("store_league");

    }




    /**
     * 身份验证
     * 陈绪
     */
    public function verify(){

        return view("store_verify");

    }



}