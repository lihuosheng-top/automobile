<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/10/25
 * Time: 11:22
 */
namespace  app\admin\controller;

use think\Controller;
use think\Session;
use think\Config;

class Home extends Controller{
    /**
     **************邹梅*******************
     * @return \think\response\View
     * 首页
     **************************************
     */
    public function index(){

        $admin = Config::get("admin");
        return view('index',["admin"=>$admin]);
    }

    
}