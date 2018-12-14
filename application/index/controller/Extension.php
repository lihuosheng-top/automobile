<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * 我要推广
 * Date: 2018/12/11 0011
 * Time: 10:49
 */

namespace  app\index\controller;

use think\Controller;

class Extension extends  Controller{

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:我要推广页面
     **************************************
     * @return \think\response\View
     */
    public function spread_index(){
        return view("spread_index");
    }
}