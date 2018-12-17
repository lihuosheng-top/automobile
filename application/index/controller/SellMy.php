<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/12 0012
 * Time: 14:46
 */
namespace app\index\controller;


use think\Controller;

class  SellMy extends Controller{

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家我的页面
     **************************************
     */
    public function sell_my_index(){
        return view("sell_my_index");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家服务订单
     **************************************
     */
    public function sell_service_order(){
        return view("sell_service_order");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家商品订单
     **************************************
     */
    public function sell_parts_order(){
        return view("sell_parts_order");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家服务订单详情
     **************************************
     */
    public function sell_service_order_detail(){
        return view("sell_service_order_detail");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家商品订单详情
     **************************************
     */
    public function sell_parts_order_detail(){
        return view("sell_parts_order_detail");
    }
    public function sell_service_record(){
        return view("sell_service_record");
    }
    public function sell_parts_record(){
        return view("sell_parts_record");
    }
    public function sell_order_bill(){
        return view("sell_order_bill");
    }
    public function sell_wallet(){
        return view("sell_wallet");
    }
    public function sell_application(){
        return view("sell_application");
    }






}