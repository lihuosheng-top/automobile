<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/26 0026
 * Time: 18:39
 */
namespace app\index\controller;

use think\Controller;
use  think\Request;
use  think\Db;

class OrderParts extends Controller{
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态全部订单
     **************************************
     * @return \think\response\View
     */
    public function order_parts_all(){
        return view('order_parts_all');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态全部订单接口
     **************************************
     * @param Request $request
     */
    public function   ios_api_order_parts_all(Request $request){
    if($request->isPost()){
        $datas =session('member');
        if(!empty($datas)){
            $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
            if(!empty($datas)){
                $data =Db::name('order_parts')->where('user_id',$member_id['id'])->order('order_create_time','desc')->select();
                if(!empty($data)){
                    return ajax_success('全部信息返回成功',$data);
                }else{
                    return ajax_error('没有订单',['status'=>0]);
                }
            }
        }else{
            return ajax_error('请登录',['status'=>0]);
        }
    }
}



    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态待付款
     **************************************
     * @return \think\response\View
     */
    public function order_parts_wait_pay(){
        return view('order_parts_wait_pay');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态待收货
     **************************************
     * @return \think\response\View
     */
    public function order_wait_deliver(){
        return view('order_wait_deliver');
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态待评价
     **************************************
     * @return \think\response\View
     */
    public function order_wait_evaluate(){
        return view('order_wait_evaluate');
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态退货
     **************************************
     * @return \think\response\View
     */
    public function order_parts_return_goods(){
        return view('order_parts_return_goods');
    }

}