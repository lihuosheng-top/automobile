<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    // 首页
    public function index()
    {
        return view("index");
    }



}
