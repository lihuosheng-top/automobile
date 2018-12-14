<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/13 0013
 * Time: 10:01
 */
namespace  app\index\controller;

use think\Controller;

class Evaluate extends  Controller{
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:评价页面
     **************************************
     */
    public function evaluate_index(){
        return view("evaluate_index");
    }
}