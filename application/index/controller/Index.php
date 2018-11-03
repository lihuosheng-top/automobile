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

    // 预约服务 首页
    public function Reservation()
    {
        return view("reservation");
    }
}
