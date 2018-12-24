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
use think\Request;
use think\Session;
class Apppay extends Controller
{
    /**
     **************李火生*******************
     * 服务商订单异步处理(支付宝IOS对接)（只对ios有用）
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
    /**
     **************李火生*******************
     * @param Request $
     *
     * Notes:服务商订单提交支付（付款）
     **************************************
     * @param Request $request
     */
    public function index_aliPay(Request $request)
    {
        $con = array (
            //应用ID,您的APPID。
            'app_id' => "2018120762470526",
            //商户私钥，您的原始格式RSA私钥
            'merchant_private_key' => "MIIEpAIBAAKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQABAoIBAQCBQK730TFmpuTOtc669y6BOzUX1EWe+C/mYO28Dn7vqUGbU7UkuihtQIpcNCHhhGAXIHEH0zzrMH3b8XXdXjmo2ChBstr7elJlX2a7WYf9kHNTfRDCE+q5Xj7niSSYE6HOgvWDFMg9nyE3P0WRmTeEvjfVsv2SMoxxIBd8yD1Vxr3Gbg+gT8zWDrqXQ1Ap1gg5jNS14CFE3uKKwQ4n5JZWnIQ+jw3LZcpk9Eb/mrQ9kbnU7g0ikx8sYJpTiP7lAlb3dq1tdUmRV8+HfWYC/a8MbZtO6UyDWvms5Lb5g4we7FCmBAkG+zv62PxG9sQAvrQoSwKTOj/7LSeTgJsT97QNAoGBAPuQUNZEhVODVhCCISg84TGi0BozU64PqegJXFxbR++hQC2EsN6L2Mk2ftpd+J/9XRD0ffcBMea+H4N7ui4Y+OHoED/8d76dTX06PWfAYYJMu/o65c3IBSBiwgREuRo38a20CZ8hKr8LVpLXbtCB8WJ1kp5QeqqSPpwnjFncyBorAoGBAMu3Hokjze+FPpeFQ3tYVt9G/VSAhRMVAb5ZQClQH9plpVM9aMukp8jiaeSBg7d5RzNRGRU5ouKQ1AVs3jkgvVzUWRMKM+VkW4lzAhEkM766egpzngs9z4YXHcBW1bPJQap2TVLRcFmueDsVABXF5XZSgAwenBhtvmZ9X/UDCD+LAoGBALmXaOwLNUm9lVsshgXHlGQoN9t8jnnV+IXFkixY86NolY5/XHVzOwaHe+LifTCbnXOKzPvUF9qh3WIFf//OUJ9ps8NhIX6xUp/WvcKzfbzBm9Uqaqv8qzuPYJABm4YqS9TZBFgwAfdcCAzhf1G47Dq1fuvpd/YrWqGd07/gUIhtAoGAHDSkg7RzZQB75BrNdxyKGqwHk1WgFz5HWYWd/ppbbq+4LkhIZDnOCWBf7QWJqTOfihlmcavjQ59t27pxIlPIJDw6gQpemRpGGkfUN29dwsCq+Rt8/G14eEZnFiRvvk7VSrbKifb5qVEg0H1d36Xg2Xsew47Ragh33lTpnlDnKXUCgYBIuk9VU3DkITWsy+xiQbN4eQqbiFB7BA55xIjwPqK8K+0PVzRyObUEF6m9KSz2mEB1CHwr1fHj8qzJ/0CgKUeCONm5crLEGCGMbGUzMloGmVLSJz6+4xT8mwKOv/BcpTqkDLx+8HBaJppJnjWn0OmHLNa1JhAaVuef8eheH546kw==",
            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQAB",


            //应用ID,您的APPID。
//            'app_id' => "2018082761132725",
            //商户私钥，您的原始格式RSA私钥
//             'merchant_private_key' =>"MIIEpAIBAAKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQABAoIBAQCBQK730TFmpuTOtc669y6BOzUX1EWe+C/mYO28Dn7vqUGbU7UkuihtQIpcNCHhhGAXIHEH0zzrMH3b8XXdXjmo2ChBstr7elJlX2a7WYf9kHNTfRDCE+q5Xj7niSSYE6HOgvWDFMg9nyE3P0WRmTeEvjfVsv2SMoxxIBd8yD1Vxr3Gbg+gT8zWDrqXQ1Ap1gg5jNS14CFE3uKKwQ4n5JZWnIQ+jw3LZcpk9Eb/mrQ9kbnU7g0ikx8sYJpTiP7lAlb3dq1tdUmRV8+HfWYC/a8MbZtO6UyDWvms5Lb5g4we7FCmBAkG+zv62PxG9sQAvrQoSwKTOj/7LSeTgJsT97QNAoGBAPuQUNZEhVODVhCCISg84TGi0BozU64PqegJXFxbR++hQC2EsN6L2Mk2ftpd+J/9XRD0ffcBMea+H4N7ui4Y+OHoED/8d76dTX06PWfAYYJMu/o65c3IBSBiwgREuRo38a20CZ8hKr8LVpLXbtCB8WJ1kp5QeqqSPpwnjFncyBorAoGBAMu3Hokjze+FPpeFQ3tYVt9G/VSAhRMVAb5ZQClQH9plpVM9aMukp8jiaeSBg7d5RzNRGRU5ouKQ1AVs3jkgvVzUWRMKM+VkW4lzAhEkM766egpzngs9z4YXHcBW1bPJQap2TVLRcFmueDsVABXF5XZSgAwenBhtvmZ9X/UDCD+LAoGBALmXaOwLNUm9lVsshgXHlGQoN9t8jnnV+IXFkixY86NolY5/XHVzOwaHe+LifTCbnXOKzPvUF9qh3WIFf//OUJ9ps8NhIX6xUp/WvcKzfbzBm9Uqaqv8qzuPYJABm4YqS9TZBFgwAfdcCAzhf1G47Dq1fuvpd/YrWqGd07/gUIhtAoGAHDSkg7RzZQB75BrNdxyKGqwHk1WgFz5HWYWd/ppbbq+4LkhIZDnOCWBf7QWJqTOfihlmcavjQ59t27pxIlPIJDw6gQpemRpGGkfUN29dwsCq+Rt8/G14eEZnFiRvvk7VSrbKifb5qVEg0H1d36Xg2Xsew47Ragh33lTpnlDnKXUCgYBIuk9VU3DkITWsy+xiQbN4eQqbiFB7BA55xIjwPqK8K+0PVzRyObUEF6m9KSz2mEB1CHwr1fHj8qzJ/0CgKUeCONm5crLEGCGMbGUzMloGmVLSJz6+4xT8mwKOv/BcpTqkDLx+8HBaJppJnjWn0OmHLNa1JhAaVuef8eheH546kw==",
            //异步通知地址
//            'notify_url' => "https://automobile.siring.com.cn/index_pay_code",
            'notify_url' => "https://zlh188.cn/index_pay_code",
            //同步跳转
//            'return_url' => "https://automobile.siring.com.cn/index_pay_code",
            'return_url' => "https://zlh188.cn/index_pay_code",
            //编码格式
            'charset' => "UTF-8",
            //签名方式
            'sign_type'=>"RSA2",

            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
//             'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQAB",
        );

        if (!empty($_POST['WIDout_trade_no'])&& trim($_POST['WIDout_trade_no'])!=""){
            //商户订单号，商户网站订单系统中唯一订单号，必填
            $out_trade_no = $_POST['WIDout_trade_no'];
            //订单名称，必填
            $subject = $_POST['WIDsubject'];
            //付款金额，必填
            $total_amount = $_POST['WIDtotal_amount'];
            //商品描述，可空
            $body = $_POST['WIDbody'];
            //超时时间
            $timeout_express="1m";
            include('../extend/AliPay/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php');
            $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
            $payRequestBuilder->setBody($body);
            $payRequestBuilder->setSubject($subject);
            $payRequestBuilder->setOutTradeNo($out_trade_no);
            $payRequestBuilder->setTotalAmount($total_amount);
            $payRequestBuilder->setTimeExpress($timeout_express);
            include('../extend/AliPay/wappay/service/AlipayTradeService.php');

            $payResponse = new \AlipayTradeService($con);
            $result=$payResponse->wapPay($payRequestBuilder,$con['return_url'],$con['notify_url']);
            return ;
        }
    }
    /**
     * [回调修改数据]
     * 服务商订单（成功回调修改状态）
     * @param Request
     *
     */
    public function  index_pay_code(Request $request){
        if($request->isGet()){
            $data['status'] = 2;
            $pay_time = time();
            $data['pay_time']=$pay_time;
            if(!empty($_GET['out_trade_no'])){
                $bool = Db::name("order_service")->where("service_order_number",$_GET['out_trade_no'])->update($data);
                if($bool){
                   $this->redirect('index/My/my_index');
                }
            }
        }
    }



    /**
     **************李火生*******************
     * @param Request $
     *
     * Notes:配件商订单提交支付（付款）
     **************************************
     * @param Request $request
     */
    public function index_parts_aliPay(Request $request)
    {
        $cons = array (
            //应用ID,您的APPID。
            'app_id' => "2018120762470526",
            //商户私钥，您的原始格式RSA私钥
            'merchant_private_key' => "MIIEpAIBAAKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQABAoIBAQCBQK730TFmpuTOtc669y6BOzUX1EWe+C/mYO28Dn7vqUGbU7UkuihtQIpcNCHhhGAXIHEH0zzrMH3b8XXdXjmo2ChBstr7elJlX2a7WYf9kHNTfRDCE+q5Xj7niSSYE6HOgvWDFMg9nyE3P0WRmTeEvjfVsv2SMoxxIBd8yD1Vxr3Gbg+gT8zWDrqXQ1Ap1gg5jNS14CFE3uKKwQ4n5JZWnIQ+jw3LZcpk9Eb/mrQ9kbnU7g0ikx8sYJpTiP7lAlb3dq1tdUmRV8+HfWYC/a8MbZtO6UyDWvms5Lb5g4we7FCmBAkG+zv62PxG9sQAvrQoSwKTOj/7LSeTgJsT97QNAoGBAPuQUNZEhVODVhCCISg84TGi0BozU64PqegJXFxbR++hQC2EsN6L2Mk2ftpd+J/9XRD0ffcBMea+H4N7ui4Y+OHoED/8d76dTX06PWfAYYJMu/o65c3IBSBiwgREuRo38a20CZ8hKr8LVpLXbtCB8WJ1kp5QeqqSPpwnjFncyBorAoGBAMu3Hokjze+FPpeFQ3tYVt9G/VSAhRMVAb5ZQClQH9plpVM9aMukp8jiaeSBg7d5RzNRGRU5ouKQ1AVs3jkgvVzUWRMKM+VkW4lzAhEkM766egpzngs9z4YXHcBW1bPJQap2TVLRcFmueDsVABXF5XZSgAwenBhtvmZ9X/UDCD+LAoGBALmXaOwLNUm9lVsshgXHlGQoN9t8jnnV+IXFkixY86NolY5/XHVzOwaHe+LifTCbnXOKzPvUF9qh3WIFf//OUJ9ps8NhIX6xUp/WvcKzfbzBm9Uqaqv8qzuPYJABm4YqS9TZBFgwAfdcCAzhf1G47Dq1fuvpd/YrWqGd07/gUIhtAoGAHDSkg7RzZQB75BrNdxyKGqwHk1WgFz5HWYWd/ppbbq+4LkhIZDnOCWBf7QWJqTOfihlmcavjQ59t27pxIlPIJDw6gQpemRpGGkfUN29dwsCq+Rt8/G14eEZnFiRvvk7VSrbKifb5qVEg0H1d36Xg2Xsew47Ragh33lTpnlDnKXUCgYBIuk9VU3DkITWsy+xiQbN4eQqbiFB7BA55xIjwPqK8K+0PVzRyObUEF6m9KSz2mEB1CHwr1fHj8qzJ/0CgKUeCONm5crLEGCGMbGUzMloGmVLSJz6+4xT8mwKOv/BcpTqkDLx+8HBaJppJnjWn0OmHLNa1JhAaVuef8eheH546kw==",
            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQAB",



            //应用ID,您的APPID。
//            'app_id' => "2018082761132725",

            //商户私钥，您的原始格式RSA私钥
//            'merchant_private_key' =>"MIIEogIBAAKCAQEAvqOSFhdmTUUJWalNisHAsPaaejz24dyZcWJAHfBYK9T2b/W+T6VJfB84cuuxUqAkNXCS74fgVRdp1ty1AqprQnyIVnBiSMqxNbTFyKaZalW4WjpGq0zKnj/DrT+VF8gTYQuS263wkB06FbYTFp4bsriSGGT94N7CsOLDlR4cD0tsck2uuKXBIfqqmoGPEiz31vYOKht3cEiS4y/bLGN92nQx9/v5Ew8D3xdeqch0NaVWVaSgTQ0f3OzZJer7nnlSvLj2U3SwCbVJ/6+E15jQDl+HqHjVi71oAnh8Qj6lZKUyFwiWk4WTxxqYj94/v3IJ6bQxq3m+StQbIv+J/w6kAwIDAQABAoIBAGi5rqzYGejkveg1a1WIbnRIZEA2cWFOMDTrRlGsEKOzj9WdZ/iU0jOPaxEmjPjY5Es/Fljjicb0372a7Q0T1WxmwPbLMhLO1l6seeJqMukJQga+8Md1nfElEjeAMEUqMgsjsn9fpEFm7Nu0c/P+0zRw1ED2ack4VjeZutuV+NAlKivD0bmZDxu1Kf1KIFNLJnE7yrXOVPwytyimNLUxSrvzCBbrSEfD7eB3mei8abe7+yKjeSighE99+1vqNHY01NB74pkLt+0DMcvpyDSmpdNUwqUwMRtU109t3P1fQyu2vYgGPvqtbYZMX9qW9H0jB+An9KbLeDqyOBC9zTIYTFECgYEA3aHF3pTQ25IfEoOxXisGbQuFUERdWUMbZHq9WkbEGM1mbFjS5MRx/eCbwq3SXfDJWaHJWxtwxJ8ZHRqSBxnZ0TsgQY4dLKMaPHRp7W2h0LveztELSWUU93hUe04eqxQsE+PXBQfqsX0yL0G2U++vp6Gkozj2+JCSyRwb1k+1Z08CgYEA3DN0cnQMlTSVK/XY3mw5ZtNyBalhJZFeXRKyUdQLga8jGpS2UWxgmV4yYWdb91J1CDBnS8Ns6DRL8TgQEVBDyNwq90N/d/YpvriR8gtFwJPU+E/n67jwoyM2N+khRKDspN8amAJvuG/dlnc4wGvbb1QM9l9IV+w2PIu0UQgYCw0CgYBpisU4hrOjLEWBwpbvWhvYR9k3bSbrAAsGYXDUkciGayHwTKg5atdB5/kkzzPTqnuoJGRH75xW9dC2zFVn7kaM3dQxg6SfAhjXWzSqbT/Wr/Cdmmz+iZVfd7z7byspmxSWcDSP38JCvXTtjiRuekCy2kYVuGXb3rUX8jvLZe/j/wKBgErp1nv5ry4zrWw/P4CsIPsyhGAYt8woIIpZigD8us1r3+1zGnOpA1QqD8nDeC40B0y56uqDmdGFuTBfelfpjqYPAS8N75fIT3trH3vRCfHyWUoBJU20pWla2V88GO1YMioFHO8KJSgCJsjB1rTf/M9rMN3AnMQMwIbooHfQ4R0hAoGAc4KU+jSNXM4GPDhqtFVC9CIOtPPDIan/779rAyehLmVRtadHSY0PtD0XGXRFQXW9dEx8jcQj6MkEYUkeTvR4ulT1mK3DL/VT3ut27xaJTPhBCqisYLznbRFrVN0Wf1TSydJ3N5VbFgweZouiVZLd3CoOzy+BuA3eHkd44BBMjF4=",

            //异步通知地址
            'notify_url' => "http://www.zlh188.cn/index_parts_pay_code",
//            'notify_url' => "https://automobile.siring.com.cn/index_parts_pay_code",
//            'notify_url' => "localhost/automobile/public/index_parts_pay_code",
            //同步跳转
            'return_url' => "http://www.zlh188.cn/index_parts_pay_code",
//            'return_url' => "https://automobile.siring.com.cn/index_parts_pay_code",
//            'return_url' => "localhost/automobile/public/index_parts_pay_code",
            //编码格式
            'charset' => "UTF-8",
            //签名方式
            'sign_type'=>"RSA2",
            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
//            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvqOSFhdmTUUJWalNisHAsPaaejz24dyZcWJAHfBYK9T2b/W+T6VJfB84cuuxUqAkNXCS74fgVRdp1ty1AqprQnyIVnBiSMqxNbTFyKaZalW4WjpGq0zKnj/DrT+VF8gTYQuS263wkB06FbYTFp4bsriSGGT94N7CsOLDlR4cD0tsck2uuKXBIfqqmoGPEiz31vYOKht3cEiS4y/bLGN92nQx9/v5Ew8D3xdeqch0NaVWVaSgTQ0f3OzZJer7nnlSvLj2U3SwCbVJ/6+E15jQDl+HqHjVi71oAnh8Qj6lZKUyFwiWk4WTxxqYj94/v3IJ6bQxq3m+StQbIv+J/w6kAwIDAQAB",

        );
        if (!empty($_POST['WIDout_trade_no'])&& trim($_POST['WIDout_trade_no'])!=""){
            //商户订单号，商户网站订单系统中唯一订单号，必填
            $out_trade_no = $_POST['WIDout_trade_no'];
            //订单名称，必填
            $subject = $_POST['WIDsubject'];
            //付款金额，必填
            $total_amount = $_POST['WIDtotal_amount'];
            //商品描述，可空
            $body = $_POST['WIDbody'];
            //超时时间
            $timeout_express="1m";
            include('../extend/AliPay/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php');
            $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
            $payRequestBuilder->setBody($body);
            $payRequestBuilder->setSubject($subject);
            $payRequestBuilder->setOutTradeNo($out_trade_no);
            $payRequestBuilder->setTotalAmount($total_amount);
            $payRequestBuilder->setTimeExpress($timeout_express);
            include('../extend/AliPay/wappay/service/AlipayTradeService.php');

            $payResponse = new \AlipayTradeService($cons);
            $result=$payResponse->wapPay($payRequestBuilder,$cons['return_url'],$cons['notify_url']);
            return ;
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单支付（成功回调修改状态）
     **************************************
     * @param Request $request
     */
    public function  index_parts_pay_code(Request $request){
        if($request->isGet()){
            $data['status'] = 2;
            $pay_time = time();
            $data['pay_time']=$pay_time;
            $data["pay_type_content"] ="支付宝";
            if(!empty($_GET['out_trade_no'])){
                $bool = Db::name("order_parts")->where("parts_order_number",$_GET['out_trade_no'])->update($data);
                if($bool){
                    $parts =Db::name("order_parts")->field("parts_goods_name")->where("parts_order_number",$_GET['out_trade_no'])->select();
                   foreach($parts as $ks=>$vs){
                       $titles[] = $vs["parts_goods_name"];
                   }
                   $title =implode("",$titles);
                    $money =Db::name("order_parts")->where("parts_order_number",$_GET['out_trade_no'])->sum("order_real_pay");
                    $user_id = Session::get("user");
                    $datas["user_id"] =$user_id; //用户ID
                    $datas["wallet_operation"] = -$money; //消费金额
                    $datas["wallet_type"] = -1; //消费操作(1入，-1出)
                    $datas["operation_time"] = date("Y-m-d H:i:s"); //操作时间
                    $datas["wallet_remarks"] = "订单号：".$_GET['out_trade_no']."，支付宝消费".$money; //消费备注
                    $datas["wallet_img"] = "index/image/alipay.png"; //图标
                    $datas["title"] = $title; //标题（消费内容）
                    Db::name("wallet")->insert($datas);
                   $this->redirect('index/OrderParts/order_wait_deliver');
                }
            }
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:
     **************************************
     */
    public function parts_notifyurl()
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
            $data['status'] = 2;//状态值
            $data['trade_no'] = $trade_no;//支付宝交易号

            $condition['parts_order_number'] = $out_trade_no;
            $select_data = Db::name('order_parts')->where($condition)->select();
            foreach ($select_data as $key => $val) {
                $result = Db::name('order_parts')->where($condition)->update($data);//修改订单状态,支付宝单号到数据库
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


    /**
     **************李火生*******************
     * @param Request $
     *
     * Notes:充值订单提交支付（付款）
     **************************************
     * @param Request $request
     */
    public function recharge_aliPay(Request $request)
    {
        $confs = array (
            //应用ID,您的APPID。
            'app_id' => "2018120762470526",
            //商户私钥，您的原始格式RSA私钥
            'merchant_private_key' => "MIIEpAIBAAKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQABAoIBAQCBQK730TFmpuTOtc669y6BOzUX1EWe+C/mYO28Dn7vqUGbU7UkuihtQIpcNCHhhGAXIHEH0zzrMH3b8XXdXjmo2ChBstr7elJlX2a7WYf9kHNTfRDCE+q5Xj7niSSYE6HOgvWDFMg9nyE3P0WRmTeEvjfVsv2SMoxxIBd8yD1Vxr3Gbg+gT8zWDrqXQ1Ap1gg5jNS14CFE3uKKwQ4n5JZWnIQ+jw3LZcpk9Eb/mrQ9kbnU7g0ikx8sYJpTiP7lAlb3dq1tdUmRV8+HfWYC/a8MbZtO6UyDWvms5Lb5g4we7FCmBAkG+zv62PxG9sQAvrQoSwKTOj/7LSeTgJsT97QNAoGBAPuQUNZEhVODVhCCISg84TGi0BozU64PqegJXFxbR++hQC2EsN6L2Mk2ftpd+J/9XRD0ffcBMea+H4N7ui4Y+OHoED/8d76dTX06PWfAYYJMu/o65c3IBSBiwgREuRo38a20CZ8hKr8LVpLXbtCB8WJ1kp5QeqqSPpwnjFncyBorAoGBAMu3Hokjze+FPpeFQ3tYVt9G/VSAhRMVAb5ZQClQH9plpVM9aMukp8jiaeSBg7d5RzNRGRU5ouKQ1AVs3jkgvVzUWRMKM+VkW4lzAhEkM766egpzngs9z4YXHcBW1bPJQap2TVLRcFmueDsVABXF5XZSgAwenBhtvmZ9X/UDCD+LAoGBALmXaOwLNUm9lVsshgXHlGQoN9t8jnnV+IXFkixY86NolY5/XHVzOwaHe+LifTCbnXOKzPvUF9qh3WIFf//OUJ9ps8NhIX6xUp/WvcKzfbzBm9Uqaqv8qzuPYJABm4YqS9TZBFgwAfdcCAzhf1G47Dq1fuvpd/YrWqGd07/gUIhtAoGAHDSkg7RzZQB75BrNdxyKGqwHk1WgFz5HWYWd/ppbbq+4LkhIZDnOCWBf7QWJqTOfihlmcavjQ59t27pxIlPIJDw6gQpemRpGGkfUN29dwsCq+Rt8/G14eEZnFiRvvk7VSrbKifb5qVEg0H1d36Xg2Xsew47Ragh33lTpnlDnKXUCgYBIuk9VU3DkITWsy+xiQbN4eQqbiFB7BA55xIjwPqK8K+0PVzRyObUEF6m9KSz2mEB1CHwr1fHj8qzJ/0CgKUeCONm5crLEGCGMbGUzMloGmVLSJz6+4xT8mwKOv/BcpTqkDLx+8HBaJppJnjWn0OmHLNa1JhAaVuef8eheH546kw==",
            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQAB",


            //应用ID,您的APPID。
//            'app_id' => "2018082761132725",

            //商户私钥，您的原始格式RSA私钥
//            'merchant_private_key' =>"MIIEogIBAAKCAQEAvqOSFhdmTUUJWalNisHAsPaaejz24dyZcWJAHfBYK9T2b/W+T6VJfB84cuuxUqAkNXCS74fgVRdp1ty1AqprQnyIVnBiSMqxNbTFyKaZalW4WjpGq0zKnj/DrT+VF8gTYQuS263wkB06FbYTFp4bsriSGGT94N7CsOLDlR4cD0tsck2uuKXBIfqqmoGPEiz31vYOKht3cEiS4y/bLGN92nQx9/v5Ew8D3xdeqch0NaVWVaSgTQ0f3OzZJer7nnlSvLj2U3SwCbVJ/6+E15jQDl+HqHjVi71oAnh8Qj6lZKUyFwiWk4WTxxqYj94/v3IJ6bQxq3m+StQbIv+J/w6kAwIDAQABAoIBAGi5rqzYGejkveg1a1WIbnRIZEA2cWFOMDTrRlGsEKOzj9WdZ/iU0jOPaxEmjPjY5Es/Fljjicb0372a7Q0T1WxmwPbLMhLO1l6seeJqMukJQga+8Md1nfElEjeAMEUqMgsjsn9fpEFm7Nu0c/P+0zRw1ED2ack4VjeZutuV+NAlKivD0bmZDxu1Kf1KIFNLJnE7yrXOVPwytyimNLUxSrvzCBbrSEfD7eB3mei8abe7+yKjeSighE99+1vqNHY01NB74pkLt+0DMcvpyDSmpdNUwqUwMRtU109t3P1fQyu2vYgGPvqtbYZMX9qW9H0jB+An9KbLeDqyOBC9zTIYTFECgYEA3aHF3pTQ25IfEoOxXisGbQuFUERdWUMbZHq9WkbEGM1mbFjS5MRx/eCbwq3SXfDJWaHJWxtwxJ8ZHRqSBxnZ0TsgQY4dLKMaPHRp7W2h0LveztELSWUU93hUe04eqxQsE+PXBQfqsX0yL0G2U++vp6Gkozj2+JCSyRwb1k+1Z08CgYEA3DN0cnQMlTSVK/XY3mw5ZtNyBalhJZFeXRKyUdQLga8jGpS2UWxgmV4yYWdb91J1CDBnS8Ns6DRL8TgQEVBDyNwq90N/d/YpvriR8gtFwJPU+E/n67jwoyM2N+khRKDspN8amAJvuG/dlnc4wGvbb1QM9l9IV+w2PIu0UQgYCw0CgYBpisU4hrOjLEWBwpbvWhvYR9k3bSbrAAsGYXDUkciGayHwTKg5atdB5/kkzzPTqnuoJGRH75xW9dC2zFVn7kaM3dQxg6SfAhjXWzSqbT/Wr/Cdmmz+iZVfd7z7byspmxSWcDSP38JCvXTtjiRuekCy2kYVuGXb3rUX8jvLZe/j/wKBgErp1nv5ry4zrWw/P4CsIPsyhGAYt8woIIpZigD8us1r3+1zGnOpA1QqD8nDeC40B0y56uqDmdGFuTBfelfpjqYPAS8N75fIT3trH3vRCfHyWUoBJU20pWla2V88GO1YMioFHO8KJSgCJsjB1rTf/M9rMN3AnMQMwIbooHfQ4R0hAoGAc4KU+jSNXM4GPDhqtFVC9CIOtPPDIan/779rAyehLmVRtadHSY0PtD0XGXRFQXW9dEx8jcQj6MkEYUkeTvR4ulT1mK3DL/VT3ut27xaJTPhBCqisYLznbRFrVN0Wf1TSydJ3N5VbFgweZouiVZLd3CoOzy+BuA3eHkd44BBMjF4=",
            //异步通知地址
            'notify_url' => "http://www.zlh188.cn/recharge_pay_code",
//            'notify_url' => "https://automobile.siring.com.cn/recharge_pay_code",
//            'notify_url' => "localhost/automobile/public/recharge_pay_code",
            //同步跳转
            'return_url' => "http://www.zlh188.cn/recharge_pay_code",
//            'return_url' => "https://automobile.siring.com.cn/recharge_pay_code",
//            'return_url' => "localhost/automobile/public/recharge_pay_code",
            //编码格式
            'charset' => "UTF-8",
            //签名方式
            'sign_type'=>"RSA2",
            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
//            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvqOSFhdmTUUJWalNisHAsPaaejz24dyZcWJAHfBYK9T2b/W+T6VJfB84cuuxUqAkNXCS74fgVRdp1ty1AqprQnyIVnBiSMqxNbTFyKaZalW4WjpGq0zKnj/DrT+VF8gTYQuS263wkB06FbYTFp4bsriSGGT94N7CsOLDlR4cD0tsck2uuKXBIfqqmoGPEiz31vYOKht3cEiS4y/bLGN92nQx9/v5Ew8D3xdeqch0NaVWVaSgTQ0f3OzZJer7nnlSvLj2U3SwCbVJ/6+E15jQDl+HqHjVi71oAnh8Qj6lZKUyFwiWk4WTxxqYj94/v3IJ6bQxq3m+StQbIv+J/w6kAwIDAQAB",

        );
        if (!empty($_POST['WIDout_trade_no'])&& trim($_POST['WIDout_trade_no'])!=""){
            //商户订单号，商户网站订单系统中唯一订单号，必填
            $out_trade_no = $_POST['WIDout_trade_no'];
            //订单名称，必填
            $subject = $_POST['WIDsubject'];
            //付款金额，必填
            $total_amount = $_POST['WIDtotal_amount'];
            //商品描述，可空
            $body = $_POST['WIDbody'];
            //超时时间
            $timeout_express="1m";
            include('../extend/AliPay/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php');
            $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
            $payRequestBuilder->setBody($body);
            $payRequestBuilder->setSubject($subject);
            $payRequestBuilder->setOutTradeNo($out_trade_no);
            $payRequestBuilder->setTotalAmount($total_amount);
            $payRequestBuilder->setTimeExpress($timeout_express);
            include('../extend/AliPay/wappay/service/AlipayTradeService.php');

            $payResponse = new \AlipayTradeService($confs);
            $result=$payResponse->wapPay($payRequestBuilder,$confs['return_url'],$confs['notify_url']);
            return ;
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:充值订单支付（成功回调修改状态）
     **************************************
     * @param Request $request
     */
    public function  recharge_pay_code(Request $request){
        if($request->isGet()){
            $user_id = Session::get("user");
            $data['status'] = 1;
            $pay_time = time();
            $pay_times = date("Y-m-d H:i:s");
            $data['pay_time']=$pay_time;
            $data["pay_type_name"] ="支付宝";
            if(!empty($_GET['out_trade_no'])){
                $bool = Db::name("recharge_record")->where("recharge_order_number",$_GET['out_trade_no'])->update($data);
                if($bool){
                    $recharge_record_data = Db::name("recharge_record")->where("recharge_order_number",$_GET['out_trade_no'])->find();
                    $datas["operation_time"] =$pay_times; //操作时间
                    $datas["user_id"] =$user_id; //用户id
                    $datas["operation_type"] =1; //操作类型（-1,1）
                    $datas["pay_type_content"] =$recharge_record_data["pay_type_name"]; //支付方式
                    $datas["money_status"] =1; //到款状态
                    $datas["img_url"] ="index/image/alipay.png"; //描述
                    $list =Db::name("recharge_setting")->field("recharge_full,send_money")->select();
                    $lists =null;
                    foreach($list as $k=>$v){
                        if($v["recharge_full"] ==$recharge_record_data["recharge_money"]){
                            $lists =$v["send_money"];
                        }
                    }
                    if(!empty($lists)){
                        $datas["operation_amount"]=$recharge_record_data["recharge_money"]+$lists; //操作金额
                        $datas["recharge_describe"] ="充值".$recharge_record_data["recharge_money"]."元,送了".$lists; //描述
                        Db::name("recharge_reflect")->insert($datas);//插到记录
                        $user_wallet =Db::name("user")->field("user_wallet")->where("id",$user_id)->find();
                        Db::name("user")->where("id",$user_id)->update(["user_wallet"=>$user_wallet["user_wallet"]+$recharge_record_data["recharge_money"]+ $lists]);
                    }else{
                        $datas["operation_amount"] =$recharge_record_data["recharge_money"]; //操作金额
                        $datas["recharge_describe"] ="充值".$recharge_record_data["recharge_money"]."元"; //描述
                        Db::name("recharge_reflect")->insert($datas);//插到记录
                        $user_wallet =Db::name("user")->field("user_wallet")->where("id",$user_id)->find();
                        Db::name("user")->where("id",$user_id)->update(["user_wallet"=>$user_wallet["user_wallet"]+$recharge_record_data["recharge_money"]]);
                    }
                    $this->redirect('index/wallet/index');
                }
            }
        }
    }






}