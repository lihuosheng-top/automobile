<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/27
 * Time: 13:53
 */
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;

class Register extends Controller{

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:h5端注册页面
     **************************************
     * @return \think\response\View
     */
    public function index(){
        return view("index");
    }



    /**
     **************李火生*******************
     * @param Request $request
     * Notes:注册验证码
     **************************************
     * @param Request $request
     */
    public function sendMobileCode(Request $request)
    {
        //接受验证码的手机号码
        if ($request->isPost()) {
            $mobile = $request->only(['mobile'])['mobile'];
            $pattern = '/^1[3456789]\d{9}$/';
            if(preg_match($pattern,$mobile)) {
                $res =  Db::name('user')->field('phone_num')->where('phone_num',$mobile)->select();
                if($res){
                    return ajax_error('此手机号已经注册',0);
                }
                $mobileCode = rand(100000, 999999);
                $arr = json_decode($mobile, true);
                $mobiles = strlen($arr);
                if (isset($mobiles) != 11) {
                    return ajax_error("手机号码不正确",0);
                }
                //存入session中
                if (strlen($mobileCode)> 0) {
                    session('mobileCode',$mobileCode);
                    $_SESSION['mobile'] = $mobile;
                }
                $content = "尊敬的用户，您本次验证码为{$mobileCode}，十分钟内有效";
                $url = "http://120.26.38.54:8000/interface/smssend.aspx";
                $post_data = array("account" => "qiche", "password" => "123qwe", "mobile" => "$mobile", "content" => $content);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                $output = curl_exec($ch);
                curl_close($ch);
                if ($output) {
                    return ajax_success("发送成功", $output);
                } else {
                    return ajax_error("发送失败",0);
                }
            }else{
                return ajax_error("请填写正确的手机号",0);
            }
            }
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:注册操作
     **************************************
     * @param Request $request
     */
    public function  doRegByPhone(Request $request){
        if($request->isPost())
        {
            $mobile = $request->only(['mobile'])['mobile'];
            $is_reg =Db::name("user")->where("phone_num",$mobile)->find();
            if(!empty($is_reg)){
                return ajax_error("此手机已注册，可以直接登录");
            }
            $code = $request->only(['mobile_code'])['mobile_code'];
            $password = $request->only(['password'])['password'];
            $confirm_password = $request->only(['confirm_password'])['confirm_password'];
            $create_time =date('Y-m-d H:i:s');
            if($password !==$confirm_password ){
                return ajax_error('两次密码不相同');
            }
            if (strlen($mobile) != 11 || substr($mobile, 0, 1) != '1' || $code == '') {
                return ajax_error("参数不正确");
            }
            if (session('mobileCode') != $code || $mobile != $_SESSION['mobile']) {
                return ajax_error("验证码不正确");
            } else {
                $passwords =password_hash($password,PASSWORD_DEFAULT);
                $invitation = $request->only(['invitation'])['invitation']; //邀请码
                $datas =[
                    'phone_num'=>$mobile,
                    'password'=>$passwords,
                    'create_time'=>strtotime($create_time),
                    "status"=>1,
                ];
                if(!empty($invitation)) {
                   $invitation = intval(qr_decode($invitation));
                    $is_user_id =Db::name('user')->where('id',$invitation)->select();
                    if(!empty($is_user_id)){
                        //如果有邀请码存在
                        $datas['inviterId']=$invitation;
                        $res =Db::name('user')->insertGetId($datas);
                        if($res){
                            //有邀请码注册成功（推荐人获取积分）
                            $recommend_integral =Db::name("recommend_integral")
                                ->where("id",1)
                                ->value("recommend_integral");
                            if($recommend_integral>0){
                                //推荐人的原账户积分
                                $old_integral_wallet = Db::name("user")
                                    ->where("id",$invitation)
                                    ->value("user_integral_wallet");
                                //推荐人的积分添加
                               $add_res = Db::name("user")
                                    ->where("id",$invitation)
                                    ->update(["user_integral_wallet"=>$old_integral_wallet+$recommend_integral]);
                               if($add_res){
                                   //余额添加成功(做积分消费记录)
                                   //插入积分记录
                                   $integral_data =[
                                       "user_id"=>$invitation,
                                       "integral_operation"=>$recommend_integral,//获得积分
                                       "integral_balance"=>$recommend_integral+$old_integral_wallet,//积分余额
                                       "integral_type"=>1, //积分类型（1获得，-1消费）
                                       "operation_time"=>date("Y-m-d H:i:s"), //操作时间
                                       "integral_remarks"=>"成功推荐一位用户注册送".$recommend_integral."积分",
                                   ];
                                   Db::name("integral")->insert($integral_data);
                               }
                            }
                            //注册成功送的积分（自己获取积分）
                            $send_integral =Db::name("recommend_integral")->where("id",1)->value("register_integral");
                            $inv_num = createCode($res);
                           $inv =[
                              "invitation"=>$inv_num,//生成邀请码
                               "user_name"=>"QC".$inv_num, //默认用户名
                               "user_integral_wallet"=>$send_integral,//注册的积分
                            ];                         //生成邀请码
                            Db::name("user")->where("id",$res)->update($inv);
                            if($send_integral>0){
                                //插入积分记录
                                $integral_data =[
                                    "user_id"=>$res,
                                    "integral_operation"=>$send_integral,//获得积分
                                    "integral_balance"=>$send_integral,//积分余额
                                    "integral_type"=>1, //积分类型（1获得，-1消费）
                                    "operation_time"=>date("Y-m-d H:i:s"), //操作时间
                                    "integral_remarks"=>"成功注册送".$send_integral."积分",
                                ];
                                Db::name("integral")->insert($integral_data);
                            }
                            return ajax_success('注册成功',$datas);
                        }else{
                            return ajax_error('请重新注册',['status'=>0]);
                        }
                    }else{
                        return ajax_error('邀请码不正确',['status'=>0]);
                    }
                }else{
                    //无邀请码
                    $res =Db::name('user')->insertGetId($datas);
                    if($res){
                        //如果注册成功（自己获取积分）
                        $send_integral =Db::name("recommend_integral")->where("id",1)->value("register_integral");
                        $inv_num = createCode($res);
                        $inv =[
                            "invitation"=>$inv_num,//生成邀请码
                            "user_name"=>"QC".$inv_num, //默认用户名
                            "user_integral_wallet"=>$send_integral,//注册的积分
                        ];
                        Db::name("user")->where("id",$res)->update($inv);
                        if($send_integral>0){
                            //插入积分记录
                            $integral_data =[
                                "user_id"=>$res,
                                "integral_operation"=>$send_integral,//获得积分
                                "integral_balance"=>$send_integral,//积分余额
                                "integral_type"=>1, //积分类型（1获得，-1消费）
                                "operation_time"=>date("Y-m-d H:i:s"), //操作时间
                                "integral_remarks"=>"成功注册送".$send_integral."积分",
                            ];
                            Db::name("integral")->insert($integral_data);
                        }
                        return ajax_success('注册成功',$datas);
                    }else{
                        return ajax_error('请重新注册',['status'=>0]);
                    }
                }

            }
        }
    }


}