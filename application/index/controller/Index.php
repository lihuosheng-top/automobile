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

    // 填写预约信息
    public function Reservation_info()
    {
        return view("reservation_info");
    }
    // 提交订单 --店铺洗车
    public function Shop_order()
    {
        return view("shop_order");
    }
}
