<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17 0017
 * 快递员
 * Time: 17:41
 */

namespace  app\index\controller;


use think\Controller;

class  Express extends  Controller{
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:待接单页面
     **************************************
     * @return \think\response\View
     */
    public function express_wait_for_order(){
        return view("express_wait_for_order");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:待取货页面
     **************************************
     * @return \think\response\View
     */
    public function express_wait_for_take(){
        return view("express_wait_for_take");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配送中页面
     **************************************
     * @return \think\response\View
     */
    public function express_distribution(){
        return view("express_distribution");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:已完成页面
     **************************************
     * @return \think\response\View
     */
    public function express_completed(){
        return view("express_completed");
    }



}