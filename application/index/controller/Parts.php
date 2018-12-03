<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/23
 * Time: 14:00
 */
namespace app\index\controller;
use think\Controller;

class Parts extends Controller{


    /**
     * 配件商城
     * 陈绪
     */
    public function parts_index(){

        return view("parts_index");

    }

}