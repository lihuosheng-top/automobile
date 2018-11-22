<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/22 0022
 * Time: 17:20
 */
namespace app\index\controller;

use think\Controller;
use think\Db;
class Apppay extends Controller
{
    /**
     **************李火生*******************
     * 异步处理(支付宝IOS对接)
     **************************************
     */
    public function notifyurl()
    {
        //这里可以做一下你自己的订单逻辑处理
        $pay_time = time();
        $data['pay_time'] = $pay_time;
        //原始订单号
        $out_trade_no = input('out_trade_no');
        //支付宝交易号
        $trade_no = input('trade_no');
        //交易状态
        $trade_status = input('trade_status');
        if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
            $data['status'] = 2;
            $condition['service_order_number'] = $out_trade_no;
            $select_data = Db::name('order_service')->where($condition)->select();
            foreach ($select_data as $key => $val) {
                $result = Db::name('order_service')->where($condition)->update($data);//修改订单状态,支付宝单号到数据库
            }
            if ($result) {
                return ajax_success('支付成功', ['status' => 1]);
            } else {
                return ajax_error('验证失败了', ['status' => 0]);
            }
        } else {
            return ajax_error('验证失败', ['status' => 0]);
        }
    }
}