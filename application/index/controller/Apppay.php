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
     * TODO:配件商订单异步处理(支付宝IOS对接)（只对ios有用）
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
        if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS' || $trade_status =="Success") {
            $data['status'] = 2;
            $data["trade_no"] =$trade_no;
            $data['pay_type_content'] = "支付宝";//支付宝交易号
            $condition['parts_order_number'] = $out_trade_no;
            $select_data = Db::name('order_parts')->where($condition)->select();
            foreach ($select_data as $key => $val) {
                $result = Db::name('order_parts')
                    ->where("parts_order_number", $val["parts_order_number"])
                    ->update($data);//修改订单状态,支付宝单号到数据库
            }
            if ($result >0) {
                $parts =Db::name("order_parts")
                    ->field("parts_goods_name")
                    ->where("parts_order_number",$out_trade_no)
                    ->select();
//                foreach($parts as $ks=>$vs){
//                    $titles[] = $vs["parts_goods_name"];
//                }
//                $title =implode("",$titles);
                $title ="消费测试";
                $money =Db::name("order_parts")->where("parts_order_number",$out_trade_no)->sum("order_real_pay");
                $datas["user_id"] =$parts[0]["user_id"]; //用户ID
                $datas["wallet_operation"] = -$money; //消费金额
                $datas["wallet_type"] = -1; //消费操作(1入，-1出)
                $datas["operation_time"] = date("Y-m-d H:i:s"); //操作时间
                $datas["wallet_remarks"] = "订单号：".$_GET['out_trade_no']."，支付宝消费".$money; //消费备注
                $datas["wallet_img"] = "index/image/alipay.png"; //图标
                $datas["title"] = $title; //标题（消费内容）
                Db::name("wallet")->insert($datas);
                return ajax_success('支付成功', ['status' => 1]);
            } else {
                return ajax_error('验证失败了', ['status' => 0]);
            }
        }else {
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
//            'merchant_private_key' => "MIIEpgIBAAKCAQEA86bQJlbz6FgjGn6/aS0UCGm1YWWBQvD5JhSUrUW4W3hAInKkLgga8rdNjbHsuMDzoBUEppeEU7CVnjTYusn4BoP4JS1q96n569IlFRltiPrykbuzcGf0HpDY1/gujy6klgF+rIZa6SL0D3LN5Yrefb5Vh16YTsiEhg3EGdGYxYGz0lNAk4dM+whleSQyajtYiK14rdKNoXX4Djs5dzn/xaomV7HGZHLyRE/GKx1jWVH5WHYJmL7CeOWdc1pSDqn0QseDds+o14wu7kRlOkiSkck1U0XiG753FxEQJjmcou4g0FbJxkViRETuFsXxhQBwCvlQfXDgwx9eupP42SFX8wIDAQABAoIBAQDORATowCniI2MsVerDJkdUS/EiT+Ua/5dcloW7Hk/RF77rqrO0yGXP3kAIWXz2d1qHxeNXCOQcWVyAP2rOzpZrJaOYDZgFrF8INbHw58Lj+NYLa6Sf31E54xO/2bnIuwrYdtF/SoqB3XZR8bW+wk5SkyQi4oD8CSKvvc2qsdasM9dgCRycfrCWFEKQv8SDj08hbN9yZb1rRlkbr8g+zgkjUtBeC0olkoORLBmmcuAYp6yyQqWDHsnCYXTAMLwzuKGXWQ6mnM8Rtl1bjXjI/ljHWWMTp17Toqr+th0uD/TTXOcgibb9jrctm40cZS6ryOaVM9EVE0gNsupzQLBnFv2BAoGBAPvYrqZiLEHqnBzDBSyzO25ZlnA6TB+0uqJkAfcEJLTWIjuMy8jVXVI1+vzAykjaP4fCl/hQRcqIACRIiavXE1mAx94MAqWOhNT+qn4OhghxRnBDrrM8o+IxurgDPddbgzfcqYr+rOrdL4NQnS88wbhJ4IcBKitEJZ/AzDwvTCuzAoGBAPeriBx+qkAEhz8TsgnbXD3YV9g0OzuVdcPCaDt76KjvMJAM7wYwJ1V24r5D0Rds7OyQLAVORD4hndxFwnovuMyCFHAOOpGuc4avTHA6j8emQJ5l/T8uwaBWWPq/y9IJJn0MBG/raF1b0lizVPsknuwIWOscDNd/gZ/BW3PjrwLBAoGBAJLy+/VZkzljC3Q81ugeLK9W1tDlM0XMzpmygPMBF+nsiEz2+nFVDf7KQIj6dQ3NSwSMJuBc9lwWvv5s1OfPtxqiafzGRUUUw60Ea7biApMqhU88LtOmrnltSuXT/mGKpCYUO1k/AOaJ6Q9WGmFVsjM16CzeIRzdxpZQiNsVPfzBAoGBAI70j4gDBLoW4wnhkG5oE4IPEgVVgKsPxf3MhOVwzAZWqa08CeuEd++4w4AXHOVslgSl4wLc/j7uoHO4QFzOlthUbP+Q9vWHwF8JaBGPe9Y9fCbxr498qX6m2I1Dj54YcgLToOwI8SCSE4PnXzHnHWQYHVr3weHRUzynGuZ1kVsBAoGBAO0LOlejhlgZ3MzqorftTay0iBg/jsjL5FKfEBrXiHBqmo3aZrEKdtO9B38fvTJumPdy6jG6CdyonW3FK/O6PzFW3Gf3ZZoR2fV/2qPUKhI3yPJx8ilAhhMLV+pdFhtNFdbANlNbvRfzE9DFEP3cvhEahOGMnwLiTH/oUVPks22L",
            'merchant_private_key' => "MIIEpQIBAAKCAQEAvPQu30qb4OsdxvU7e+5QlbK6xlku6q/Gm940nrGciz46+ICyAuifLs0OF+qH4HH9hIf5rxslsHTOHREMk/6SyUmkpE/ChYm4vp1tHcIknqWQsDaYHKFs9ML0OW4BI3xZxSs3YKOkdNTgmKFUZLAK1z59guo14MbpYskDmk8WaNHkKJgipTnwYMaXac8hHCDUOH0asMCbb2ZNwlCM9M4rGNaL62USeKhq26HiX7uct3Xd5I4a+l9Bk0COx05DGRLdX7Qo5TCatTK+g89BFCk2REQHYlSoIfR1rzJkSz2wyw/wz4GcfRxRO8t6khVJ5TclRL1BqH0P5aCwmqru4ZwqbQIDAQABAoIBACNadKKmrP+qPgoJvDV143H+3N9btGGIvdpP+vxuMuICpLn2KlhM+euhxi9HXGDsMwjnABp5M9YOJLpfhED0crZipwFvQvAFUqGnhtD4kC12wNmXfWJt+ZraFyPfpyrii1FOwq+8LnsFkXg9RzMHlR+su4MRGz+RN/2Zqqjmf0ThHYWl9bMzkl77s7TNK98zdsyLYf12qa/PZEPl7Zf2Sg5ksVZl+ilkHeR02OKt+zj04SIhw/IgZO/0jfaq9xHUTWXfJH45WWIjOKwsS5aV74iL9iHIdoFfGsPRsA0am9Hrj0lEH/WWHyQoNSzZpNCcvkGUh2q4dBQoOT1fw5D4GykCgYEA++njkSiclymIQu6JdSEGPHISH6R0IcBe2mvTWBZ6j+PXiStk/I33QWo4fxT4Qx0lRcuD2bc44rWYgCbZXm90GdymeSkZ94lpl0/4XEfdrT6deWrS7wEnV+U+Nu/3M3RmoxNAmq1w/IYc5MPkPhrkxoAZf7eqQFtVuAeNfI+s4eMCgYEAwATX/XuWAi/PP00dUoiq0I/MbPrU3W0zYA/V55+8/tyrLBs/1XXQXirp7GzmjTFBz5xh8FflIISv6KVdQ+5R/gx/66qYq7MklGeNvnLS8bvL6T+loF7w2xY7FHQ7J5q8Jtgd6ENe525WFBefHohxCsASbedOWNwVqZUj23GiM28CgYEA55tMLINoJNp8ev0Qx+2K7Dv8SbdLRlS1YJ/N+akBGhXibizFdiWX9D6SntRKxHmhcSABo0N/O4C2KuyM4PQbjEN/ktmlFB70Q7e0ojM4rjVpVt5N/IvU/ky3/I53eolEbB4hQZTA0osDOZiY2jijqZTQ3Mmh7/WJQ989yA3YZv0CgYEAvzCTdvrk5AyCiH7Z4WHe4ocrfp9J76dZbh6WV2g/oGxLq0D+kCYccIa+IABWluMiqfsFsW9y3qv/EuAEOy1CSnhbrmVQWjWnCHULN0PRMYxRfk83NLUTkLFT5gvNEqQJD4xESw05o6nC34KdlqM4GwIf13pmEighEZdD+q3sWNUCgYEAhTz2NfzdDfT0gZGxfpUhhK0nkqdNI8HX2CfUVMPQjmnbgZ9Hfav+RMMf+02ftUzmZX0f/VyytSu5i0bkIUClUqxvjaKXRwwoiv3Tb2JHTururXBViUOCQeCJuXarX89EoReIvUXBBw2G6HcULGfuuNZM81fd/cav4EZfMO2+zPM=",
            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
//            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA86bQJlbz6FgjGn6/aS0UCGm1YWWBQvD5JhSUrUW4W3hAInKkLgga8rdNjbHsuMDzoBUEppeEU7CVnjTYusn4BoP4JS1q96n569IlFRltiPrykbuzcGf0HpDY1/gujy6klgF+rIZa6SL0D3LN5Yrefb5Vh16YTsiEhg3EGdGYxYGz0lNAk4dM+whleSQyajtYiK14rdKNoXX4Djs5dzn/xaomV7HGZHLyRE/GKx1jWVH5WHYJmL7CeOWdc1pSDqn0QseDds+o14wu7kRlOkiSkck1U0XiG753FxEQJjmcou4g0FbJxkViRETuFsXxhQBwCvlQfXDgwx9eupP42SFX8wIDAQAB",
            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyUgwfM85daNJ3tHlmsFET1Mcc6kaXjLD9AdjOy8pknzHVvDp66fMp2TZfnJjwGUlYDr9w/mY9CDbzwQgwllVUCSNxzVLH57FfRuzVStoHN22BJBMTvkMPpSbEvV3MxEzXzrl2kyV5sqEEyDiY28wpX1aFb5ug/G18R6216DMqGlTdYwHSOBd+S+2r2B5ljk786dtRf0inimEAGP5CdX2HxI8svpUVq2OeabSZ/kvf39yW9oLNLz0WIAwHQiWSg3DCb+MBP4I/W+5pX6N8p+ijqtPDJTF7/XBDQnftKDfVwoFL/yOxc+XP3pFbU3h5F7Srv3FPoO1Cnh38P5iiY/pFwIDAQAB",

            //应用ID,您的APPID。
//            'app_id' => "2018082761132725",
            //商户私钥，您的原始格式RSA私钥
//             'merchant_private_key' =>"MIIEpAIBAAKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQABAoIBAQCBQK730TFmpuTOtc669y6BOzUX1EWe+C/mYO28Dn7vqUGbU7UkuihtQIpcNCHhhGAXIHEH0zzrMH3b8XXdXjmo2ChBstr7elJlX2a7WYf9kHNTfRDCE+q5Xj7niSSYE6HOgvWDFMg9nyE3P0WRmTeEvjfVsv2SMoxxIBd8yD1Vxr3Gbg+gT8zWDrqXQ1Ap1gg5jNS14CFE3uKKwQ4n5JZWnIQ+jw3LZcpk9Eb/mrQ9kbnU7g0ikx8sYJpTiP7lAlb3dq1tdUmRV8+HfWYC/a8MbZtO6UyDWvms5Lb5g4we7FCmBAkG+zv62PxG9sQAvrQoSwKTOj/7LSeTgJsT97QNAoGBAPuQUNZEhVODVhCCISg84TGi0BozU64PqegJXFxbR++hQC2EsN6L2Mk2ftpd+J/9XRD0ffcBMea+H4N7ui4Y+OHoED/8d76dTX06PWfAYYJMu/o65c3IBSBiwgREuRo38a20CZ8hKr8LVpLXbtCB8WJ1kp5QeqqSPpwnjFncyBorAoGBAMu3Hokjze+FPpeFQ3tYVt9G/VSAhRMVAb5ZQClQH9plpVM9aMukp8jiaeSBg7d5RzNRGRU5ouKQ1AVs3jkgvVzUWRMKM+VkW4lzAhEkM766egpzngs9z4YXHcBW1bPJQap2TVLRcFmueDsVABXF5XZSgAwenBhtvmZ9X/UDCD+LAoGBALmXaOwLNUm9lVsshgXHlGQoN9t8jnnV+IXFkixY86NolY5/XHVzOwaHe+LifTCbnXOKzPvUF9qh3WIFf//OUJ9ps8NhIX6xUp/WvcKzfbzBm9Uqaqv8qzuPYJABm4YqS9TZBFgwAfdcCAzhf1G47Dq1fuvpd/YrWqGd07/gUIhtAoGAHDSkg7RzZQB75BrNdxyKGqwHk1WgFz5HWYWd/ppbbq+4LkhIZDnOCWBf7QWJqTOfihlmcavjQ59t27pxIlPIJDw6gQpemRpGGkfUN29dwsCq+Rt8/G14eEZnFiRvvk7VSrbKifb5qVEg0H1d36Xg2Xsew47Ragh33lTpnlDnKXUCgYBIuk9VU3DkITWsy+xiQbN4eQqbiFB7BA55xIjwPqK8K+0PVzRyObUEF6m9KSz2mEB1CHwr1fHj8qzJ/0CgKUeCONm5crLEGCGMbGUzMloGmVLSJz6+4xT8mwKOv/BcpTqkDLx+8HBaJppJnjWn0OmHLNa1JhAaVuef8eheH546kw==",
            //异步通知地址
//            'notify_url' => "http://localhost/automobile/public/index_pay_code",
            'notify_url' => "https://zlh188.cn/index_pay_code",
            //同步跳转
//            'return_url' => "http://localhost/automobile/public/index_pay_code",
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
                    $parts =Db::name("order_service")->field("service_goods_name")->where("service_order_number",$_GET['out_trade_no'])->select();
                    foreach($parts as $ks=>$vs){
                        $titles[] = $vs["parts_goods_name"];
                    }
                    $title =implode("",$titles);
                    $money =$parts[0]["service_real_pay"];//金额
                    $datas["user_id"] =$parts[0]["user_id"]; //用户ID
                    $datas["wallet_operation"] = -$money; //消费金额
                    $datas["wallet_type"] = -1; //消费操作(1入，-1出)
                    $datas["operation_time"] = date("Y-m-d H:i:s"); //操作时间
                    $datas["wallet_remarks"] = "订单号：".$_GET['out_trade_no']."，支付宝消费".$money; //消费备注
                    $datas["wallet_img"] = "index/image/alipay.png"; //图标
                    $datas["title"] = $title; //标题（消费内容）
                    Db::name("wallet")->insert($datas);
                   $this->redirect('index/OrderService/order_service_wait_deliver');
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
//            'merchant_private_key' => "MIIEpgIBAAKCAQEA86bQJlbz6FgjGn6/aS0UCGm1YWWBQvD5JhSUrUW4W3hAInKkLgga8rdNjbHsuMDzoBUEppeEU7CVnjTYusn4BoP4JS1q96n569IlFRltiPrykbuzcGf0HpDY1/gujy6klgF+rIZa6SL0D3LN5Yrefb5Vh16YTsiEhg3EGdGYxYGz0lNAk4dM+whleSQyajtYiK14rdKNoXX4Djs5dzn/xaomV7HGZHLyRE/GKx1jWVH5WHYJmL7CeOWdc1pSDqn0QseDds+o14wu7kRlOkiSkck1U0XiG753FxEQJjmcou4g0FbJxkViRETuFsXxhQBwCvlQfXDgwx9eupP42SFX8wIDAQABAoIBAQDORATowCniI2MsVerDJkdUS/EiT+Ua/5dcloW7Hk/RF77rqrO0yGXP3kAIWXz2d1qHxeNXCOQcWVyAP2rOzpZrJaOYDZgFrF8INbHw58Lj+NYLa6Sf31E54xO/2bnIuwrYdtF/SoqB3XZR8bW+wk5SkyQi4oD8CSKvvc2qsdasM9dgCRycfrCWFEKQv8SDj08hbN9yZb1rRlkbr8g+zgkjUtBeC0olkoORLBmmcuAYp6yyQqWDHsnCYXTAMLwzuKGXWQ6mnM8Rtl1bjXjI/ljHWWMTp17Toqr+th0uD/TTXOcgibb9jrctm40cZS6ryOaVM9EVE0gNsupzQLBnFv2BAoGBAPvYrqZiLEHqnBzDBSyzO25ZlnA6TB+0uqJkAfcEJLTWIjuMy8jVXVI1+vzAykjaP4fCl/hQRcqIACRIiavXE1mAx94MAqWOhNT+qn4OhghxRnBDrrM8o+IxurgDPddbgzfcqYr+rOrdL4NQnS88wbhJ4IcBKitEJZ/AzDwvTCuzAoGBAPeriBx+qkAEhz8TsgnbXD3YV9g0OzuVdcPCaDt76KjvMJAM7wYwJ1V24r5D0Rds7OyQLAVORD4hndxFwnovuMyCFHAOOpGuc4avTHA6j8emQJ5l/T8uwaBWWPq/y9IJJn0MBG/raF1b0lizVPsknuwIWOscDNd/gZ/BW3PjrwLBAoGBAJLy+/VZkzljC3Q81ugeLK9W1tDlM0XMzpmygPMBF+nsiEz2+nFVDf7KQIj6dQ3NSwSMJuBc9lwWvv5s1OfPtxqiafzGRUUUw60Ea7biApMqhU88LtOmrnltSuXT/mGKpCYUO1k/AOaJ6Q9WGmFVsjM16CzeIRzdxpZQiNsVPfzBAoGBAI70j4gDBLoW4wnhkG5oE4IPEgVVgKsPxf3MhOVwzAZWqa08CeuEd++4w4AXHOVslgSl4wLc/j7uoHO4QFzOlthUbP+Q9vWHwF8JaBGPe9Y9fCbxr498qX6m2I1Dj54YcgLToOwI8SCSE4PnXzHnHWQYHVr3weHRUzynGuZ1kVsBAoGBAO0LOlejhlgZ3MzqorftTay0iBg/jsjL5FKfEBrXiHBqmo3aZrEKdtO9B38fvTJumPdy6jG6CdyonW3FK/O6PzFW3Gf3ZZoR2fV/2qPUKhI3yPJx8ilAhhMLV+pdFhtNFdbANlNbvRfzE9DFEP3cvhEahOGMnwLiTH/oUVPks22L",
            'merchant_private_key' => "MIIEpQIBAAKCAQEAvPQu30qb4OsdxvU7e+5QlbK6xlku6q/Gm940nrGciz46+ICyAuifLs0OF+qH4HH9hIf5rxslsHTOHREMk/6SyUmkpE/ChYm4vp1tHcIknqWQsDaYHKFs9ML0OW4BI3xZxSs3YKOkdNTgmKFUZLAK1z59guo14MbpYskDmk8WaNHkKJgipTnwYMaXac8hHCDUOH0asMCbb2ZNwlCM9M4rGNaL62USeKhq26HiX7uct3Xd5I4a+l9Bk0COx05DGRLdX7Qo5TCatTK+g89BFCk2REQHYlSoIfR1rzJkSz2wyw/wz4GcfRxRO8t6khVJ5TclRL1BqH0P5aCwmqru4ZwqbQIDAQABAoIBACNadKKmrP+qPgoJvDV143H+3N9btGGIvdpP+vxuMuICpLn2KlhM+euhxi9HXGDsMwjnABp5M9YOJLpfhED0crZipwFvQvAFUqGnhtD4kC12wNmXfWJt+ZraFyPfpyrii1FOwq+8LnsFkXg9RzMHlR+su4MRGz+RN/2Zqqjmf0ThHYWl9bMzkl77s7TNK98zdsyLYf12qa/PZEPl7Zf2Sg5ksVZl+ilkHeR02OKt+zj04SIhw/IgZO/0jfaq9xHUTWXfJH45WWIjOKwsS5aV74iL9iHIdoFfGsPRsA0am9Hrj0lEH/WWHyQoNSzZpNCcvkGUh2q4dBQoOT1fw5D4GykCgYEA++njkSiclymIQu6JdSEGPHISH6R0IcBe2mvTWBZ6j+PXiStk/I33QWo4fxT4Qx0lRcuD2bc44rWYgCbZXm90GdymeSkZ94lpl0/4XEfdrT6deWrS7wEnV+U+Nu/3M3RmoxNAmq1w/IYc5MPkPhrkxoAZf7eqQFtVuAeNfI+s4eMCgYEAwATX/XuWAi/PP00dUoiq0I/MbPrU3W0zYA/V55+8/tyrLBs/1XXQXirp7GzmjTFBz5xh8FflIISv6KVdQ+5R/gx/66qYq7MklGeNvnLS8bvL6T+loF7w2xY7FHQ7J5q8Jtgd6ENe525WFBefHohxCsASbedOWNwVqZUj23GiM28CgYEA55tMLINoJNp8ev0Qx+2K7Dv8SbdLRlS1YJ/N+akBGhXibizFdiWX9D6SntRKxHmhcSABo0N/O4C2KuyM4PQbjEN/ktmlFB70Q7e0ojM4rjVpVt5N/IvU/ky3/I53eolEbB4hQZTA0osDOZiY2jijqZTQ3Mmh7/WJQ989yA3YZv0CgYEAvzCTdvrk5AyCiH7Z4WHe4ocrfp9J76dZbh6WV2g/oGxLq0D+kCYccIa+IABWluMiqfsFsW9y3qv/EuAEOy1CSnhbrmVQWjWnCHULN0PRMYxRfk83NLUTkLFT5gvNEqQJD4xESw05o6nC34KdlqM4GwIf13pmEighEZdD+q3sWNUCgYEAhTz2NfzdDfT0gZGxfpUhhK0nkqdNI8HX2CfUVMPQjmnbgZ9Hfav+RMMf+02ftUzmZX0f/VyytSu5i0bkIUClUqxvjaKXRwwoiv3Tb2JHTururXBViUOCQeCJuXarX89EoReIvUXBBw2G6HcULGfuuNZM81fd/cav4EZfMO2+zPM=",
            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
//            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA86bQJlbz6FgjGn6/aS0UCGm1YWWBQvD5JhSUrUW4W3hAInKkLgga8rdNjbHsuMDzoBUEppeEU7CVnjTYusn4BoP4JS1q96n569IlFRltiPrykbuzcGf0HpDY1/gujy6klgF+rIZa6SL0D3LN5Yrefb5Vh16YTsiEhg3EGdGYxYGz0lNAk4dM+whleSQyajtYiK14rdKNoXX4Djs5dzn/xaomV7HGZHLyRE/GKx1jWVH5WHYJmL7CeOWdc1pSDqn0QseDds+o14wu7kRlOkiSkck1U0XiG753FxEQJjmcou4g0FbJxkViRETuFsXxhQBwCvlQfXDgwx9eupP42SFX8wIDAQAB",
            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyUgwfM85daNJ3tHlmsFET1Mcc6kaXjLD9AdjOy8pknzHVvDp66fMp2TZfnJjwGUlYDr9w/mY9CDbzwQgwllVUCSNxzVLH57FfRuzVStoHN22BJBMTvkMPpSbEvV3MxEzXzrl2kyV5sqEEyDiY28wpX1aFb5ug/G18R6216DMqGlTdYwHSOBd+S+2r2B5ljk786dtRf0inimEAGP5CdX2HxI8svpUVq2OeabSZ/kvf39yW9oLNLz0WIAwHQiWSg3DCb+MBP4I/W+5pX6N8p+ijqtPDJTF7/XBDQnftKDfVwoFL/yOxc+XP3pFbU3h5F7Srv3FPoO1Cnh38P5iiY/pFwIDAQAB",


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
                    $datas["user_id"] =$parts[0]["user_id"]; //用户ID
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
     * Notes:服务商订单回调
     **************************************
     */
    public function service_notifyurl()
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
        if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS' || $trade_status =="Success") {
            $data['status'] = 2;//状态值
            $data['trade_no'] = $trade_no;//支付宝交易号
            $data['pay_type_content'] = "支付宝";//支付宝交易号
            $condition['service_order_number'] = $out_trade_no;
            $result = Db::name('order_service')->where($condition)->update($data);//修改订单状态,支付宝单号到数据库
            if ($result > 0) {
                //进行钱包消费记录
//                $parts =Db::name("order_service")
//                    ->field("service_goods_name,service_real_pay,user_id")
//                    ->where($condition)
//                    ->find();
//                $title =$parts["service_goods_name"];
//                $money =$parts["service_real_pay"];//金额
//                $datas["user_id"] =$parts["user_id"]; //用户ID
//                $datas["wallet_operation"] = -$money; //消费金额
//                $datas["wallet_type"] = -1; //消费操作(1入，-1出)
//                $datas["operation_time"] = date("Y-m-d H:i:s"); //操作时间
//                $datas["wallet_remarks"] = "订单号：".$out_trade_no."，支付宝消费".$money; //消费备注
//                $datas["wallet_img"] = "index/image/alipay.png"; //图标
//                $datas["title"] = $title; //标题（消费内容）
//                $datas["order_number"] =$out_trade_no; //订单编号
//                $datas["pay_type"] ="支付宝";//消费类型
//                $datas["wallet_balance"] =1; //此刻钱包余额
                $datas["user_id"] =1; //用户ID
                $datas["wallet_operation"] = -1; //消费金额
                $datas["wallet_type"] = -1; //消费操作(1入，-1出)
                $datas["operation_time"] = date("Y-m-d H:i:s"); //操作时间
                $datas["wallet_remarks"] = 1; //消费备注
                $datas["wallet_img"] = "index/image/alipay.png"; //图标
                $datas["title"] = 1; //标题（消费内容）
                $datas["order_number"] =1; //订单编号
                $datas["pay_type"] ="支付宝";//消费类型
                $datas["wallet_balance"] =1; //此刻钱包余额
                Db::name("wallet")->insert($datas);
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
//            'merchant_private_key' => "MIIEpgIBAAKCAQEA86bQJlbz6FgjGn6/aS0UCGm1YWWBQvD5JhSUrUW4W3hAInKkLgga8rdNjbHsuMDzoBUEppeEU7CVnjTYusn4BoP4JS1q96n569IlFRltiPrykbuzcGf0HpDY1/gujy6klgF+rIZa6SL0D3LN5Yrefb5Vh16YTsiEhg3EGdGYxYGz0lNAk4dM+whleSQyajtYiK14rdKNoXX4Djs5dzn/xaomV7HGZHLyRE/GKx1jWVH5WHYJmL7CeOWdc1pSDqn0QseDds+o14wu7kRlOkiSkck1U0XiG753FxEQJjmcou4g0FbJxkViRETuFsXxhQBwCvlQfXDgwx9eupP42SFX8wIDAQABAoIBAQDORATowCniI2MsVerDJkdUS/EiT+Ua/5dcloW7Hk/RF77rqrO0yGXP3kAIWXz2d1qHxeNXCOQcWVyAP2rOzpZrJaOYDZgFrF8INbHw58Lj+NYLa6Sf31E54xO/2bnIuwrYdtF/SoqB3XZR8bW+wk5SkyQi4oD8CSKvvc2qsdasM9dgCRycfrCWFEKQv8SDj08hbN9yZb1rRlkbr8g+zgkjUtBeC0olkoORLBmmcuAYp6yyQqWDHsnCYXTAMLwzuKGXWQ6mnM8Rtl1bjXjI/ljHWWMTp17Toqr+th0uD/TTXOcgibb9jrctm40cZS6ryOaVM9EVE0gNsupzQLBnFv2BAoGBAPvYrqZiLEHqnBzDBSyzO25ZlnA6TB+0uqJkAfcEJLTWIjuMy8jVXVI1+vzAykjaP4fCl/hQRcqIACRIiavXE1mAx94MAqWOhNT+qn4OhghxRnBDrrM8o+IxurgDPddbgzfcqYr+rOrdL4NQnS88wbhJ4IcBKitEJZ/AzDwvTCuzAoGBAPeriBx+qkAEhz8TsgnbXD3YV9g0OzuVdcPCaDt76KjvMJAM7wYwJ1V24r5D0Rds7OyQLAVORD4hndxFwnovuMyCFHAOOpGuc4avTHA6j8emQJ5l/T8uwaBWWPq/y9IJJn0MBG/raF1b0lizVPsknuwIWOscDNd/gZ/BW3PjrwLBAoGBAJLy+/VZkzljC3Q81ugeLK9W1tDlM0XMzpmygPMBF+nsiEz2+nFVDf7KQIj6dQ3NSwSMJuBc9lwWvv5s1OfPtxqiafzGRUUUw60Ea7biApMqhU88LtOmrnltSuXT/mGKpCYUO1k/AOaJ6Q9WGmFVsjM16CzeIRzdxpZQiNsVPfzBAoGBAI70j4gDBLoW4wnhkG5oE4IPEgVVgKsPxf3MhOVwzAZWqa08CeuEd++4w4AXHOVslgSl4wLc/j7uoHO4QFzOlthUbP+Q9vWHwF8JaBGPe9Y9fCbxr498qX6m2I1Dj54YcgLToOwI8SCSE4PnXzHnHWQYHVr3weHRUzynGuZ1kVsBAoGBAO0LOlejhlgZ3MzqorftTay0iBg/jsjL5FKfEBrXiHBqmo3aZrEKdtO9B38fvTJumPdy6jG6CdyonW3FK/O6PzFW3Gf3ZZoR2fV/2qPUKhI3yPJx8ilAhhMLV+pdFhtNFdbANlNbvRfzE9DFEP3cvhEahOGMnwLiTH/oUVPks22L",
            'merchant_private_key' => "MIIEpQIBAAKCAQEAvPQu30qb4OsdxvU7e+5QlbK6xlku6q/Gm940nrGciz46+ICyAuifLs0OF+qH4HH9hIf5rxslsHTOHREMk/6SyUmkpE/ChYm4vp1tHcIknqWQsDaYHKFs9ML0OW4BI3xZxSs3YKOkdNTgmKFUZLAK1z59guo14MbpYskDmk8WaNHkKJgipTnwYMaXac8hHCDUOH0asMCbb2ZNwlCM9M4rGNaL62USeKhq26HiX7uct3Xd5I4a+l9Bk0COx05DGRLdX7Qo5TCatTK+g89BFCk2REQHYlSoIfR1rzJkSz2wyw/wz4GcfRxRO8t6khVJ5TclRL1BqH0P5aCwmqru4ZwqbQIDAQABAoIBACNadKKmrP+qPgoJvDV143H+3N9btGGIvdpP+vxuMuICpLn2KlhM+euhxi9HXGDsMwjnABp5M9YOJLpfhED0crZipwFvQvAFUqGnhtD4kC12wNmXfWJt+ZraFyPfpyrii1FOwq+8LnsFkXg9RzMHlR+su4MRGz+RN/2Zqqjmf0ThHYWl9bMzkl77s7TNK98zdsyLYf12qa/PZEPl7Zf2Sg5ksVZl+ilkHeR02OKt+zj04SIhw/IgZO/0jfaq9xHUTWXfJH45WWIjOKwsS5aV74iL9iHIdoFfGsPRsA0am9Hrj0lEH/WWHyQoNSzZpNCcvkGUh2q4dBQoOT1fw5D4GykCgYEA++njkSiclymIQu6JdSEGPHISH6R0IcBe2mvTWBZ6j+PXiStk/I33QWo4fxT4Qx0lRcuD2bc44rWYgCbZXm90GdymeSkZ94lpl0/4XEfdrT6deWrS7wEnV+U+Nu/3M3RmoxNAmq1w/IYc5MPkPhrkxoAZf7eqQFtVuAeNfI+s4eMCgYEAwATX/XuWAi/PP00dUoiq0I/MbPrU3W0zYA/V55+8/tyrLBs/1XXQXirp7GzmjTFBz5xh8FflIISv6KVdQ+5R/gx/66qYq7MklGeNvnLS8bvL6T+loF7w2xY7FHQ7J5q8Jtgd6ENe525WFBefHohxCsASbedOWNwVqZUj23GiM28CgYEA55tMLINoJNp8ev0Qx+2K7Dv8SbdLRlS1YJ/N+akBGhXibizFdiWX9D6SntRKxHmhcSABo0N/O4C2KuyM4PQbjEN/ktmlFB70Q7e0ojM4rjVpVt5N/IvU/ky3/I53eolEbB4hQZTA0osDOZiY2jijqZTQ3Mmh7/WJQ989yA3YZv0CgYEAvzCTdvrk5AyCiH7Z4WHe4ocrfp9J76dZbh6WV2g/oGxLq0D+kCYccIa+IABWluMiqfsFsW9y3qv/EuAEOy1CSnhbrmVQWjWnCHULN0PRMYxRfk83NLUTkLFT5gvNEqQJD4xESw05o6nC34KdlqM4GwIf13pmEighEZdD+q3sWNUCgYEAhTz2NfzdDfT0gZGxfpUhhK0nkqdNI8HX2CfUVMPQjmnbgZ9Hfav+RMMf+02ftUzmZX0f/VyytSu5i0bkIUClUqxvjaKXRwwoiv3Tb2JHTururXBViUOCQeCJuXarX89EoReIvUXBBw2G6HcULGfuuNZM81fd/cav4EZfMO2+zPM=",
            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
//            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA86bQJlbz6FgjGn6/aS0UCGm1YWWBQvD5JhSUrUW4W3hAInKkLgga8rdNjbHsuMDzoBUEppeEU7CVnjTYusn4BoP4JS1q96n569IlFRltiPrykbuzcGf0HpDY1/gujy6klgF+rIZa6SL0D3LN5Yrefb5Vh16YTsiEhg3EGdGYxYGz0lNAk4dM+whleSQyajtYiK14rdKNoXX4Djs5dzn/xaomV7HGZHLyRE/GKx1jWVH5WHYJmL7CeOWdc1pSDqn0QseDds+o14wu7kRlOkiSkck1U0XiG753FxEQJjmcou4g0FbJxkViRETuFsXxhQBwCvlQfXDgwx9eupP42SFX8wIDAQAB",
            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyUgwfM85daNJ3tHlmsFET1Mcc6kaXjLD9AdjOy8pknzHVvDp66fMp2TZfnJjwGUlYDr9w/mY9CDbzwQgwllVUCSNxzVLH57FfRuzVStoHN22BJBMTvkMPpSbEvV3MxEzXzrl2kyV5sqEEyDiY28wpX1aFb5ug/G18R6216DMqGlTdYwHSOBd+S+2r2B5ljk786dtRf0inimEAGP5CdX2HxI8svpUVq2OeabSZ/kvf39yW9oLNLz0WIAwHQiWSg3DCb+MBP4I/W+5pX6N8p+ijqtPDJTF7/XBDQnftKDfVwoFL/yOxc+XP3pFbU3h5F7Srv3FPoO1Cnh38P5iiY/pFwIDAQAB",


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
                    $datas["user_id"] =$recharge_record_data["user_id"]; //用户id
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
                        $user_wallet =Db::name("user")->field("user_wallet")->where("id",$recharge_record_data["user_id"])->find();
                        Db::name("user")->where("id",$recharge_record_data["user_id"])->update(["user_wallet"=>$user_wallet["user_wallet"]+$recharge_record_data["recharge_money"]+ $lists]);
                    }else{
                        $datas["operation_amount"] =$recharge_record_data["recharge_money"]; //操作金额
                        $datas["recharge_describe"] ="充值".$recharge_record_data["recharge_money"]."元"; //描述
                        Db::name("recharge_reflect")->insert($datas);//插到记录
                        $user_wallet =Db::name("user")->field("user_wallet")->where("id",$recharge_record_data["user_id"])->find();
                        Db::name("user")->where("id",$recharge_record_data["user_id"])->update(["user_wallet"=>$user_wallet["user_wallet"]+$recharge_record_data["recharge_money"]]);
                    }
                    $this->redirect('index/wallet/index');
                }
            }
        }
    }




    /**
     **************陈绪*******************
     * 生成支付宝签名 TODO:配件商支付宝支付生成签名
     **************************************
     */
    public function ios_api_alipay(Request $request){
        if($request->isPost()){
            $order_num =$request->only(['order_num'])['order_num'];
            $product_code ="QUICK_MSECURITY_PAY";
            $out_trade_no="ZQLM3O56MJD4SK3";
            $time =date('Y-m-d H:i:s');
            if(!empty( $order_num)){
                $counts =Db::name('order_parts')->where('parts_order_number',$order_num)->count();
                if(!empty($counts)){
                    $data = Db::name('order_parts')->where('parts_order_number',$order_num)->select();
                    if(!empty($data)){
                        include VENDOR_PATH."AliPay/aop/AopClient.php";
                        foreach ($data as $k=>$v){
                            $goods_name = $v['parts_goods_name'];
                            $order_num = $v['parts_order_number'];
                            $goods_pay_money =$v['order_real_pay'];
                            $subject =$v['order_quantity'];
                            $app_id ="{'timeout_express':'30m','seller_id':"."'".$order_num."'".",'product_code':"."'".$product_code."'".",'total_amount':"."'".$goods_pay_money."'".",'subject':"."'".$subject."'".",'body':"."'".$goods_name."'".",'out_trade_no':"."'".$out_trade_no."'"."}";
                            $app_ids =urlencode($app_id);
                            $time_encode =urlencode($time);
                            // 订单信息，在iOS端加密
                            $private_path="MIIEpQIBAAKCAQEAvPQu30qb4OsdxvU7e+5QlbK6xlku6q/Gm940nrGciz46+ICyAuifLs0OF+qH4HH9hIf5rxslsHTOHREMk/6SyUmkpE/ChYm4vp1tHcIknqWQsDaYHKFs9ML0OW4BI3xZxSs3YKOkdNTgmKFUZLAK1z59guo14MbpYskDmk8WaNHkKJgipTnwYMaXac8hHCDUOH0asMCbb2ZNwlCM9M4rGNaL62USeKhq26HiX7uct3Xd5I4a+l9Bk0COx05DGRLdX7Qo5TCatTK+g89BFCk2REQHYlSoIfR1rzJkSz2wyw/wz4GcfRxRO8t6khVJ5TclRL1BqH0P5aCwmqru4ZwqbQIDAQABAoIBACNadKKmrP+qPgoJvDV143H+3N9btGGIvdpP+vxuMuICpLn2KlhM+euhxi9HXGDsMwjnABp5M9YOJLpfhED0crZipwFvQvAFUqGnhtD4kC12wNmXfWJt+ZraFyPfpyrii1FOwq+8LnsFkXg9RzMHlR+su4MRGz+RN/2Zqqjmf0ThHYWl9bMzkl77s7TNK98zdsyLYf12qa/PZEPl7Zf2Sg5ksVZl+ilkHeR02OKt+zj04SIhw/IgZO/0jfaq9xHUTWXfJH45WWIjOKwsS5aV74iL9iHIdoFfGsPRsA0am9Hrj0lEH/WWHyQoNSzZpNCcvkGUh2q4dBQoOT1fw5D4GykCgYEA++njkSiclymIQu6JdSEGPHISH6R0IcBe2mvTWBZ6j+PXiStk/I33QWo4fxT4Qx0lRcuD2bc44rWYgCbZXm90GdymeSkZ94lpl0/4XEfdrT6deWrS7wEnV+U+Nu/3M3RmoxNAmq1w/IYc5MPkPhrkxoAZf7eqQFtVuAeNfI+s4eMCgYEAwATX/XuWAi/PP00dUoiq0I/MbPrU3W0zYA/V55+8/tyrLBs/1XXQXirp7GzmjTFBz5xh8FflIISv6KVdQ+5R/gx/66qYq7MklGeNvnLS8bvL6T+loF7w2xY7FHQ7J5q8Jtgd6ENe525WFBefHohxCsASbedOWNwVqZUj23GiM28CgYEA55tMLINoJNp8ev0Qx+2K7Dv8SbdLRlS1YJ/N+akBGhXibizFdiWX9D6SntRKxHmhcSABo0N/O4C2KuyM4PQbjEN/ktmlFB70Q7e0ojM4rjVpVt5N/IvU/ky3/I53eolEbB4hQZTA0osDOZiY2jijqZTQ3Mmh7/WJQ989yA3YZv0CgYEAvzCTdvrk5AyCiH7Z4WHe4ocrfp9J76dZbh6WV2g/oGxLq0D+kCYccIa+IABWluMiqfsFsW9y3qv/EuAEOy1CSnhbrmVQWjWnCHULN0PRMYxRfk83NLUTkLFT5gvNEqQJD4xESw05o6nC34KdlqM4GwIf13pmEighEZdD+q3sWNUCgYEAhTz2NfzdDfT0gZGxfpUhhK0nkqdNI8HX2CfUVMPQjmnbgZ9Hfav+RMMf+02ftUzmZX0f/VyytSu5i0bkIUClUqxvjaKXRwwoiv3Tb2JHTururXBViUOCQeCJuXarX89EoReIvUXBBw2G6HcULGfuuNZM81fd/cav4EZfMO2+zPM=";
                            //构造业务请求参数的集合(订单信息)
                            $content = array();
                            $content['subject'] = $goods_name;
                            $content['out_trade_no'] = $order_num;
                            $content['timeout_express'] = "90m";
                            $content['total_amount'] = $goods_pay_money;
                            $content['product_code'] = "QUICK_MSECURITY_PAY";
                            $con = json_encode($content);//$content是biz_content的值,将之转化成json字符串
                            //公共参数
                        }
                        $Client = new \AopClient();//实例化支付宝sdk里面的AopClient类,下单时需要的操作,都在这个类里面
                        $param['app_id'] = '2018120762470526';
                        $param['method'] = 'alipay.trade.app.pay';//接口名称，固定值
                        $param['charset'] = 'utf-8';//请求使用的编码格式
                        $param['sign_type'] = 'RSA2';//商户生成签名字符串所使用的签名算法类型
                        $param['timestamp'] = date("Y-m-d H:i:s");//发送请求的时间
                        $param['version'] = '1.0';//调用的接口版本，固定为：1.0
                        $param['notify_url'] = 'https://automobile.siring.com.cn/notifyurls';
                        $param['biz_content'] = $con;//业务请求参数的集合,长度不限,json格式，即前面一步得到的
                        $paramStr = $Client->getSignContent($param);//组装请求签名参数
                        $sign = $Client->alonersaSign($paramStr, $private_path, 'RSA2', false);//生成签名()
                        $param['sign'] = $sign;
                        $str = $Client->getSignContentUrlencode($param);//最终请求参数
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
     **************陈绪*******************
     * 生成支付宝签名 TODO:服务商支付宝支付生成签名
     **************************************
     */
    public function ios_api_service_alipay(Request $request){
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
                            $goods_pay_money =$v['service_real_pay'];
                            $subject =$v['service_order_quantitative'];
                            $app_id ="{'timeout_express':'30m','seller_id':"."'".$order_num."'".",'product_code':"."'".$product_code."'".",'total_amount':"."'".$goods_pay_money."'".",'subject':"."'".$subject."'".",'body':"."'".$goods_name."'".",'out_trade_no':"."'".$out_trade_no."'"."}";
                            $app_ids =urlencode($app_id);
                            $time_encode =urlencode($time);
                            // 订单信息，在iOS端加密
                            include VENDOR_PATH."AliPay/aop/AopClient.php";
                            $private_path="MIIEpQIBAAKCAQEAvPQu30qb4OsdxvU7e+5QlbK6xlku6q/Gm940nrGciz46+ICyAuifLs0OF+qH4HH9hIf5rxslsHTOHREMk/6SyUmkpE/ChYm4vp1tHcIknqWQsDaYHKFs9ML0OW4BI3xZxSs3YKOkdNTgmKFUZLAK1z59guo14MbpYskDmk8WaNHkKJgipTnwYMaXac8hHCDUOH0asMCbb2ZNwlCM9M4rGNaL62USeKhq26HiX7uct3Xd5I4a+l9Bk0COx05DGRLdX7Qo5TCatTK+g89BFCk2REQHYlSoIfR1rzJkSz2wyw/wz4GcfRxRO8t6khVJ5TclRL1BqH0P5aCwmqru4ZwqbQIDAQABAoIBACNadKKmrP+qPgoJvDV143H+3N9btGGIvdpP+vxuMuICpLn2KlhM+euhxi9HXGDsMwjnABp5M9YOJLpfhED0crZipwFvQvAFUqGnhtD4kC12wNmXfWJt+ZraFyPfpyrii1FOwq+8LnsFkXg9RzMHlR+su4MRGz+RN/2Zqqjmf0ThHYWl9bMzkl77s7TNK98zdsyLYf12qa/PZEPl7Zf2Sg5ksVZl+ilkHeR02OKt+zj04SIhw/IgZO/0jfaq9xHUTWXfJH45WWIjOKwsS5aV74iL9iHIdoFfGsPRsA0am9Hrj0lEH/WWHyQoNSzZpNCcvkGUh2q4dBQoOT1fw5D4GykCgYEA++njkSiclymIQu6JdSEGPHISH6R0IcBe2mvTWBZ6j+PXiStk/I33QWo4fxT4Qx0lRcuD2bc44rWYgCbZXm90GdymeSkZ94lpl0/4XEfdrT6deWrS7wEnV+U+Nu/3M3RmoxNAmq1w/IYc5MPkPhrkxoAZf7eqQFtVuAeNfI+s4eMCgYEAwATX/XuWAi/PP00dUoiq0I/MbPrU3W0zYA/V55+8/tyrLBs/1XXQXirp7GzmjTFBz5xh8FflIISv6KVdQ+5R/gx/66qYq7MklGeNvnLS8bvL6T+loF7w2xY7FHQ7J5q8Jtgd6ENe525WFBefHohxCsASbedOWNwVqZUj23GiM28CgYEA55tMLINoJNp8ev0Qx+2K7Dv8SbdLRlS1YJ/N+akBGhXibizFdiWX9D6SntRKxHmhcSABo0N/O4C2KuyM4PQbjEN/ktmlFB70Q7e0ojM4rjVpVt5N/IvU/ky3/I53eolEbB4hQZTA0osDOZiY2jijqZTQ3Mmh7/WJQ989yA3YZv0CgYEAvzCTdvrk5AyCiH7Z4WHe4ocrfp9J76dZbh6WV2g/oGxLq0D+kCYccIa+IABWluMiqfsFsW9y3qv/EuAEOy1CSnhbrmVQWjWnCHULN0PRMYxRfk83NLUTkLFT5gvNEqQJD4xESw05o6nC34KdlqM4GwIf13pmEighEZdD+q3sWNUCgYEAhTz2NfzdDfT0gZGxfpUhhK0nkqdNI8HX2CfUVMPQjmnbgZ9Hfav+RMMf+02ftUzmZX0f/VyytSu5i0bkIUClUqxvjaKXRwwoiv3Tb2JHTururXBViUOCQeCJuXarX89EoReIvUXBBw2G6HcULGfuuNZM81fd/cav4EZfMO2+zPM=";
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
                            $param['app_id'] = '2018120762470526';
                            $param['method'] = 'alipay.trade.app.pay';//接口名称，固定值
                            $param['charset'] = 'utf-8';//请求使用的编码格式
                            $param['sign_type'] = 'RSA2';//商户生成签名字符串所使用的签名算法类型
                            $param['timestamp'] = date("Y-m-d H:i:s");//发送请求的时间
                            $param['version'] = '1.0';//调用的接口版本，固定为：1.0
                            $param['notify_url'] = 'https://automobile.siring.com.cn/service_notifyurl';
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







}