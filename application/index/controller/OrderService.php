<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/22
 * Time: 14:55
 */
namespace app\index\controller;
use think\Controller;
use think\Session;
use  think\Db;
use think\Request;

class OrderService extends Controller{

    /**
     * 服务订单首页
     */
    // 提交订单 --店铺洗车
    public function Shop_order()
    {
        return view("shop_order");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商订单我的所有订单
     **************************************
     * @return \think\response\View
     */
    public function order_service_all(){
        return view('order_service_all');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商订单详情
     **************************************
     * @return \think\response\View
     */
    public function order_service_detail(){
        return view('order_service_detail');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商订单我的所有订单接口
     **************************************
     * @param Request $request
     */
    public function ios_api_order_service_all(Request $request){
        if($request->isPost()){
            $datas =session('member');
            if(!empty($datas)){
                $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
                if(!empty($datas)){
                    $data =Db::name('order_service')->where('user_id',$member_id['id'])->order('create_time','desc')->select();
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
     * Notes:服务商订单我的待付款
     **************************************
     * @return \think\response\View
     */
    public function order_service_wait_pay(){
        return view('order_service_wait_pay');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商订单我的待付款接口
     **************************************
     * @param Request $request
     */
    public function ios_api_order_service_wait_pay(Request $request){
        if($request->isPost()){
            $datas =session('member');
            if(!empty($datas)){
                $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
                if(!empty($datas)){
                    $data =Db::name('order_service')
                        ->where('user_id',$member_id['id'])
                        ->where('status',1)
                        ->order('create_time','desc')
                        ->select();
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
     * Notes:服务商订单我的待服务
     **************************************
     * @return \think\response\View
     */
    public function order_service_wait_deliver(){
        return view('order_service_wait_deliver');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商订单我的待服务接口
     **************************************
     * @param Request $request
     */
    public function ios_api_order_service_wait_deliver(Request $request){
        if($request->isPost()){
            $datas =session('member');
            if(!empty($datas)){
                $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
                if(!empty($datas)){
                    $condition ="`status` = '2' or `status` = '3'";
                    $data =Db::name('order_service')
                        ->where('user_id',$member_id['id'])
                        ->where($condition)
                        ->order('create_time','desc')
                        ->select();
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
     * Notes:服务商订单我的待评价
     **************************************
     * @return \think\response\View
     */
    public function order_service_wait_evaluate(){
        return view('order_service_wait_evaluate');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商订单我的待评价接口
     **************************************
     * @param Request $request
     */
    public function ios_api_order_service_wait_evaluate(Request $request){
        if($request->isPost()){
            $datas =session('member');
            if(!empty($datas)){
                $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
                if(!empty($datas)){
                    $condition ="`status` = '4' or `status` = '5'";
                    $data =Db::name('order_service')
                        ->where('user_id',$member_id['id'])
                        ->where($condition)
                        ->order('create_time','desc')
                        ->select();
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
     * Notes:服务商订单我的退货
     **************************************
     * @return \think\response\View
     */
    public function order_service_return_goods(){
        return view('order_service_return_goods');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商订单我的退货接口
     **************************************
     * @return \think\response\View
     */
    public function ios_api_order_service_return_goods(Request $request){
        if($request->isPost()){
            $datas =session('member');
            if(!empty($datas)){
                $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
                if(!empty($datas)){
                    $data =Db::name('order_service')
                        ->where('user_id',$member_id['id'])
                        ->where('status',11)
                        ->order('create_time','desc')
                        ->select();
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



    /*************************************修改状态
     *************************************************************/
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商订单未付款 取消订单
     **************************************
     */
    public function ios_api_order_service_no_pay_cancel(Request $request){
        if($request->isPost()){
            $order_id =$_POST['order_id'];
            if(!empty($order_id)){
                $res =Db::name('order_service')->where('id',$order_id)->update(['status'=>9]);
                if($res){
                    return ajax_success('订单取消成功',['status'=>1]);
                }else{
                    return ajax_error('订单取消失败',['status'=>0]);
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商订单买家确认服务（确认收货）
     **************************************
     * @param Request $request
     */
    public function ios_api_order_service_already_served(Request $request){
        if($request->isPost()){
            $order_id =$_POST['order_id'];
            if(!empty($order_id)){
                $res =Db::name('order_service')->where('id',$order_id)->update(['status'=>5]);
                if($res){
                    return ajax_success('确认服务成功',['status'=>1]);
                }else{
                    return ajax_error('确认服务失败',['status'=>0]);
                }
            }
        }
    }









    /**
     **************李火生*******************
     * ios提交订单传过来的参数形成订单存库并返回对应的订单号给IOS
    'goods_id' 商品id
     * 'service_order_quantitative' //订单数量
     * service_money                //服务金额
     **************************************
     */
    public function  ios_api_order_service_button(Request $request)
    {
        if ($request->isPost()) {
            $data = $_POST;
            $member_data = session('member');
            $member = Db::name('user')->field('id,harvester,harvester_phone_num,harvester_real_address')->where('phone_num', $member_data['phone_num'])->find();
            $commodity_id = $_POST['goods_id'];
            if (!empty($commodity_id)) {
                $goods_data = Db::name('serve_goods')->where('id', $commodity_id)->find();
                $create_time = time();
                if (!empty($data)) {
                    $datas = [
                        'service_goods_id' => $goods_data['id'],//服务项目ID
                        'service_goods_name' => $goods_data['vehicle_model'], //车型
                        'service_order_quantitative' => $data['service_order_quantitative'],      //订单数量
//                        'service_order_quantitative' => 1,      //订单数量
                        'user_id' => $member['id'],         //用户id
                        'create_time' => $create_time,
                        'service_order_amount' => $data['service_money'],//服务金额
//                        'service_order_amount' => 0.01,//服务金额
                        'status' => 1,      //订单状态
                        'service_goods_id' => $commodity_id,        //服务项目ID
                        'service_order_number' => $create_time . $member['id'],//时间戳+用户id构成订单号
                    ];
                    $res = Db::name('order_service')->insertGetId($datas);
                    if ($res) {
                        return ajax_success('下单成功', $datas['service_order_number']);
                    }
                }
            }
        }
    }
    /**
     **************李火生*******************
     * 生成支付宝签名 TODO:支付宝签名
     **************************************
     */
    public function ios_api_alipay(Request $request){
        if($request->isPost()){
            $order_num =$request->only(['order_num'])['order_num'];
//            $order_num =15428821121022;
            $product_code ="QUICK_MSECURITY_PAY";
            $out_trade_no="ZQLM3O56MJD4SK3";
            $time =date('Y-m-d H:i:s');
            if(!empty( $order_num)){
                $counts =Db::name('order_service')->where('service_order_number',$order_num)->count();
                if(!empty($counts)){
                    $data = Db::name('order_service')->where('service_order_number',$order_num)->select();
                    if(!empty($data)){
                        foreach ($data as $k=>$v){
                            $goods_name = $v['service_goods_name'];
                            $order_num = $v['service_order_number'];
                            $goods_pay_money =$v['service_order_amount'];
                            $subject =$v['service_order_quantitative'];
                            $app_id ="{'timeout_express':'30m','seller_id':"."'".$order_num."'".",'product_code':"."'".$product_code."'".",'total_amount':"."'".$goods_pay_money."'".",'subject':"."'".$subject."'".",'body':"."'".$goods_name."'".",'out_trade_no':"."'".$out_trade_no."'"."}";
                            $app_ids =urlencode($app_id);
                            $time_encode =urlencode($time);
                            // 订单信息，在iOS端加密
                            include('../vendor/Alipays/aop/AopClient.php');
                            $private_path="MIIEpAIBAAKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQABAoIBAQCBQK730TFmpuTOtc669y6BOzUX1EWe+C/mYO28Dn7vqUGbU7UkuihtQIpcNCHhhGAXIHEH0zzrMH3b8XXdXjmo2ChBstr7elJlX2a7WYf9kHNTfRDCE+q5Xj7niSSYE6HOgvWDFMg9nyE3P0WRmTeEvjfVsv2SMoxxIBd8yD1Vxr3Gbg+gT8zWDrqXQ1Ap1gg5jNS14CFE3uKKwQ4n5JZWnIQ+jw3LZcpk9Eb/mrQ9kbnU7g0ikx8sYJpTiP7lAlb3dq1tdUmRV8+HfWYC/a8MbZtO6UyDWvms5Lb5g4we7FCmBAkG+zv62PxG9sQAvrQoSwKTOj/7LSeTgJsT97QNAoGBAPuQUNZEhVODVhCCISg84TGi0BozU64PqegJXFxbR++hQC2EsN6L2Mk2ftpd+J/9XRD0ffcBMea+H4N7ui4Y+OHoED/8d76dTX06PWfAYYJMu/o65c3IBSBiwgREuRo38a20CZ8hKr8LVpLXbtCB8WJ1kp5QeqqSPpwnjFncyBorAoGBAMu3Hokjze+FPpeFQ3tYVt9G/VSAhRMVAb5ZQClQH9plpVM9aMukp8jiaeSBg7d5RzNRGRU5ouKQ1AVs3jkgvVzUWRMKM+VkW4lzAhEkM766egpzngs9z4YXHcBW1bPJQap2TVLRcFmueDsVABXF5XZSgAwenBhtvmZ9X/UDCD+LAoGBALmXaOwLNUm9lVsshgXHlGQoN9t8jnnV+IXFkixY86NolY5/XHVzOwaHe+LifTCbnXOKzPvUF9qh3WIFf//OUJ9ps8NhIX6xUp/WvcKzfbzBm9Uqaqv8qzuPYJABm4YqS9TZBFgwAfdcCAzhf1G47Dq1fuvpd/YrWqGd07/gUIhtAoGAHDSkg7RzZQB75BrNdxyKGqwHk1WgFz5HWYWd/ppbbq+4LkhIZDnOCWBf7QWJqTOfihlmcavjQ59t27pxIlPIJDw6gQpemRpGGkfUN29dwsCq+Rt8/G14eEZnFiRvvk7VSrbKifb5qVEg0H1d36Xg2Xsew47Ragh33lTpnlDnKXUCgYBIuk9VU3DkITWsy+xiQbN4eQqbiFB7BA55xIjwPqK8K+0PVzRyObUEF6m9KSz2mEB1CHwr1fHj8qzJ/0CgKUeCONm5crLEGCGMbGUzMloGmVLSJz6+4xT8mwKOv/BcpTqkDLx+8HBaJppJnjWn0OmHLNa1JhAaVuef8eheH546kw==";
                            //构造业务请求参数的集合(订单信息)
                            $content = array();
                            $content['subject'] = $goods_name;
                            $content['out_trade_no'] = $order_num;
                            $content['timeout_express'] = "90m";
                            $content['total_amount'] = $goods_pay_money;
                            $content['product_code'] = "QUICK_MSECURITY_PAY";
                            $con = json_encode($content);//$content是biz_content的值,将之转化成json字符串
                            //公共参数
                            $Client = new \AopClient();//实例化支付宝sdk里面的AopClient类,下单时需要的操作,都在这个类里面
                            $param['app_id'] = '2018082761132725';
                            $param['method'] = 'alipay.trade.app.pay';//接口名称，固定值
                            $param['charset'] = 'utf-8';//请求使用的编码格式
                            $param['sign_type'] = 'RSA2';//商户生成签名字符串所使用的签名算法类型
                            $param['timestamp'] = date("Y-m-d H:i:s");//发送请求的时间
                            $param['version'] = '1.0';//调用的接口版本，固定为：1.0
                            $param['notify_url'] = 'https://automobile.siring.com.cn/notifyurl';
                            $param['biz_content'] = $con;//业务请求参数的集合,长度不限,json格式，即前面一步得到的
                            $paramStr = $Client->getSignContent($param);//组装请求签名参数
                            $sign = $Client->alonersaSign($paramStr, $private_path, 'RSA2', false);//生成签名()
                            $param['sign'] = $sign;
                            $str = $Client->getSignContentUrlencode($param);//最终请求参数
                        }
                        return ajax_success('数据成功返回',$str);
                    }else{
                        return ajax_error('数据返回不成功',['status'=>0]);
                    }
                }else{
                    return ajax_error('没有这个订单号',['status'=>0]);
                }
            }else{
                return ajax_error('失败',['status'=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:生成订单(未用)
     **************************************
     * @param Request $request
     */
    public function ios_return_num(Request $request){
        if($request->isPost()){
            $ios_return_num =$request->only(['num'])['num'];
            $data =Db::name('order_service')->where('service_order_number',$ios_return_num)->find();
            if($data){
                return ajax_success('成功',$data);
            }else{
                return ajax_error('失败');
            }
        }
    }






}