<?php
/**
 * Created by PhpStorm.
 * User: GY
 * Date: 2018/10/26
 * Time: 19:17
 */
namespace  app\admin\controller;

use think\Controller;

class  Advertisement extends  Controller{


    public function accessories_business_advertising(){
        return view('accessories_business_advertising');
    }



    public function accessories_business_add(){
        return view('accessories_business_add');
    }



    public function accessories_business_edit(){
        return view('accessories_business_edit');
    }



    /**
     * 配件商广告删除
     * 陈绪
     */
    public function del(){



    }

}