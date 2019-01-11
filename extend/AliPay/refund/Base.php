<?php
include './Rsa.php';
/* 
 * 黎明互联
 * https://www.liminghulian.com/
 */

class Base extends RSA
{
    /**
     * 以下信息需要根据自己实际情况修改
     */
    const PID = '2088231580479005';//合作伙伴ID
    const KEY = ''; //安全码
    const PAYGAGEWAY = 'https://mapi.alipay.com/gateway.do';
    const CHECKURL = 'https://mapi.alipay.com/gateway.do?service=notify_verify&partner=' . self::PID . '&notify_id=';
    const APPPRIKEY = 'MIIEpQIBAAKCAQEAvPQu30qb4OsdxvU7e+5QlbK6xlku6q/Gm940nrGciz46+ICyAuifLs0OF+qH4HH9hIf5rxslsHTOHREMk/6SyUmkpE/ChYm4vp1tHcIknqWQsDaYHKFs9ML0OW4BI3xZxSs3YKOkdNTgmKFUZLAK1z59guo14MbpYskDmk8WaNHkKJgipTnwYMaXac8hHCDUOH0asMCbb2ZNwlCM9M4rGNaL62USeKhq26HiX7uct3Xd5I4a+l9Bk0COx05DGRLdX7Qo5TCatTK+g89BFCk2REQHYlSoIfR1rzJkSz2wyw/wz4GcfRxRO8t6khVJ5TclRL1BqH0P5aCwmqru4ZwqbQIDAQABAoIBACNadKKmrP+qPgoJvDV143H+3N9btGGIvdpP+vxuMuICpLn2KlhM+euhxi9HXGDsMwjnABp5M9YOJLpfhED0crZipwFvQvAFUqGnhtD4kC12wNmXfWJt+ZraFyPfpyrii1FOwq+8LnsFkXg9RzMHlR+su4MRGz+RN/2Zqqjmf0ThHYWl9bMzkl77s7TNK98zdsyLYf12qa/PZEPl7Zf2Sg5ksVZl+ilkHeR02OKt+zj04SIhw/IgZO/0jfaq9xHUTWXfJH45WWIjOKwsS5aV74iL9iHIdoFfGsPRsA0am9Hrj0lEH/WWHyQoNSzZpNCcvkGUh2q4dBQoOT1fw5D4GykCgYEA++njkSiclymIQu6JdSEGPHISH6R0IcBe2mvTWBZ6j+PXiStk/I33QWo4fxT4Qx0lRcuD2bc44rWYgCbZXm90GdymeSkZ94lpl0/4XEfdrT6deWrS7wEnV+U+Nu/3M3RmoxNAmq1w/IYc5MPkPhrkxoAZf7eqQFtVuAeNfI+s4eMCgYEAwATX/XuWAi/PP00dUoiq0I/MbPrU3W0zYA/V55+8/tyrLBs/1XXQXirp7GzmjTFBz5xh8FflIISv6KVdQ+5R/gx/66qYq7MklGeNvnLS8bvL6T+loF7w2xY7FHQ7J5q8Jtgd6ENe525WFBefHohxCsASbedOWNwVqZUj23GiM28CgYEA55tMLINoJNp8ev0Qx+2K7Dv8SbdLRlS1YJ/N+akBGhXibizFdiWX9D6SntRKxHmhcSABo0N/O4C2KuyM4PQbjEN/ktmlFB70Q7e0ojM4rjVpVt5N/IvU/ky3/I53eolEbB4hQZTA0osDOZiY2jijqZTQ3Mmh7/WJQ989yA3YZv0CgYEAvzCTdvrk5AyCiH7Z4WHe4ocrfp9J76dZbh6WV2g/oGxLq0D+kCYccIa+IABWluMiqfsFsW9y3qv/EuAEOy1CSnhbrmVQWjWnCHULN0PRMYxRfk83NLUTkLFT5gvNEqQJD4xESw05o6nC34KdlqM4GwIf13pmEighEZdD+q3sWNUCgYEAhTz2NfzdDfT0gZGxfpUhhK0nkqdNI8HX2CfUVMPQjmnbgZ9Hfav+RMMf+02ftUzmZX0f/VyytSu5i0bkIUClUqxvjaKXRwwoiv3Tb2JHTururXBViUOCQeCJuXarX89EoReIvUXBBw2G6HcULGfuuNZM81fd/cav4EZfMO2+zPM=';//私钥
    const APPID = '2018120762470526'; //appid
    const NEW_ALIPUBKE = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyUgwfM85daNJ3tHlmsFET1Mcc6kaXjLD9AdjOy8pknzHVvDp66fMp2TZfnJjwGUlYDr9w/mY9CDbzwQgwllVUCSNxzVLH57FfRuzVStoHN22BJBMTvkMPpSbEvV3MxEzXzrl2kyV5sqEEyDiY28wpX1aFb5ug/G18R6216DMqGlTdYwHSOBd+S+2r2B5ljk786dtRf0inimEAGP5CdX2HxI8svpUVq2OeabSZ/kvf39yW9oLNLz0WIAwHQiWSg3DCb+MBP4I/W+5pX6N8p+ijqtPDJTF7/XBDQnftKDfVwoFL/yOxc+XP3pFbU3h5F7Srv3FPoO1Cnh38P5iiY/pFwIDAQAB';//公钥
    const NEW_PAYGATEWAY = 'https://openapi.alipay.com/gateway.do';

    public function getStr($arr,$type = 'RSA'){
        //筛选  
        if(isset($arr['sign'])){
            unset($arr['sign']);
        }
        if(isset($arr['sign_type']) && $type == 'RSA'){
            unset($arr['sign_type']);
        }
        //排序  
        ksort($arr);
        //拼接
       return  $this->getUrl($arr,false);
    }
    //将数组转换为url格式的字符串
    public function getUrl($arr,$encode = true){
       if($encode){
            return http_build_query($arr);
       }else{
            return urldecode(http_build_query($arr));
       }
    }
    //获取签名MD5
    public function getSign($arr){
       return  md5($this->getStr($arr) . self::KEY );
    }
    //获取含有签名的数组MD5
    public function setSign($arr){
        $arr['sign'] = $this->getSign($arr);
        return $arr;
    }
    //获取签名RSA
    public function getRsaSign($arr){
       return $this->rsaSign($this->getStr($arr), self::APPPRIKEY) ;
    }
    //获取含有签名的数组RSA
    public function setRsaSign($arr){
        $arr['sign'] = $this->getRsaSign($arr);
        return $arr;
    }
    //获取签名RSA2
    public function getRsa2Sign($arr){
       return $this->rsaSign($this->getStr($arr,'RSA2'), self::APPPRIKEY,'RSA2') ;
    }
    //获取含有签名的数组RSA
    public function setRsa2Sign($arr){
        $arr['sign'] = $this->getRsa2Sign($arr);
        return $arr;
    }
    //记录日志
    public function logs($filename,$data){
        file_put_contents('./logs/' . $filename, $data . "\r\n",FILE_APPEND);
    }
    //2.验证签名
    public function checkSign($arr){
        $sign = $this->getSign($arr);
        if($sign == $arr['sign']){
            return true;
        }else{
            return false;
        }
    }
     
    //验证是否来之支付宝的通知
    public function isAlipay($arr){
        $str = file_get_contents(self::CHECKURL . $arr['notify_id']);
        if($str == 'true'){
            return true;
        }else{
            return false;
        }
    }
    // 4.验证交易状态
    public function checkOrderStatus($arr){
        if($arr['trade_status'] == 'TRADE_SUCCESS' || $arr['trade_status'] == 'TRADE_FINISHED'){
            return true;
        } else {
            return false;
        }
    }
    
    /**
      使用curl方式实现get或post请求
      @param $url 请求的url地址
      @param $data 发送的post数据 如果为空则为get方式请求
      return 请求后获取到的数据
    */
    public function curlRequest($url,$data = ''){
            $ch = curl_init();
            $params[CURLOPT_URL] = $url;    //请求url地址
            $params[CURLOPT_HEADER] = false; //是否返回响应头信息
            $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
            $params[CURLOPT_FOLLOWLOCATION] = true; //是否重定向
                    $params[CURLOPT_TIMEOUT] = 30; //超时时间
                    if(!empty($data)){
                            $params[CURLOPT_POST] = true;
                            $params[CURLOPT_POSTFIELDS] = $data;
            }
                    $params[CURLOPT_SSL_VERIFYPEER] = false;//请求https时设置,还有其他解决方案
                    $params[CURLOPT_SSL_VERIFYHOST] = false;//请求https时,其他方案查看其他博文
            curl_setopt_array($ch, $params); //传入curl参数
            $content = curl_exec($ch); //执行
            curl_close($ch); //关闭连接
                    return $content;
    }
    
}