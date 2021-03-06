<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * Created by PhpStorm.
 * User: CHEN
 * Date: 2018/5/26
 * Time: 10:53
 */

//手机验证码
function phone($account= "",$password = '', $phone = "" ,$content = ""){
    $url = "http://120.26.38.54:8000/interface/smssend.aspx";
    $post_data = array ("account" => $account,"password" => $password,"mobile"=>$phone,"content"=>$content);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}


function objectToArray($object) {
    //先编码成json字符串，再解码成数组
    return json_decode(json_encode($object), true);
}

function arrayToObject($arr){
    if(is_array($arr)){
        return (object) array_map(__FUNCTION__, $arr);
    }else{
        return $arr;
    }
}

//生成树形图
function genTree($items,$id='id',$pid='pid',$son = 'children'){
    $tree = array(); //格式化的树
    $tmpMap = array();  //临时扁平数据

    foreach ($items as $item) {
        $tmpMap[$item[$id]] = $item;
    }

    foreach ($items as $item) {
        if (isset($tmpMap[$item[$pid]])) {
            $tmpMap[$item[$pid]][$son][] = &$tmpMap[$item[$id]];
        } else {
            $tree[] = &$tmpMap[$item[$id]];
        }
    }
    unset($tmpMap);
    return $tree;
}

/**
 * 分类排序（降序）
 */
function _tree_sort($arr,$cols){
    //子分类排序
    foreach ($arr as $k => &$v) {
        if(!empty($v['sub'])){
            $v['sub']=_tree_sort($v['sub'],$cols);
        }
        $sort[$k]=$v[$cols];
    }
    if(isset($sort))
        array_multisort($sort,SORT_ASC,$arr);
    return $arr;
}
/**
 * 横向分类树
 */
function _tree_hTree($arr,$pid=0){
    foreach($arr as $k => $v){
        if($v['pid']==$pid){
            $data[$v['id']]=$v;
            $data[$v['id']]['sub']=_tree_hTree($arr,$v['id']);
        }
    }
    return isset($data)?$data:array();
}
/**
 * 纵向分类树
 */
function _tree_vTree($arr,$pid=0){
    foreach($arr as $k => $v){
        if($v['pid']==$pid){
            $data[$v['id']]=$v;
            $data+=_tree_vTree($arr,$v['id']);
        }
    }
    return isset($data)?$data:array();
}

function ajax_error($msg = '服务器错误，可刷新页面重试',$data=array()){
    $return = array('status'=>'0');
    $return['info'] = $msg;
    exit(json_encode($return,JSON_UNESCAPED_UNICODE));
}

function ajax_success($msg = '提交成功',$data=array()){
    $return = array('status'=>'1');
    $return['info'] = $msg;
    $return['data'] = $data;
    exit(json_encode($return,JSON_UNESCAPED_UNICODE));
}

/**
 * 打印调试函数
 * @param mixed $var 打印的东西
 */
function p($var = null,$debugger = 0){
    $str = '<pre style="border:1px solid #ccc; padding:10px; font-size:16px; line-height:28px; border-radius:5px; background:#eaebe6;">%str%</pre>';
    $replace = print_r($var, true);
    if(is_null($var)){
        $replace = '__NULL__';
    }elseif(is_bool($var)){
        $var = $var === true ? 'true' : 'false';
        $replace = '(bool)'.$var;
    }elseif(is_string($var) && trim($var) === ''){
        $replace = '空';
    }
    $str = str_replace('%str%', $replace, $str);
    echo $str;
    if($debugger) exit;
}


/**
 * 将中文转换成首字母大写
 * 获取首字母
 */
function getfirstchar($s0){
    $fchar = ord($s0{0});
    if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
    $s1 = @iconv("UTF-8","gbk", $s0);
    $s2 = iconv("gbk","UTF-8", $s1);
    if($s2 == $s0){$s = $s1;}else{$s = $s0;}
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if($asc >= -20319 and $asc <= -20284) return "A";
    if($asc >= -20283 and $asc <= -19776) return "B";
    if($asc >= -19775 and $asc <= -19219) return "C";
    if($asc >= -19218 and $asc <= -18711) return "D";
    if($asc >= -18710 and $asc <= -18527) return "E";
    if($asc >= -18526 and $asc <= -18240) return "F";
    if($asc >= -18239 and $asc <= -17923) return "G";
    if($asc >= -17922 and $asc <= -17418) return "H";
    if($asc >= -17417 and $asc <= -16475) return "J";
    if($asc >= -16474 and $asc <= -16213) return "K";
    if($asc >= -16212 and $asc <= -15641) return "L";
    if($asc >= -15640 and $asc <= -15166) return "M";
    if($asc >= -15165 and $asc <= -14923) return "N";
    if($asc >= -14922 and $asc <= -14915) return "O";
    if($asc >= -14914 and $asc <= -14631) return "P";
    if($asc >= -14630 and $asc <= -14150) return "Q";
    if($asc >= -14149 and $asc <= -14091) return "R";
    if($asc >= -14090 and $asc <= -13319) return "S";
    if($asc >= -13318 and $asc <= -12839) return "T";
    if($asc >= -12838 and $asc <= -12557) return "W";
    if($asc >= -12556 and $asc <= -11848) return "X";
    if($asc >= -11847 and $asc <= -11056) return "Y";
    if($asc >= -11055 and $asc <= -10247) return "Z";
    return null;
}

/**
 * 将中文转换成首字母大写
 * 中文字符转英文字符
 */
function make_semiangle($str){
    $arr = array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', 'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'F' => 'F', 'G' => 'G', 'H' => 'H', 'I' => 'I', 'J' => 'J', 'K' => 'K', 'L' => 'L', 'M' => 'M', 'N' => 'N', 'O' => 'O', 'P' => 'P', 'Q' => 'Q', 'R' => 'R', 'S' => 'S', 'T' => 'T', 'U' => 'U', 'V' => 'V', 'W' => 'W', 'X' => 'X', 'Y' => 'Y', 'Z' => 'Z', 'a' => 'a', 'b' => 'b', 'c' => 'c', 'd' => 'd', 'e' => 'e', 'f' => 'f', 'g' => 'g', 'h' => 'h', 'i' => 'i', 'j' => 'j', 'k' => 'k', 'l' => 'l', 'm' => 'm', 'n' => 'n', 'o' => 'o', 'p' => 'p', 'q' => 'q', 'r' => 'r', 's' => 's', 't' => 't', 'u' => 'u', 'v' => 'v', 'w' => 'w', 'x' => 'x', 'y' => 'y', 'z' => 'z', '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[', '】' => ']', '〖' => '[', '〗' => ']', '“' => '"', '”' => '"', '‘' => '\'', '’' => '\'', '｛' => '{', '｝' => '}', '《' => '<', '》' => '>', '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-', '：' => ':', '。' => '.', '、' => ',', '，' => ',', '；' => ';', '？' => '?', '！' => '!', '…' => '...', '‖' => '|', '｜' => '|', '〃' => '"', '　' => ' ');
    return strtr($str, $arr);
}

/**
 * 将中文转换成首字母大写
 * 输入中文转换首字母英文
 */
function zh2pinyin($zh){
    $zh = make_semiangle($zh);
    $ret = "";
    $s1 = iconv("utf-8","gbk", $zh);
    $s2 = iconv("gbk","utf-8", $s1);
    if($s2 == $zh){$zh = $s1;}
    for($i = 0; $i < strlen($zh); $i++){
        $s1 = substr($zh,$i,1);
        $p = ord($s1);
        if($p > 160){
            $s2 = substr($zh,$i++,2);
            $ret .= getfirstchar($s2);
        }else{
            $ret .= $s1;
        }
    }
    return $ret;
}

// base64 上传图片
function base64_upload($type,$field,$callback = ''){
    $sBase64 = request($field);
    $file_name_pre = '/storage/'.$type.'/'.date('Y-m-d').'/';
    $return = array();
    if(empty($sBase64)){
        @$callback([]);
        return false;
    }
    if(!is_array($sBase64)){
        $sBase64 = array($sBase64);
    }
    foreach($sBase64 as $base64){
        if(strpos($base64 , "base64,")){
            $base64 = explode('base64,' , $base64)[1];
        }
        $base64 = base64_decode($base64);
        if(empty($base64)) continue;

        $file_name = $file_name_pre.uniqid(date('Ymd').rand('1000','9999')).'.png';
        $save_path = '.'.$file_name;
        if(!is_dir('.'.$file_name_pre)){
            mkdir('.'.$file_name_pre,0777,true);
        }
        file_put_contents($save_path,$base64);
        $return[] = $file_name;
    }
    $callback($return);
    return true;
}

/**
 * @param $files
 * @return mixed
 * 上传到文件服务器
 */
function curl_upfile($files){
    $cwd = rtrim(getcwd(), '/') . '/';

    $ch = curl_init();

    $post = array();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER,array('User-Agent: Opera/9.80 (Windows NT 6.2; Win64; x64) Presto/2.12.388 Version/12.15','Content-Type: multipart/form-data'));
    curl_setopt($ch, CURLOPT_URL,env('FILE_SERVER_URL'));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// same as <input type="file" name="file_box">
    $post = array(
        'AppId'		=> env('FILE_SERVER_APPID'),
        'SafeCode'	=> env('FILE_SERVER_SAFECODE'),
        'Thumnail'	=> '0',
//		'file_box'	=> "@".getcwd().$files,
        'image[]'	=> curl_file_create($cwd . $files),
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $response = curl_exec($ch);
    if(curl_errno($ch)){	//出错则显示错误信息
        throw new Exception('上传图片文件过大');
    }
    curl_close($ch); //关闭curl链接
    unlink($cwd . $files);
    preg_match('/<fullpath.*fullpath>/i',$response,$match);
    return strip_tags($match[0]);
}

/**
 * @param $add_status array
 * @return bool
 * 检查事务是否都是插入成功的。
 */
function checkTrans($add_status)
{
    foreach ($add_status as $v) {
        if (!$v && $v !== 0) {
            return false;
        }
        return true;
    }
}



/**
 * 查询快递信息
 * @param $com 物流公司信息，拼音
 * @param $no 快递单号
 *  常见快递公司编码：
公司名称 	公司公司编码
邮政包裹/平邮 	youzhengguonei
国际包裹 	youzhengguoji
EMS 	ems
EMS-国际件 	emsguoji
EMS-国际件 	emsinten
北京EMS 	bjemstckj
顺丰 	shunfeng
申通 	shentong
圆通 	yuantong
中通 	zhongtong
汇通 	huitongkuaidi
韵达 	yunda
宅急送 	zhaijisong
天天 	tiantian
德邦 	debangwuliu
国通 	guotongkuaidi
增益 	zengyisudi
速尔 	suer
中铁物流 	ztky
中铁快运 	zhongtiewuliu
能达 	ganzhongnengda
优速 	youshuwuliu
全峰 	quanfengkuaidi
京东 	jd
 */
function kuaidi($com , $no){
    $host = "http://express.woyueche.com";
    $path = "/query.action";
    $appcode = "ece8cce0e2e84443b684286e65965c89"; // porter的阿里云
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    //根据API的要求，定义相对应的Content-Type
    array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded;charset=UTF-8");

    $url = $host . $path;
    $result = curl_post($url , ['express'=>$com , 'trackingNo'=>$no] , $headers);
    $result =explode("\n" , $result);
    return json_decode(array_pop($result), true);
}


// curl 模拟 post 请求
function curl_post($url,$post_data = array() , $header = false){
    $ch = curl_init(); //初始化curl
    curl_setopt($ch, CURLOPT_URL, $url);//设置链接
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
    curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
    if($header){
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));//POST数据
    $response = curl_exec($ch);//接收返回信息
    if(curl_errno($ch)){	//出错则显示错误信息
        print curl_error($ch);
    }
    curl_close($ch); //关闭curl链接
    return $response;
}



function q($tip){
    if(is_object($tip) || is_string($tip)){
        echo "<pre>";
        var_dump($tip);
    }else{
        echo"<pre>";
        print_r($tip);
    }
}

function ajaxSuccess($msg = '操作成功',$url = '',$data = []){
    $return = ['status'=>1,'url'=>$url,'data'=>$data,'info'=>$msg];
    return response()->json($return);
}


function getSelectList($table , $pid = 0 ,&$result = [] , $spac = -4){
    $spac += 4;
    $list = db($table)->where(["pid"=>$pid,"status"=>1])->field("pid,id,name")->select();     //传递条件数组
    $list = objectToArray($list);
    foreach($list as $value){
        $value["name"] = str_repeat("&nbsp;",$spac).$value["name"];
        $result[] = $value;
        getSelectList($table , $value["id"] , $result , $spac);
    }
    return $result;
}

function selectList($table , $pid = 0 ,&$result = [] , $spac = -4){
    $spac += 4;
    $list = db($table)->where(["pid"=>$pid])->field("pid,id,name")->select();     //传递条件数组
    $list = objectToArray($list);
    foreach($list as $value){
        $value["name"] = str_repeat("&nbsp;",$spac).$value["name"];
        $result[] = $value;
        selectList($table , $value["id"] , $result , $spac);
    }
    return $result;
}

//递归循环
function recursionArr($arr,$pid = 0) {
    $array = [];
    foreach ($arr as $value) {
        if ($value['pid'] == $pid) {
            $value['child'] = recursionArr($arr,$value['id']);
            $array[] = $value;
        }
    }
    return $array;
}



function _tree_sorts($arr){
    //子分类排序
    foreach ($arr as $key=>$value){
        $arr[$key] = _tree_sorts($value);
    }
    return $arr;
}


function show_category($arr){
    if(!empty($arr)){
        foreach ($arr as $value){
            echo '<ul id="rootUL">';
                echo '<li data-name="ZKCH" class="parent_li" data-value='."{$value["id"]}".'><span title="关闭"><i class="icon-th icon-minus-sign">';
                echo '</i>';
                echo $value["name"];
                echo '</span>' ;
                    if($value["sub"]) {
                        show_category($value["sub"]);
                    }
                echo "</li>";
            echo '</ul>' ;
        }
    }
}

/**
 **************李火生*******************
 * @param Request $request
 * Notes:判断是后台用户配件商还是服务商（1为配件商，2为服务商）
 **************************************
 * @param $data
 */
function show_user_type($data){
    if($data==1){
        echo '配件商';
    }else if($data==2){
        echo '服务商';
    }else if($data==3) {
        echo '车主';
    }else{
        echo '其他';
    }
}
/**
 **************李火生*******************
 * @param Request $request
 * Notes:判断后台会员等级是E还是其他（1为E级）
 **************************************
 * @param $data
 */
function show_user_grade($data){
    $res =\think\Db::name('user_grade')->field('user_grade_content')->where('user_grade_types',$data)->find();
    $grade_content=$res['user_grade_content'];
    echo $grade_content;
}

/**
 **************李火生*******************
 * @param Request $request
 * Notes:后台充值提现（类型）
 **************************************
 * @param $data
 */
function show_recharge_type($data){
    if($data==1){
        echo '充值';
    }else if($data==-1){
        echo '提现';
    }else{
        echo '其他';
    }
}

/**
 **************李火生*******************
 * @param Request $request
 * Notes:后台充值提现到款状态设置
 **************************************
 * @param $data
 */
function show_recharge_status($data){
    if($data==1){
        echo '已到账';
    }else if($data==2){
        echo '未到账';
    }else{
        echo '其他';
    }
}

/**
 **************李火生*******************
 * @param Request $request
 * Notes:积分操作状态（1为获取，-1为消费）
 **************************************
 * @param $data
 */
function show_integral_type($data){
    if($data==-1){
        echo '消费';
    }else if($data==1){
        echo '获得';
    }else{
        echo '其他';
    }
}



/**
 **************李火生*******************
 * @param Request $request
 * Notes:积分操作(大于0则为+，小于0则为-，等于0不变)
 **************************************
 * @param $data
 */
function show_integral_operation($data){
    if($data>0){
        echo '+'.$data;
    }else if($data<0){
        echo $data;
    }else{
        echo '0';
    }
}


/**
 **************李火生*******************
 * @param Request $request
 * Notes:配件商订单后台显示订单状态
 **************************************
 * @param $status
 */
function show_order_status($status){
    if($status==0){
        echo '<button type="button" class="state  close-btu" >已关闭</button>';
    }else if($status==1){
        echo '<button type="button" class="state  obligation" >待支付</button>';
    }else if($status==2){
        echo '<button type="button" class="state  payment-has-been" >已付款</button>';
    }else  if($status==3){
        echo '<button type="button" class="state  shipmenting-btu" >待发货</button>';
    }else  if($status==4){
        echo '<button type="button" class="state  shipmented-btu" >已发货</button>';
    }else  if($status==5){
        echo '<button type="button" class="state  gooding-btu" >待收货</button>';
    }else  if($status==6){
        echo '<button type="button" class="state  gooded-btu" >已收货</button>';
    }else  if($status==7){
        echo '<button type="button" class="state  obligation" >待评价</button>';
    } else  if($status==8){
        echo '<button type="button" class="state  finish-btu" >已完成</button>';
    }else  if($status==9){
        echo '<button type="button" class="state  cancel-btu" >取消订单</button>';
    }else  if($status==10){
        echo '<button type="button" class="state  cancel-btu" >取消订单</button>';
    }else  if($status==11){
        echo '<button type="button" class="state  cancel-btu" >退货</button>';
    }
}

/**
 **************李火生*******************
 * @param Request $request
 * Notes:配件商订单评价判断是否回复的状态
 **************************************
 * @param $is_repay
 * @return string
 */
function show_evaluate_is_repay($is_repay){
        switch ($is_repay){
            case '0':
                return "否";
                break;
            case '1':
                return "是";
                break;
        }
}

/**
 **************李火生*******************
 * @param Request $request
 * Notes:配件商订单评价星星的状态（显示星星）
 **************************************
 * @param $is_repay
 */
function show_evaluate_start_show($is_repay){
    switch ($is_repay){
        case '1':
            echo "
                <span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars_full.png\" width=\"100%\" >
				</span>
                <span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars.png\" width=\"100%\" >
				</span>
				";
            break;
        case '2':
            echo "
                <span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars_full.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars_full.png\" width=\"100%\" >
				</span>
                <span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars.png\" width=\"100%\" >
				</span>
				";
            break;
        case '3':
            echo "
                <span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars_full.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars_full.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars_full.png\" width=\"100%\" >
				</span>
                <span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars.png\" width=\"100%\" >
				</span>
            
            ";
            break;
        case '4':
            echo "
                <span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars_full.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars_full.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars_full.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars_full.png\" width=\"100%\" >
				</span>
                <span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars.png\" width=\"100%\" >
				</span>
                    ";
            break;
        case '5':
            echo "
                <span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars_full.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars_full.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars_full.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars_full.png\" width=\"100%\" >
				</span>
				<span style=\"display: inline-block; width: 20px;\">
					<img src=\"__STATIC__/admin/common/img/stars_full.png\" width=\"100%\" >
				</span>
            ";
            break;
    }
}

/**
 **************李火生*******************
 * @param $is_pay
 * 判断店铺是否付费上架
 **************************************
 */
function store_is_pay($is_pay){
    if($is_pay==1){
        echo '付费';
    }else if ($is_pay==-1){
        echo '未付费';
    }else{
        echo  '待操作员确定';
    }
}

/**
 **************李火生*******************
 * @param $status
 * 店铺的平台审核状态
 **************************************
 */
function operation_status($status){
    if($status==1){
        echo '通过';
    }elseif ($status==-1){
        echo '<span class="gray">拒绝</span>';
    }else{
        echo '<span class="red">待审核</sapn>';
    }
}

/**
 **************李火生*******************
 * @param Request $request
 * Notes:服务商订单状态值
 **************************************
 * @param $status
 */
function show_service_order_status($status){
    if($status==0){
        echo '<button type="button" class="state  close-btu" >已关闭</button>';
    }else if($status==1){
        echo '<button type="button" class="state  obligation" >待支付</button>';
    }else if($status==2){
        echo '<button type="button" class="state  payment-has-been" >已付款</button>';
    }else  if($status==3){
        echo '<button type="button" class="state  shipmenting-btu" >待服务</button>';
    }else  if($status==4){
        echo '<button type="button" class="state  shipmented-btu" >已服务</button>';
    }else  if($status==5){
        echo '<button type="button" class="state  obligation" >待评价</button>';
    } else  if($status==6){
        echo '<button type="button" class="state  finish-btu" >已完成</button>';
    }else  if($status==9){
        echo '<button type="button" class="state  cancel-btu" >取消订单</button>';
    }else  if($status==10){
        echo '<button type="button" class="state  cancel-btu" >取消订单</button>';
    }
}


/**
 **************李火生*******************
 * @param Request $request
 * Notes:生成我的邀请码
 **************************************
 * @param $user_id
 * @return string
 */
function createCode($user_id) {
    static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
    $num = $user_id;
    $code = '';
    while ( $num > 0) {
        $mod = $num % 35;
        $num = ($num - $mod) / 35;
        $code = $source_string[$mod].$code;
    }
    if(empty($code[3]))
        $code = str_pad($code,6,'0',STR_PAD_LEFT);
   return $code;
}

/**
 **************李火生*******************
 * @param Request $request
 * Notes:邀请码解析
 **************************************
 * @param $code
 * @return bool|int
 */
function qr_decode($code) {
    static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
    if (strrpos($code, '0') !== false)
        $code = substr($code, strrpos($code, '0')+1);
    $len = strlen($code);
    $code = strrev($code);
    $num = 0;
    for ($i=0; $i < $len; $i++) {
        $num += strpos($source_string, $code[$i]) * pow(35, $i);
    }
    return $num;
}

/**
 **************李火生*******************
 * @param Request $request
 * Notes:提现审核状态
 **************************************
 * @param $status
 */
function operation_recharge_status($status){
    if($status==1){
        echo '通过';
    }elseif ($status==-1){
        echo '<span class="gray">不通过</span>';
    }elseif($status==2){
        echo '<span class="red">待审核</sapn>';
    }
}




//数组去重
function unique_arr($array2D,$stkeep=false,$ndformat=true)
{
    // 判断是否保留一级数组键 (一级数组键可以为非数字)
    if($stkeep) $stArr = array_keys($array2D);
    // 判断是否保留二级数组键 (所有二级数组键必须相同)
    if($ndformat) $ndArr = array_keys(end($array2D));
    //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
    foreach ($array2D as $v){
        $v = join(",",$v);
        $temp[] = $v;
    }

    //去掉重复的字符串,也就是重复的一维数组
    $temp = array_unique($temp);
    //再将拆开的数组重新组装
    foreach ($temp as $k=>$v){
        if($stkeep) $k = $stArr[$k];
        if($ndformat)
        {
            $tempArr = explode(",",$v);
            foreach($tempArr as $ndkey => $ndval) {
                $output[$k][$ndArr[$ndkey]] = $ndval;
            }
        }else{
            $output[$k] = explode(",",$v);
        }

    }
    return $output;
}

//平台广告显示顺序
function foreach_plater($data){
    $qq = array();
    $ww = array();
    $ee = array();
    $rr = array();

    foreach($data as $pp => $z){
        if( $z["status"] == 2){ //待审核
            $qq[] = $z;                                               
        }
        if($z["status"]== 1){ //通过                 
            $ww[] = $z;                                               
        }
        if($z["status"]== 4){ // 拒绝
            $ee[] = $z;                                             
        } 
        if($z["status"]== 3){ // 已过期
            $rr[] = $z;                                   
        }        
    }
    $home = array_merge($qq,$ww,$ee,$rr);
    return $home; 
}
//平台广告位置排序
function foreach_pid($arr){
    $aa = array();
    $bb = array();
    $cc = array();
    $dd = array();
    $ff = array();

    foreach($arr as $key => $value){
        if($value["pid"] == 18){ //首页轮播  array_value_exists($value,18)
            $aa[] = $value;                                                          
        }        
        if($value["pid"]== 19){//首页固定               
            $bb[] =$value;                                               
        } 

        if($value["pid"]== 20){ //热门推荐
            $cc[] = $value;                                             
        }

        if($value["pid"]== 21){ //配件商城
            $dd[] = $value;                                   
        } 

        if($value["pid"]== 27){ //配件商城优惠推荐
            $ff[] = $value;                                   
        } 
        
    }
    $a = foreach_plater($aa);
    $b = foreach_plater($bb);
    $c = foreach_plater($cc);
    $d = foreach_plater($dd);
    $f = foreach_plater($ff);
    $rest = array_merge($a,$b,$c,$d,$f);
    return $rest; 
}



 function rest($we){
    foreach($we as $e => $r){
    preg_match_all("/http:[\/]{2}[0-9]*/",$r['advert_text'],$we[$e]['goods_id']);
    $we[$e]['goods_id'] = $we[$e]['goods_id'][0];
    if(count($we[$e]['goods_id']) == 0){
        unset($we[$e]['goods_id']);
        }
    } 
    foreach($we as $y => $s){
        if(isset($s['goods_id'])){      
        foreach($s['goods_id'] as $n => $m){
            $we[$y]['interlinkage'][$n] = array(
                'goods_id'=> intval(str_replace("http://","",$s['goods_id'][$n]))-1000000,
                'store_id'=> db("goods")-> where("id",intval(str_replace("http://","",$s['goods_id'][$n]))-1000000) -> value("store_id"),
                'goods_brand_id' => db("goods")-> where("id",intval(str_replace("http://","",$s['goods_id'][$n]))-1000000) -> value("goods_brand_id")

            );
          }
        }
    }

    return $we;
    
    
}