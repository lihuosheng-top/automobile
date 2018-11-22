<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/22
 * Time: 14:41
 */
namespace app\index\controller;
use think\Controller;

class Reservation extends Controller{


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

}