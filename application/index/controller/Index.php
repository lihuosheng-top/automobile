<?php
namespace app\index\controller;

use think\Controller;
use think\Paginator;
use think\Request;
use think\Session;

class Index extends Controller
{


    /**
     * 首页
     * 陈绪
     */
    public function index(Request $request)
    {

        $goods_id = Session::get("goods_id");
        halt($goods_id);
        if($request->isPost()) {
            $user_id = Session::get("user");
            if (!empty($user_id)) {
                $user_car = db("user_car")->where("user_id", $user_id)->where("status", 1)->select();
                if ($user_car) {
                    return ajax_success("获取成功", $user_car);
                } else {
                    return ajax_error("获取失败");
                }

            } else {
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
     * 微信回调
     * 陈绪
     */
    public function saoma_callback(Request $request)
    {
        //扫码支付，接收微信请求;

        if($request->isPost()){
            $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
            $xml_data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            $val = json_decode(json_encode($xml_data), true);
            $goods_id = $request->only(["goods_id"])["goods_id"];
            if($val){
                file_put_contents(EXTEND_PATH . "lib/data/data.txt", $goods_id);
                exit();
            }

        }
        exit();
        if($request->isPost()) {


            exit();
            if($val["result_code"] == 'SUCCESS'){


                $bool = db("goods")->where("id",$goods_id)->update(["putaway_status"=>1,"goods_status"=>1]);

                return ajax_success("成功",$bool);
            }else{
                return ajax_error("失败");
            }
        }
    }


    /*
    微信通知地址   要放到前台不限制登录的地址
    */
    public function weixin_notify(){
        include_once(EXTEND_PATH . "lib/payment/wxpay/WxPayPubHelper.php");
        //使用通用通知接口
        $notify = new \Notify_pub();//实例化通知类 要不要加斜杠到线上测试

        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];


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

                //$obj_order = L::loadClass('order', 'index');//加载订单表 就是你那个goods数据表
                //$arr_order = $obj_order->get_one_main($int_order_id); //获取到支付的那条数据

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


}
