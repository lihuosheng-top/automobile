<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/26
 * Time: 17:00
 */
namespace app\index\controller;
use think\Controller;

class Marketing extends Controller{


    /**
     * 我要推广
     * 陈绪
     */
    public function generalize(){

        return view("marketing_generalize");

    }

}
