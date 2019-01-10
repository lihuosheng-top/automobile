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
    public function shop_order()
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
     * Notes:服务商订单保存订单编号给订单详情
     **************************************
     * @param Request $request
     */
    public function order_service_save_record(Request $request){
        if($request->isPost()){
            $service_order_number =$request->only("service_order_number")["service_order_number"];//配件商订单编号
            if(!empty($service_order_number)){
                Session::set("service_order_number", $service_order_number);
                return ajax_success("暂存成功",['status'=>1]);
            }else{
                return ajax_error("没有这个订单编号",["status"=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商订单详情
     **************************************
     * @return \think\response\View
     */
    public function order_service_detail(Request $request){
        if($request->isPost()){
            $order_id=Session::get("service_order_number");
            $data =Db::name("order_service")
                ->where("service_order_number",$order_id)
                ->find();
            if(!empty($data)){
                return ajax_success("订单信息返回成功",$data);
            }else{
                return ajax_error("没有对应的订单信息",["status"=>0]);
            }
        }
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
                    $data =Db::name('order_service')
                        ->where('user_id',$member_id['id'])
                        ->field("service_order_number,status,service_goods_name,got_to_time,id,store_name,service_real_pay")
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
                        ->field("service_order_number,status,service_goods_name,got_to_time,id,store_name,service_real_pay")
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
                        ->field("service_order_number,status,service_goods_name,got_to_time,id,store_name,service_real_pay")
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
                        ->field("service_order_number,status,service_goods_name,got_to_time,id,store_name,service_real_pay")
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
     * Notes:服务商订单我的已完成
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
                        ->field("service_order_number,status,service_goods_name,got_to_time,id,store_name,service_real_pay")
                        ->where('user_id',$member_id['id'])
                        ->where('status',6)
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
     * @param Request $request
     * Notes:服务商填写预约信息到达确认订单页面
     **************************************
     * @param Request $request
     */
   public function ios_api_order_service_save_information(Request $request){
            if($request->isPost()){
                $store_id =$request->only("store_id")["store_id"];
                $service_setting_id =$request->only("service_setting_id")["service_setting_id"];
                $service_goods_id =$request->only("serve_goods_id")["serve_goods_id"];
                $times =$request->only("time")["time"]; //选择到店时间
                $data =[
                    "store_id" =>$store_id,
                    "service_setting_id"=>$service_setting_id,
                    "service_goods_id"=>$service_goods_id,
                    "time"=>$times,
                ];
                if(!empty($data)){
                        Session::set("service_data",$data);
                        return ajax_success("保存信息成功",["status"=>1]);
                }
            }
   }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商获取预约信息，准备下单
     **************************************
     * @param Request $request
     */
   public function ios_api_order_service_get_information(Request $request){
            if($request->isPost()){
                    $data =  Session::get("service_data");
                    if(!empty($data)){
                            $user_id = Session::get("user");//用户id
                            //用户的爱车
                            $user_car = db("user_car")
                                ->where("user_id",$user_id)
                                ->where("status",1)
                                ->find();
                            //用户爱车信息
                            $user_love_info =Db::name("user_car_message")->where("user_car_id",$user_car["id"])->find();
                            //服务项目的信息
                            $car_series = db("car_series")
                                ->where("brand",$user_car["brand"])
                                ->where("series",$user_car["series"])
                                ->where("year",$user_car["production_time"])
                                ->where("displacement",$user_car["displacement"])
                                ->find();
                            $serve_goods_id = $data["service_goods_id"]; //商品id
                            //商品信息
                            $goods = db("serve_goods")
                                ->where("id",$serve_goods_id)
                                ->where("status",1)
                                ->select();
                             $serve_store_id = $data["store_id"]; //店铺id
                            //店铺信息
                            $store = db("store")->where("store_id",$serve_store_id)->select();
                            $serve_data = [];
                            foreach ($store as $key=>$value){
                                $serve_data[$key]["serve_goods"] = db("serve_goods")
                                    ->where("store_id",$value["store_id"])
                                    ->where("vehicle_model",$car_series["vehicle_model"])
                                    ->select();
                                $serve_data[$key]["serve_name"] = db("service_setting")
                                    ->where("service_setting_id",$serve_data[$key]["serve_goods"][0]["service_setting_id"])
                                    ->find();
                            }
                            $user_info =Db::name("user")
                                ->field("user_name,phone_num,real_name")
                                ->where("id",$user_id)
                                ->find();
                            $integral_discount =Db::name("integral_discount_settings")->select();
                            $ios_data =[
                                "goods"=>$goods,//商品信息
                                "store"=>$store,//店铺
                                "serve_data"=>$serve_data, //服务信息
                                "time"=>$data["time"],   //预约到店时间
                                "integral_info"=> $integral_discount, //积分抵扣
                                "user_info"=> $user_info,//用户信息
                                "car_series"=>$car_series,//车信息
                                "user_love_info"=>$user_love_info, //爱车信息（默认）
                            ];
                            if($serve_data){
                                return ajax_success("获取成功",$ios_data);
                            }else{
                                return ajax_error("没有数据");
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
            $user_id = Session::get('user');
            //用户信息
            $member = Db::name('user')->where("id",$user_id)->find();
            //用户爱车信息（默认）
            $user_car_data =Db::table("tb_user_car")->where("user_id",$user_id)->where("status",1)->find();
            //默认爱车的车牌信息
            $user_love_car_message =Db::name("user_car_message")->where("id",$user_car_data["id"])->find();
            if(!empty($member["real_name"])){
                $member_name =$member["real_name"];
            }else{
                $member_name =$member["user_name"];
            }
            $commodity_id = $_POST['goods_id'];
            if (!empty($commodity_id)) {
                //积分
                if(!empty($data["setting_id"])){
                    $setting_data =Db::name("integral_discount_settings")->where("setting_id",$data["setting_id"])->find();
                    $integral_deductible =$setting_data["deductible_money"];
                    $integral_discount_setting_id =$data["setting_id"];
                    $integral_deductible_num =$setting_data["integral_full"];
                }else{
                    $integral_deductible = 0;
                    $integral_discount_setting_id =NULL;
                    $integral_deductible_num =NULL;
                }

                $goods_data = Db::table('tb_serve_goods')
                    ->join("tb_service_setting","tb_serve_goods.service_setting_id =tb_service_setting.service_setting_id","left")
                    ->where('tb_serve_goods.id', $commodity_id)
                    ->find();
                $store_name =Db::name("store")
                    ->where("store_id",$goods_data["store_id"])
                    ->value("store_name");
                $create_time = time();
                $time=date("Y-m-d",time());
                $v=explode('-',$time);
                $time_second=date("H:i:s",time());
                $vs=explode(':',$time_second);
                $service_order_number =$v[0].$v[1].$v[2].$vs[0].$vs[1].$vs[2].rand(1000,9999).$user_id; //订单编号
                if (!empty($data)) {
                    $datas = [
                        'service_goods_name' => $goods_data['vehicle_model'], //车型
                        "service_goods_image" =>$goods_data["service_setting_calss_img"],//服务项目图片
                        'service_order_quantitative' => $data['service_order_quantitative'],//订单数量
                        "service_goods_name" =>$goods_data["service_setting_name"],//服务项目名称
                        'user_id' => $user_id,         //用户id
                        'create_time' => $create_time,//创建时间
                        'service_order_amount' => $goods_data['service_money'],//服务金额
                        "car_owner_phone_number"=>$member["phone_num"],//联系方式
                        "car_owner_name"=>$member_name,//车主名字
                        "car_number"=> $user_love_car_message["plate_number"],//车牌号
                        "car_information"=>$data["car_information"],//爱车信息
                        "reserve_time"=>null, //因为还没有服务所以没有服务时间
                        'status' => 1,      //订单状态
                        'service_goods_id' => $commodity_id,        //服务项目ID
                        'service_order_number' => $service_order_number,//订单号
                        "got_to_time"=>$data["got_to_time"],//预约到店时间
                        "store_id"=>$goods_data["store_id"],//商铺id
                        "store_name"=>$store_name,//商铺id
                        "service_reservations"=>$store_name,//预约门店（店铺名称）
                        "service_real_pay"=>$data["service_money"],//积分抵扣之后的金额
                        "order_amount"=>$goods_data["service_money"],//订单金额
                        "integral_deductible"=> $integral_deductible,//积分抵扣（元）
                        "integral_discount_setting_id"=>$integral_discount_setting_id, //积分设置中的id
                        "integral_deductible_num" =>$integral_deductible_num, //使用了多少积分
                    ];
                    $res = Db::name('order_service')->insertGetId($datas);
                    if ($res) {
                        $order_datas =Db::name("order_service")
                            ->field("service_real_pay,service_goods_name,service_order_number")
                            ->where('id',$res)
                            ->where("user_id",$user_id)
                            ->find();
                        if(!empty($data["setting_id"])){
                            //积分消费记录
                            $user_integral_wallet =$member["user_integral_wallet"]; //之前的积分余额
                            $user_integral_wallets =$user_integral_wallet - $setting_data["integral_full"];//减了之后的积分
                            $operation_times =date("Y-m-d H:i:s");
                            $integral_data =[
                                "user_id"=>$user_id,//用户ID
                                "integral_operation"=>"-".$setting_data['integral_full'],//积分操作
                                "integral_balance"=>$user_integral_wallets,//积分余额
                                "integral_type"=> -1,//积分类型
                                "operation_time"=>$operation_times ,//操作时间
                                "integral_remarks"=>"订单号:".$order_datas['service_order_number']."下单使用积分".$setting_data['integral_full']."抵扣".$setting_data["deductible_money"]."元钱",//积分备注
                            ];
                            Db::name("user")->where("id",$user_id)->update(["user_integral_wallet"=>$user_integral_wallets,"user_integral_wallet_consumed"=>$setting_data["integral_full"]+$member["user_wallet_consumed"]]);
                            Db::name("integral")->insert($integral_data); //插入积分消费记录
                        }
                        return ajax_success('下单成功', $order_datas);
                    }else{
                        return ajax_error("下单失败",["status"=>0]);
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


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商订单删除
     **************************************
     * @param Request $request
     */
    public function service_del(Request $request){
        if($request->isPost()){
            $id =$_POST['order_id'];
            if(is_array($id)){
                $where ='id in('.implode(',',$id).')';
            }else{
                $where ='id='.$id;
            }
            $list =  Db::name('order_service')->where($where)->delete();
            if($list!==false)
            {
                //如果评价过的则进行评价删除
                $is_set_evaluate =Db::name("order_service_evaluate")
                    ->where("order_id",$id)
                    ->value("id");
                if(!empty($is_set_evaluate)){
                    $is_set_img =Db::name("order_service_evaluate_images")
                        ->where("evaluate_order_id",$is_set_evaluate)
                        ->select();
                    if(!empty($is_set_img)){
                        foreach ($is_set_img as $ks=>$vs){
                            unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$vs['images']);
                            Db::name("order_service_evaluate_images")
                                ->where("evaluate_order_id",$vs["id"])
                                ->delete();
                        }
                    }
                    Db::name("order_service_evaluate")
                        ->where("order_id",$id)
                        ->delete();
                }
                return ajax_success('成功删除!',['status'=>1]);
            }else{
                return ajax_error('删除失败',['status'=>0]);
            }
        }
    }




}