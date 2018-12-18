<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4 0004
 * Time: 11:56
 */

namespace app\index\controller;

use think\Controller;

class  About extends  Controller{

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:关于我们
     **************************************
     * @return mixed
     */
    public function index(){
        return view('index');
    }
}