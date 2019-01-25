<?php
namespace app\index\controller;

use think\Controller;
use think\Paginator;
use think\Request;
use think\Session;

use think\Db;
class Index extends Controller
{


    /**
     * 首页
     * 陈绪
     */
    public function index(Request $request)
    {
        if($request->isPost()) {
            $user_id = Session::get("user");
            if (!empty($user_id)) {
                $user_car = db("user_car")->where("user_id", $user_id)->where("status", 1)->select();
                if ($user_car) {

                    return ajax_success("获取成功", $user_car);
                } else {
                    return ajax_error("获取失败");
                }

            }else {
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
        }

        return view("index");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:首页目的不让直接进入前台页面
     **************************************
     * @return \think\response\View
     */
    public function home()
    {
        return view("home");
    }


    /**
     * 微信扫码
     * 陈绪
     */
    public function saoma_callback(Request $request)
    {
        //扫码支付，接收微信请求;
        if($request->isPost()){
            $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
            $xml_data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            $val = json_decode(json_encode($xml_data), true);
            if($val["result_code"] == "SUCCESS" ){
                $goods = strstr($val["out_trade_no"],"g");
                $goods_id = substr($goods,1);
                $ids = explode("g",$goods_id);
                foreach ($ids as $value){
                    $bool = db("goods")->where("id",$value)->update(["putaway_status"=>1,"goods_status"=>1]);
                }

                if($bool){
                    return ajax_success("成功",$bool);
                }else{
                    return ajax_error("失败");
                }

            }

        }
    }





    /*
       微信通知地址
       */
    public function weixin_notify(){
        include_once(EXTEND_PATH . "lib/payment/wxpay/WxPayPubHelper.php");
        //使用通用通知接口
        $notify = new \Notify_pub();//实例化通知类 要不要加斜杠到线上测试

        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);

        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if ($notify->checkSign() == FALSE) {//这个验证签名要线上测试才知道
            //file_put_contents(S_ROOT.'data/failed_'.time().'.txt',var_export($_POST,true));
            $notify->setReturnParameter("return_code", "FAIL"); //返回状态码
            $notify->setReturnParameter("return_msg", "签名失败"); //返回信息
        } else {

            //记录日志
            $int_order_id = str_replace($notify->data["appid"] . 'b', '', $notify->data["out_trade_no"]);//刚才在上面写的 订单号 以b来替换 拿到真实的数据库主键id
            $int_order_id = intval($int_order_id);
            $int_total_fee = number_format($notify->data["total_fee"], 2, '.', '');//得到微信支付的金额
            $str_fee_type = strtoupper(trim($notify->data["fee_type"]));//得到微信支付的币种

            //file_put_contents(S_ROOT.'data/success_order_id_'.time().'.txt','订单ID:'.$int_order_id);
            /*$obj_wxpay = L::loadClass('wxpay', 'payment');//这个是日志表 你可以建一个自己的来用 或者不要
            $arr_log_data = array(
                    'order_id' => $int_order_id, //订单id
                    'pay_type' => 0, //支付类型为购物
                    'is_update_ok' => 0, //默认更新失败
                    'return_info' => str_addslashes(var_export($xml, TRUE)),
                    'in_date' => $_SGLOBAL['timestamp'],
            );*/
            //设置默认通知微信的返回码为SUCCESS,如果微信提示支付成功但是本地更新数据库失败则让通知微信接收失败，让微信再次来通知

            if ($notify->data["return_code"] != "FAIL" && $notify->data["result_code"] != "FAIL") {

                $obj_order = L::loadClass('order', 'index');//加载订单表 就是你那个goods数据表
                $arr_order = $obj_order->get_one_main($int_order_id); //获取到支付的那条数据

                $int_main_total_fee = ($arr_bat_pay['price']) * 100;//z这个是你数据表里面的价格 用于对比跟微信支付过来的金额是否一致


                //$int_main_total_fee = ($arr_order['total_price']) * 100;
                $int_main_total_fee = number_format($int_main_total_fee, 2, '.', '');//请不要调整此处与上面$int_total_fee = number_format($notify->data["total_fee"], 2, '.', '');处否则会出现付款金额为19.9 bug

                if ($str_fee_type != 'CNY' || $int_total_fee != $int_main_total_fee) {//币种或者金额非法
                    //file_put_contents(S_ROOT.'data/price_error'.time().'.txt',$int_total_fee.','.$int_main_total_fee);
                    $notify->setReturnParameter("return_code", "SUCCESS");
                } else {
                    //第三方交易信息
                    $arr_third_pay_data = array(
                        'third_id' => str_addslashes($notify->data["transaction_id"]), //交易号
                        //'pay_type'=>LEM_order::PAY_TYPE_WEIXINPAY
                    );
                    $arr_res = $obj_order->do_pay_success($arr_order, $arr_third_pay_data['third_id']);//订单类里的回调方法 此方法写更新你的上架状态还有是否已支付
                    //$bool_update = $obj_order->update_main(array('order_id'=>$int_order_id),array('status'=>LEM_order::ORDER_PAY,'third_id'=>$arr_third_pay_data['third_id'],'pay_date'=>$_SGLOBAL['timestamp']));
                    //file_put_contents(S_ROOT.'data/update_bool'.time().'.txt',$bool_update);
                    if ($arr_res['status'] == 200) {//订单数据更新成功
                        //$arr_log_data['is_update_ok'] = 1; //更新成功
                        $notify->setReturnParameter("return_code", "SUCCESS");
                    } else {
                        $notify->setReturnParameter("return_code", "FAIL"); //返回状态码
                    }
                }
            }
            //$obj_wxpay->log($arr_log_data);//插入log日志
        }

        $returnXml = $notify->returnXml();
        echo $returnXml;
    }





    /**
     * 热门配件店铺
     * 陈绪
     */
    public function shop(Request $request){

        if($request->isPost()){
            $where ="`is_hot_store` = '1' and `store_is_button` = '1' and `del_status` = '1' and `operation_status` = '1' ";
            $shop = db("store")->where($where)->select();
            $shop_data  = [];
            foreach ($shop as $key=>$value){
                $shop_data[$key]["shop_address"] = $value["store_city_address"].",".$value["store_street_address"];
                $shop_data[$key]["shop_images"] = $value["verifying_physical_storefront_one"];
                $shop_data[$key]["id"] = $value["store_id"];
                $shop_data[$key]["shop_time"] = $value["store_do_bussiness_time"];
                $shop_data[$key]["shop_name"] = $value["store_name"];
                $shop_data[$key]["latitude"] = $value["latitude"];
                $shop_data[$key]["longitude"] = $value["longitude"];
                $parts_attitude_stars =Db::name("order_parts_evaluate")
                    ->where("store_id",$value["store_id"])
                    ->avg('service_attitude_stars');
                $service_attitude_stars =Db::name("order_service_evaluate")
                    ->where("store_id",$value["store_id"])
                    ->avg("service_attitude_stars");
                if(!empty($parts_attitude_stars) && (!empty($service_attitude_stars))){
                    $sum =(round($parts_attitude_stars,2) + round($service_attitude_stars,2))/2;
                    $shop_data[$key]["store_star"] =round($sum,2);
                }else if(!empty($parts_attitude_stars) && empty($service_attitude_stars)){
                    $sum =round($parts_attitude_stars,2);
                    $shop_data[$key]["store_star"] =round($sum,2);
                }else if(empty($parts_attitude_stars) && (!empty($service_attitude_stars))){
                    $sum = round($service_attitude_stars,2);
                    $shop_data[$key]["store_star"] =round($sum,2);
                }else{
                    $shop_data[$key]["store_star"] =0;
                }
            }
            return ajax_success("获取成功",$shop_data);
        }

    }




    /**
     * 热门配件店铺中的商品
     * 陈绪
     */
    public function shop_goods(Request $request){

        if($request->isPost()) {
            $data = Session::get("role_name_store_id");
            $user_id = Session::get("user");
            if (empty($user_id)) {
                $shop_id = $request->only(["id"])["id"];
                $goods = db("goods")->where("store_id", $shop_id)->where("goods_status", 1)->select();
                foreach ($goods as $k_1=>$v_1){
                    $goods[$k_1]['goods_adjusted_money'] = db("special")->where("goods_id", $goods[$k_1]['id'])->min('goods_adjusted_price');
                    if($v_1["goods_standard"] != "通用"){
                        unset($goods[$k_1]);
                    }
                }
                $serve_goods = db("serve_goods")->where("store_id", $shop_id)->where("status", 1)->select();
                $store = db("store")->where("store_id", $shop_id)->order("store_order_num")->select();
                foreach ($store as $k_2=>$v_2){
                    $store[$k_2]["advert_text"] = db("platform")->where("store_id",$v_2["store_id"])->value("advert_text");
                }
                $service_setting = db("service_setting")->select();
                $serve_data = [];
                foreach ($service_setting as $key => $value) {
                    foreach ($serve_goods as $val) {
                        if ($value["service_setting_id"] == $val["service_setting_id"]) {
                            $serve_data[$key]["serve_name"] = $value["service_setting_name"];
                            $serve_data[$key]["serve_goods"] = db("serve_goods")->where("service_setting_id", $val["service_setting_id"])->where("store_id", $shop_id)->select();
                            $serve_data[$key]["service_setting_id"] = $val["service_setting_id"];
                        }
                    }
                }
                if ($goods || $store || $serve_data) {
                    return ajax_success("获取成功", array("store" => $store, "goods" => $goods, "serve_data" => $serve_data));
                } else {
                    return ajax_error("获取失败");
                }
            }else {
                $user_id = Session::get("user");
                $user_car = db("user_car")->where("user_id", $user_id)->where("status", 1)->find();
                $car_series = db("car_series")->where("brand", $user_car["brand"])->where("series", $user_car["series"])->field("vehicle_model")->find();
                if (!empty($car_series)) {
                    $shop_id = $request->only(["id"])["id"];
                    $goods = db("goods")->where("store_id", $shop_id)->where("goods_status", 1)->select();
                    if(empty($data)){
                        foreach ($goods as $k_1=>$v_1){
                            $goods[$k_1]['goods_adjusted_money'] = db("special")->where("goods_id", $goods[$k_1]['id'])->min('goods_adjusted_price');
                            if($v_1["goods_standard"] != "通用"){
                                unset($goods[$k_1]);
                            }
                        }
                    }else{
                        foreach ($goods as $k_1 => $v_1) {
                            $goods[$k_1]['goods_adjusted_money'] = db("special")->where("goods_id", $goods[$k_1]['id'])->min('goods_adjusted_price');
                        }
                    }
                    $serve_vehicle_model = db("serve_goods")->where("store_id", $shop_id)->where("vehicle_model", $car_series["vehicle_model"])->find();
                    $serve_goods = db("serve_goods")->where("store_id", $shop_id)->where("status", 1)->select();
                    $store = db("store")->where("store_id", $shop_id)->order("store_order_num")->select();
                    foreach ($store as $k_2=>$v_2){
                        $store[$k_2]["advert_text"] = db("platform")->where("store_id",$v_2["store_id"])->value("advert_text");
                    }
                    $serve_data = [];
                    $serve = [];
                    foreach ($serve_goods as $k => $val) {
                        if ($val["service_setting_id"] == $serve_vehicle_model["service_setting_id"]) {
                            unset($serve_goods[$k]);
                        }
                    }

                    $serve_goods[] = $serve_vehicle_model;

                    //相同数组合并
                    foreach ($serve_goods as $k_1 => $v_1) {
                        $serve_data[$v_1['service_setting_id']]["serve_goods"][] = $v_1;
                        $serve_data[$v_1['service_setting_id']]["serve_name"] = db("service_setting")->where("service_setting_id", $v_1["service_setting_id"])->value("service_setting_name");
                        $serve_data[$v_1['service_setting_id']]["service_setting_id"] = $v_1['service_setting_id'];

                    }
                    if ($goods || $store || $serve_goods) {
                        //exit(json_encode(array("info" => "获取成功", "status" => "2", "data" => []),JSON_UNESCAPED_UNICODE));
                        return ajax_success("获取成功", array("store" => $store, "goods" => $goods, "serve_data" => array_values($serve_data)));
                    } else {
                        exit(json_encode(array("info" => "获取成功", "status" => "3")));
                    }
                } else {
                    $shop_id = $request->only(["id"])["id"];
                    $goods = db("goods")->where("store_id", $shop_id)->where("goods_status", 1)->select();
                    if(empty($data)){
                        foreach ($goods as $k_1=>$v_1){
                            $goods[$k_1]['goods_adjusted_money'] = db("special")->where("goods_id", $goods[$k_1]['id'])->min('goods_adjusted_price');
                            if($v_1["goods_standard"] != "通用"){
                                unset($goods[$k_1]);
                            }
                        }
                    }else{
                        foreach ($goods as $k_1 => $v_1) {
                            $goods[$k_1]['goods_adjusted_money'] = db("special")->where("goods_id", $goods[$k_1]['id'])->min('goods_adjusted_price');
                        }
                    }
                    $serve_goods = db("serve_goods")->where("store_id", $shop_id)->where("status", 1)->select();
                    $store = db("store")->where("store_id", $shop_id)->order("store_order_num")->select();
                    foreach ($store as $k_2=>$v_2){
                        $store[$k_2]["advert_text"] = db("platform")->where("store_id",$v_2["store_id"])->value("advert_text");
                    }
                    $service_setting = db("service_setting")->select();
                    $serve_data = [];
                    foreach ($service_setting as $key => $value) {
                        foreach ($serve_goods as $val) {
                            if ($value["service_setting_id"] == $val["service_setting_id"]) {
                                $serve_data[$key]["serve_name"] = $value["service_setting_name"];
                                $serve_data[$key]["serve_goods"] = db("serve_goods")->where("service_setting_id", $val["service_setting_id"])->where("store_id", $shop_id)->select();
                                $serve_data[$key]["service_setting_id"] = $val["service_setting_id"];
                            }
                        }
                    }
                    if ($goods || $store || $serve_data) {
                        return ajax_success("获取成功", array("store" => $store, "goods" => $goods, "serve_data" => $serve_data));
                    } else {
                        return ajax_error("获取失败");
                    }
                }
            }
        }

    }




}
