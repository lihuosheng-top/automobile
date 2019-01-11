<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/1/11
 * Time: 11:58
 */
namespace app\admin\controller;
use think\Controller;

class Payment extends Controller{


    /**
     * 支付宝退款
     * 陈绪
     */
    public function WxPayment(){

        $order["out_trade_no"] = "2019011110174781101021";
        include EXTEND_PATH."AliPay/aop/AopClient.php";
        include EXTEND_PATH."AliPay/aop/request/AlipayTradeRefundRequest.php";
        $aop = new \AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = '2018120762470526';
        $aop->rsaPrivateKey = 'MIIEpQIBAAKCAQEAvPQu30qb4OsdxvU7e+5QlbK6xlku6q/Gm940nrGciz46+ICyAuifLs0OF+qH4HH9hIf5rxslsHTOHREMk/6SyUmkpE/ChYm4vp1tHcIknqWQsDaYHKFs9ML0OW4BI3xZxSs3YKOkdNTgmKFUZLAK1z59guo14MbpYskDmk8WaNHkKJgipTnwYMaXac8hHCDUOH0asMCbb2ZNwlCM9M4rGNaL62USeKhq26HiX7uct3Xd5I4a+l9Bk0COx05DGRLdX7Qo5TCatTK+g89BFCk2REQHYlSoIfR1rzJkSz2wyw/wz4GcfRxRO8t6khVJ5TclRL1BqH0P5aCwmqru4ZwqbQIDAQABAoIBACNadKKmrP+qPgoJvDV143H+3N9btGGIvdpP+vxuMuICpLn2KlhM+euhxi9HXGDsMwjnABp5M9YOJLpfhED0crZipwFvQvAFUqGnhtD4kC12wNmXfWJt+ZraFyPfpyrii1FOwq+8LnsFkXg9RzMHlR+su4MRGz+RN/2Zqqjmf0ThHYWl9bMzkl77s7TNK98zdsyLYf12qa/PZEPl7Zf2Sg5ksVZl+ilkHeR02OKt+zj04SIhw/IgZO/0jfaq9xHUTWXfJH45WWIjOKwsS5aV74iL9iHIdoFfGsPRsA0am9Hrj0lEH/WWHyQoNSzZpNCcvkGUh2q4dBQoOT1fw5D4GykCgYEA++njkSiclymIQu6JdSEGPHISH6R0IcBe2mvTWBZ6j+PXiStk/I33QWo4fxT4Qx0lRcuD2bc44rWYgCbZXm90GdymeSkZ94lpl0/4XEfdrT6deWrS7wEnV+U+Nu/3M3RmoxNAmq1w/IYc5MPkPhrkxoAZf7eqQFtVuAeNfI+s4eMCgYEAwATX/XuWAi/PP00dUoiq0I/MbPrU3W0zYA/V55+8/tyrLBs/1XXQXirp7GzmjTFBz5xh8FflIISv6KVdQ+5R/gx/66qYq7MklGeNvnLS8bvL6T+loF7w2xY7FHQ7J5q8Jtgd6ENe525WFBefHohxCsASbedOWNwVqZUj23GiM28CgYEA55tMLINoJNp8ev0Qx+2K7Dv8SbdLRlS1YJ/N+akBGhXibizFdiWX9D6SntRKxHmhcSABo0N/O4C2KuyM4PQbjEN/ktmlFB70Q7e0ojM4rjVpVt5N/IvU/ky3/I53eolEbB4hQZTA0osDOZiY2jijqZTQ3Mmh7/WJQ989yA3YZv0CgYEAvzCTdvrk5AyCiH7Z4WHe4ocrfp9J76dZbh6WV2g/oGxLq0D+kCYccIa+IABWluMiqfsFsW9y3qv/EuAEOy1CSnhbrmVQWjWnCHULN0PRMYxRfk83NLUTkLFT5gvNEqQJD4xESw05o6nC34KdlqM4GwIf13pmEighEZdD+q3sWNUCgYEAhTz2NfzdDfT0gZGxfpUhhK0nkqdNI8HX2CfUVMPQjmnbgZ9Hfav+RMMf+02ftUzmZX0f/VyytSu5i0bkIUClUqxvjaKXRwwoiv3Tb2JHTururXBViUOCQeCJuXarX89EoReIvUXBBw2G6HcULGfuuNZM81fd/cav4EZfMO2+zPM=';
        $aop->alipayrsaPublicKey='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyUgwfM85daNJ3tHlmsFET1Mcc6kaXjLD9AdjOy8pknzHVvDp66fMp2TZfnJjwGUlYDr9w/mY9CDbzwQgwllVUCSNxzVLH57FfRuzVStoHN22BJBMTvkMPpSbEvV3MxEzXzrl2kyV5sqEEyDiY28wpX1aFb5ug/G18R6216DMqGlTdYwHSOBd+S+2r2B5ljk786dtRf0inimEAGP5CdX2HxI8svpUVq2OeabSZ/kvf39yW9oLNLz0WIAwHQiWSg3DCb+MBP4I/W+5pX6N8p+ijqtPDJTF7/XBDQnftKDfVwoFL/yOxc+XP3pFbU3h5F7Srv3FPoO1Cnh38P5iiY/pFwIDAQAB';
        $aop->apiVersion = '0.01';
        $aop->signType = 'RSA2';
        $aop->postCharset='GBK';
        $aop->format='json';
        $request = new \AlipayTradeRefundRequest ();

    }

}