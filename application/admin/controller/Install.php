<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/22
 * Time: 11:25
 */
namespace app\admin\controller;
use think\Controller;

class Install extends Controller{

    /**
     * 价格调整设置
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index(){

        return view("install_index");

    }



    /**
     * 推荐奖励积分设置
     * 陈绪
     */
    public function recommend(){

        return view("recommend_index");

    }



    /**
     * 积分折扣设置
     * 陈绪
     */
    public function integral(){

        return view("integral_index");

    }



    /**
     * 上架年限设置
     * 陈绪
     */
    public function putaway(){

        return view("putaway_index");

    }

}