<?php

class Wxpayandroid
{
    //参数配置
    public $config = array(
        'appid' => "wxfa30dbf87b781811", /*微信开放平台上的应用id*/
        'mch_id' => "1523937771", /*微信申请成功之后邮件中的商户id*/
        'api_key' => "Zlh188cnZwxcqgzyszunlianhuiappZy", /*在微信商户平台上自己设定的api密钥 32位*/
    );
    //服务器异步通知页面路径(必填)
    public $notify_url;
    //商户订单号(必填，商户网站订单系统中唯一订单号)
    public $out_trade_no;
    //商品描述(必填，不填则为商品名称)
    public $body;
    //付款金额(必填)
    public $total_fee = 0;
    //自定义超时(选填，支持dhmc)
    public $time_expire = '';
    private $WxPayHelper;
    public function __construct($total_fee,$tade_no,$goods_pay_money){
        $this->total_fee = intval($total_fee * 100);//订单的金额 1元
        $this->out_trade_no = $tade_no;   //订单号
        $this->body = $goods_pay_money;  //支付描述信息
        $this->time_expire = date('YmdHis', time() + 7200);//订单支付的过期时间(eg:一天过期)
        $this->notify_url = "automobile.siring.com.cn/wxpay_notifyurl";//异步通知URL(更改支付状态)
        //数据以JSON的形式返回给APP
        $app_response = $this->doPay();
            if (isset($app_response['return_code']) && $app_response['return_code'] == 'FAIL') {
            $errorCode = 100;
            $errorMsg = $app_response['return_msg'];
            $this->echoResult($errorCode, $errorMsg);
            } else {
            $errorCode = 0;
            $errorMsg = 'success';
            $responseData = array(
            'notify_url' => $this->notify_url,
            'app_response' => $app_response,
            );
            $this->echoResult($errorCode, $errorMsg, $responseData);
            }
    }
    //接口输出
    function echoResult($errorCode = 0, $errorMsg = 'success', $responseData = array())
    {
        $arr = array(
        'errorCode' => $errorCode,
        'errorMsg' => $errorMsg,
        'responseData' => $responseData,
        );
        exit(json_encode($arr));  //exit可以正常发送给APP json数据
        // return json_encode($arr); //在TP5中return这个json数据，APP接收到的是null,无法正常吊起微信支付
    }

    function getVerifySign($data, $key)
    {
    $String = $this->formatParameters($data, false);
    //签名步骤二：在string后加入KEY
    $String = $String . "&key=" . $key;
    //签名步骤三：MD5加密
    $String = md5($String);
    //签名步骤四：所有字符转为大写
    $result = strtoupper($String);
    return $result;
    }
    function formatParameters($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
        if($k=="sign"){
            continue;
        }
        if ($urlencode) {
            $v = urlencode($v);
        }
        $buff .= $k . "=" . $v . "&";
    }
        $reqPar;
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
            return $reqPar;
        }
    /**
    * 得到签名
    * @param object $obj
    * @param string $api_key
    * @return string
    */
    private function getSign($params) {
        //签名步骤一：按字典序排序参数
        ksort($params);
        $string = $this->ToUrlParams($params);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$this->config['api_key'];
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }



    /**
     * 发送给app签名
     * @param object $obj
     * @param string $api_key
     * @return string
     */
    private function getSigns($params) {
        //签名步骤一：按字典序排序参数
        ksort($params);
        $string = $this->formatBizQueryParaMap($params,false);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$this->config['api_key'];
        halt($string);
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }


    /**
    * 获取指定长度的随机字符串
    * @param int $length
    * @return Ambigous <NULL, string>
    */
    function getRandChar($length){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
        for($i=0;$i<$length;$i++){
        $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }
    /**
    * 数组转xml
    * @param array $arr
    * @return string
    */
    function arrayToXml($arr)
    {
        $xml = "<xml>";
            foreach ($arr as $key=>$val)
            {
            if (is_numeric($val))
            {
            $xml.="<".$key.">".$val."</".$key.">";
        } else
        $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }
    /**
    * 以post方式提交xml到对应的接口url
    *
    * @param string $xml 需要post的xml数据
    * @param string $url url
    * @param bool $useCert 是否需要证书，默认不需要
    * @param int $second url执行超时时间，默认30s
    * @throws WxPayException
    */
    function postXmlCurl($xml, $url, $second=30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            file_put_contents(EXTEND_PATH."WxpayAPI/data.txt",$data);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return false;
        }

    }
    /**
    * 获取当前服务器的IP
    * @return Ambigous <string, unknown>
    */
    function get_client_ip()
    {
        if (isset($_SERVER['REMOTE_ADDR'])) {
        $cip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv("REMOTE_ADDR")) {
        $cip = getenv("REMOTE_ADDR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
        $cip = getenv("HTTP_CLIENT_IP");
        } else {
        $cip = "127.0.0.1";
        }
        return $cip;
    }
    /**
    * 将数组转成uri字符串
    * @param array $paraMap
    * @param bool $urlencode
    * @return string
    */

    public function ToUrlParams($params)
    {
        $buff = "";
        foreach ($params as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }





    /**
     *  作用：格式化参数，签名过程需要使用
     */
    public function formatBizQueryParaMap($paraMap, $urlencode){
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v){
            if($urlencode){
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0){
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }




    /**
    * XML转数组
    * @param unknown $xml
    * @return mixed
    */
    function xmlToArray($xml)
    {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    public function chkParam()
    {
        //用户网站订单号
        if (empty($this->out_trade_no)) {
         die('out_trade_no error');
        }
        //商品描述
        if (empty($this->body)) {
            die('body error');
        }
        if (empty($this->time_expire)){
            die('time_expire error');
        }
        //检测支付金额
        if (empty($this->total_fee) || !is_numeric($this->total_fee)) {
            die('total_fee error');
        }
        //异步通知URL
        if (empty($this->notify_url)) {
            die('notify_url error');
        }
        if (!preg_match("#^http:\/\/#i", $this->notify_url)) {
            $this->notify_url = "http://" . $this->notify_url;
        }
            return true;
    }
    /**
    * 生成支付(返回给APP)
    * @return boolean|mixed
    */
    public function doPay() {
        //检测构造参数
        $this->chkParam();
        return $this->createAppPara();
    }
    /**
    * APP统一下单
    */
    private function createAppPara()
    {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $data["appid"]  = $this->config['appid'];//微信开放平台审核通过的应用APPID
        $data["body"]   = $this->body;//商品或支付单简要描述
        $data["mch_id"]  = $this->config['mch_id'];//商户号
        $data["nonce_str"] = $this->getRandChar(32);//随机字符串
        $data["notify_url"] = $this->notify_url;//通知地址
        $data["out_trade_no"] = $this->out_trade_no;//商户订单号
        $data["spbill_create_ip"] = $this->get_client_ip();//终端IP
        $data["total_fee"]  = $this->total_fee;//总金额
        $data["time_expire"]  = $this->time_expire;//交易结束时间
        $data["trade_type"]  = "APP";//交易类型
        $data["sign_type"] = "MD5";
        $data["sign"]    = $this->getSign($data);//签名
        $xml  = $this->arrayToXml($data);
        $response = $this->postXmlCurl($xml, $url);
        //将微信返回的结果xml转成数组
        $responseArr = $this->xmlToArray($response);
        if(isset($responseArr["return_code"]) && $responseArr["return_code"]=='SUCCESS'){
            return $this->getOrder($responseArr['prepay_id'],$responseArr["sign"]);
        }
        return $responseArr;
    }
    /**
    * 执行第二次签名，才能返回给客户端使用
    * @param int $prepayId:预支付交易会话标识
    * @return array
    */
    public function getOrder($prepayId,$sign)
    {
        $data["appid"]  = $this->config['appid'];
        $data["noncestr"] = $this->getRandChar(32);
        $data["partnerid"] = $this->config['mch_id'];
        $data["prepayid"] = $prepayId;
        $data["timestamp"] = time();
        //$data["sign"]  = $sign;
        $data["packagevalue"] = "Sign=WXPay";
        //$data["api_key"] = $this->config['api_key'];
        $data["sign"]  =  $this->getSigns($data);//签名
        return $data;
    }
    /**
    * 异步通知信息验证
    * @return boolean|mixed
    */
    public function verifyNotify()
    {
            $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
            if(!$xml){
                return false;
            }
            $wx_back = $this->xmlToArray($xml);
            if(empty($wx_back)){
                return false;
            }
            $checkSign = $this->getVerifySign($wx_back, $this->config['api_key']);
        if($checkSign=$wx_back['sign']){
        return $wx_back;
        }else{
        return false;
        }
    }
}