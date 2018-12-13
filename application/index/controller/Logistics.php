<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/13 0013
 * Time: 14:00
 */
namespace  app\index\controller;


use think\Controller;

class  Logistics extends  Controller{
    public function logistics_index(){
        return view("logistics_index");
    }
}