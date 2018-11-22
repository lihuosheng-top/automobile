<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/22
 * Time: 14:55
 */
namespace app\index\controller;
use think\Controller;

class OrderService extends Controller{


    /**
     * 服务订单首页
     */
    // 提交订单 --店铺洗车
    public function Shop_order()
    {
        return view("shop_order");
    }

}