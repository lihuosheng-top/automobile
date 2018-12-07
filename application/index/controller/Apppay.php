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

    public function index_aliPay(Request $request)
    {
        $config = array (
            //应用ID,您的APPID。
//            'app_id' => "2016112603335050",
            'app_id' => "2018101361694139",
            //商户私钥，您的原始格式RSA私钥
//            'merchant_private_key' => "MIIEowIBAAKCAQEAz+SfWrndsOSD3AY3v5YtA9n+BoBcckMYfjgpIrT5Bu2YF2GR5oFCBJASSQeRRyDHPWL3i91lbyZeiBsE2l+rJcMTP+EfH6MpxMerwqfvOPw4p4OHHAnbI52xjdNZStBdIT7oEwEUsghuejCpWelL/b3CPFpW/1OpEVRnssw9gc0f1mius2eOXZ0+5JaJRZ/zJWxgyMHctF6NXcSG2oVOl0WyiNK/F4CuqdIcq1y8ZDiVvmRbyfzcEmbgob7MpwVFWw1Fge3z4fSnG7bicOJSXkPbWNhZmGe/yXCEXbA/8Kldp/nMkwnMGJ5A/3yFZTEUnmY60qnXA5T3R1KOnpXklwIDAQABAoIBAQCDq2VSbQ4AD3uES1vbuB3ipprBO2NR6zUEHEXReZWP0cPWazGhMJTDlww9vNFCn3wRYTEwIJUyBLcytQop1RXs4NS8TLUNsKWvwFcE/qABE54+WoukMonc0O+3x/hx7e5ONC2Ae9rDt5thQJjCHYTHvPvchcs8A5y9IRxcngcGweL6m6KUd4yT4yr5pPCXM8Q4B5cG/BM+MtLeqPJ1S7zheMKt4pN52M9pU9+n1V3nx1FgViv7ycOh8E+9L33S/Ri9HuLyIeV9zZ44g53ociUlSoQBnUIiDHWriHROWP0yxPdp0Et4oUPFcsDR1FVa8rFSmhZRauA6M7Um8SRXKVtBAoGBAPrhPqij8HfnOCJAGMcbwJnpQGZAypYBawEOSib3uIKyqEQmDlvzTjJgR2YbFUfGvgeAn0mX/Q4B/Vgffb1dqCJbU4McSE3GHJHCdBO6UqvUD4B8Qy6aJJomPGwgZAi+DAk9PtNDo2tC6DTZbd5UJMTqdpMq0776pjR6E3+7F2wZAoGBANQiyZTfw9qqf9xyQ4YKwu6v0165e+mnlycOTRkrBSESJUNSCH4aYHZnE4B9J1MU5fxxrZuk5qt6iu0N5AkUQY6xuLkKdjX8WJbHWgHHjvMxXsEqx1LQlQ2PSCCvF5jxB0xhzTjBa3uCzfabs3o+6MKh1QF1DuYMBE1B/rku8uwvAoGAdnuAIxbhj08EpLBOw2Ho8QdGocQBqRxcU7BS9tpRKnCDpUOvzl820/XCYodx4mcLAfINyCzelwn7gu3EbXVY3XjyFN57izd/8Jq8RUDeoEXTWGPXOqATnzVlnc8iTzqp5oclL5MnD5YWojb5e2GTx+fPPiuguvYXHnt00AMkyakCgYAkFOKqksDSUYu76Cd6BhyP0pImG3BrFplMCE+uxzVxIY/6+ln9cOkVWoTjpuXoaLaRkJhRz+N4KTi2B1XRAYQBDFN6DcB7gDdlNfUmNlYnIS+XtXn/qQChNMy02nMuDVkLcdshGyz37hCwMF1/nnGioToErG9jS4nzxhTYVJb2+wKBgGGU5XtXTZAeBtueAgwwPkdOe1pXHXjkeytG2cGeNJrBkMmj7B7eNt+3EkHw4yPgvj/e4OYNm4ojRH05FefZmb6dtLDUH0p1k9LeqEbGGbHn7cl7jDjTqaRznlODyaT3pJlRldZIaJ95VEwZtpMQCnItUAu5yGH3Vrgo2Y8eNpAn",
            'merchant_private_key' =>"MIIEpAIBAAKCAQEAwRQWx+FYZqIHISoUFYPn23paT5J3H3Bv4g3qhVSPejB6G/3i
2vFmuP3cXqW1+rznom0qmhfXB/59iplBAqlOtJGIAQnEyou/5fx8RYyfE8YELNwD
nznipF0uO+LJkasXdreSGfdEZn13WVBUoQvbNqp6MeD2OR7bm271GfWGclIak8Mg
9dbsR9O1uOX57PkYzftW3AXJGW+/4pecmVcTTZ/j8Wj3COwkr8C3qJcfuodZdYCr
YhcJ16WFGFqUxzWORFu+Jha8dbFeZQoNMvg3E94QifiuMFAzvAbiPDUK+qf3Izu2
hbKcdoTRUpCUqyReE0xdDGLb32zUnOlPo4CSawIDAQABAoIBAC6atcD7cMYdOBFW
QdDLrQx6JRBt7xkEn8z1xPT360X4K5FUIAzvFfdzN10VLK7gKB05sn3NYVcJo63v
vhRxX5EQS77MZ9boEqLl5e03uzIfquRVGv61DzLTVxSXckYaASjjuH1SBQ2kHUnO
rQ7OeNRiI+qkPWqeOy3yg3EHpbM0F+j86g5bWKwPXP9BwIlX7YXtsN9ckn2njdgt
2vyj/5yk3iuJqMMLMy61Xs/522wxDtBVM0hX9mJqkn5UmGYeyNmbWmuC/H69Qvea
p7g7r4Z81MShWOWpzVgggbNKRH0jxk/gc8DzwATxHWGpTL7JvdANwOe2IBexe6mw
e+2DOxkCgYEA/sAqbPKGft3Nr6Qb5dhHi5lvO40TB8vfkRCnyxrvN4Sbi6SbgdDE
K/wdUNf/x1dpQUlRxUuvgpxoO05MUKlWdx1tcWNM0QefWaJuIoaQg3CDD26bAHnO
gF8Vi6vNqRRFZD3yoN7JPJ17qS1eRtdKj8196wSADAuw6XUNQ6nykC0CgYEAwgZ+
vqxW/0xHSgYdibKmJQmDVrMMrskvWfwQ8UDMBxSqQbv3g0mJkWP1x/0RT4Sf+stD
NvpWyH3ZgUAPrarSsdb3jl6HLjjQ2qdtd05f+Fk/V+OlT/8f/LS0uBpM4M0N5YxY
3no0sN2ACEz2pYnSUceV4uJO80VcvXv6Dm9Is/cCgYEAsfbe7IM0r/YVVclIb/X6
bSyAB1MTY1PqV7YR8hJb4SXzX8dbl4GXOb8DX9G1brXC4wfsZu7rqkvHiP6203ao
38egCCE9rZ0slfqiA96LoDb+Z8513Aw71XQXYh5gKujtXQzlrbpVTNIbbGE0Rjrq
bLE6cO8Q4Nld+ol5/bYArPUCgYB8xbKCKc/0c3gwyUf7ghBIJxr4CUKxsB0sT+YR
TyslrotipF8iZYr7HX40MrEMIuzCWi6KT2i5m/zgmnK6quy9gtFGE9H4zn647gx+
Ob+LtNEAUxu/GwDWANtqvTcNJbC3Go8X2xzBVF/+dofyonPHNYNVGVXOKZ/fFrgS
ykvFtwKBgQDxrBF9PNt5RfNuqHsMBx6j1o2tNR6VtErgJ/ribEFef1tSCce5ijoN
OPRn+/eIAa8Dv6slF76rF4LmZlngJjKQcV+ztL78ouVx5Pdbt8PeznZV82YZzxSt
ihjWSdZpFDsidZkpe+RqwTUaVYpP0t69GbRWjpO/IoCX6qZsDxXVGw==",
            //异步通知地址
//            'notify_url' => "https://vip.gagaliang.com/Alipay_pay_code",
            'notify_url' => "https://automobile.siring.com.cn/index_pay_code",
            //同步跳转
//            'return_url' => "https://vip.gagaliang.com/Alipay_pay_code",
            'return_url' => "https://automobile.siring.com.cn/index_pay_code",

            //编码格式
            'charset' => "UTF-8",

            //签名方式
            'sign_type'=>"RSA2",

            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
//            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAz+SfWrndsOSD3AY3v5YtA9n+BoBcckMYfjgpIrT5Bu2YF2GR5oFCBJASSQeRRyDHPWL3i91lbyZeiBsE2l+rJcMTP+EfH6MpxMerwqfvOPw4p4OHHAnbI52xjdNZStBdIT7oEwEUsghuejCpWelL/b3CPFpW/1OpEVRnssw9gc0f1mius2eOXZ0+5JaJRZ/zJWxgyMHctF6NXcSG2oVOl0WyiNK/F4CuqdIcq1y8ZDiVvmRbyfzcEmbgob7MpwVFWw1Fge3z4fSnG7bicOJSXkPbWNhZmGe/yXCEXbA/8Kldp/nMkwnMGJ5A/3yFZTEUnmY60qnXA5T3R1KOnpXklwIDAQAB",
            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwRQWx+FYZqIHISoUFYPn
23paT5J3H3Bv4g3qhVSPejB6G/3i2vFmuP3cXqW1+rznom0qmhfXB/59iplBAqlO
tJGIAQnEyou/5fx8RYyfE8YELNwDnznipF0uO+LJkasXdreSGfdEZn13WVBUoQvb
Nqp6MeD2OR7bm271GfWGclIak8Mg9dbsR9O1uOX57PkYzftW3AXJGW+/4pecmVcT
TZ/j8Wj3COwkr8C3qJcfuodZdYCrYhcJ16WFGFqUxzWORFu+Jha8dbFeZQoNMvg3
E94QifiuMFAzvAbiPDUK+qf3Izu2hbKcdoTRUpCUqyReE0xdDGLb32zUnOlPo4CS
awIDAQAB",
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
     * @param Request
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


}