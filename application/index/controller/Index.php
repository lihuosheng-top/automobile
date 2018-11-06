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

    // 服务类型
    public function Service_type()
    {
        return view("service_type");
    }

    // 预约服务 首页
    public function Reservation()
    {
        return view("reservation");
    }

    // 预约服务 详情
    public function Reservation_detail()
    {
        return view("reservation_detail");
    }
}
