<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Session;

class Index extends Controller
{


    /**
     * 首页
     * 陈绪
     */
    public function index()
    {
        return view("index");
    }



}
