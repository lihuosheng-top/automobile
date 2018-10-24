<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/10/24
 * Time: 9:56
 * 订单管理
 */
namespace app\admin\controller;

use think\Controller;

class Order extends Controller{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 订单列表
     **************************************
     */
    public function index(){

       return view('index');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     *订单编辑
     **************************************
     */
    public function edit(){
       return view('edit');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     *订单订单评价
     **************************************
     */
    public function evaluate(){
        return view('evaluate');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 订单评价详情
     **************************************
     */
    public function evaluate_details(){
        return view('evaluate_details');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     *订单维修售后
     **************************************
     */
    public function after_sale(){
        return view('after_sale');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     *订单维修售后待处理
     **************************************
     */
    public function after_sale_wait_handle(){
        return view('after_sale_wait_handle');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     *订单维修售后待发货
     **************************************
     */
    public function after_sale_wait_deliver(){
        return view('after_sale_wait_deliver');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     *发票列表
     **************************************
     */
    public function invoice(){
        return view('invoice');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     *发票信息
     **************************************
     */
    public function invoice_edit(){
        return view('invoice_edit');
    }








}