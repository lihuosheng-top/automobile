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
                    return ajax_error('此手机号已经注册',['status'=>0]);
                }
                $mobileCode = rand(100000, 999999);
                $arr = json_decode($mobile, true);
                $mobiles = strlen($arr);
                if (isset($mobiles) != 11) {
                    return ajax_error("手机号码不正确",['status'=>0]);
                }
                //存入session中
                if (strlen($mobileCode)> 0) {
                    session('mobileCode',$mobileCode);
                    $_SESSION['mobile'] = $mobile;
                }
                $content = "尊敬的用户，您本次验证码为{$mobileCode}，十分钟内有效";
                $url = "http://120.26.38.54:8000/interface/smssend.aspx";
                $post_data = array("account" => "gagaliang", "password" => "123qwe", "mobile" => "$mobile", "content" => $content);
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
                    return ajax_error("发送失败",['status'=>0]);
                }
            }else{
                return ajax_error("请填写正确的手机号",['status'=>0]);
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
            $mobile = trim($_POST['mobile']);
            $code = trim($_POST['mobile_code']);
            $password =trim($_POST['password']);
            $confirm_password =trim($_POST['confirm_password']);
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
                $datas =[
                    'phone_num'=>$mobile,
                    'password'=>md5($password),
                    'create_time'=>strtotime($create_time),
                    "status"=>1,
                ];
                $invitation = $request->only(['invitation'])['invitation'];
                if(!empty($invitation)) {
                    $is_user_id =Db::name('user')->where('id',$invitation)->select();
                    if(!empty($is_user_id)){
                        $datas['inviterId']=$invitation;
                        $res =Db::name('user')->insert($datas);
                        if($res){
                            return ajax_success('注册成功',$datas);
                        }else{
                            return ajax_error('注册失败',['status'=>0]);
                        }
                    }else{
                        return ajax_error('邀请码不正确',['status'=>0]);
                    }
                }else{
                    $res =Db::name('user')->insert($datas);
                    if($res){
                        return ajax_success('注册成功',$datas);
                    }else{
                        return ajax_error('注册失败',['status'=>0]);
                    }
                }

            }
        }
    }



}