<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/13 0013
 * Time: 15:05
 */
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class Findpwd extends Controller{
    /**
     **************李火生*******************
     * @param Request $request
     * 用来接收密码
     * phone_number 手机号
     * code     输入的验证码
     * password     输入的密码
     **************************************
     */
    public function find_password_by_phone(Request $request){
        if($request->isPost()){
            $mobile =trim($_POST['phone_number']);
            $code =trim($_POST['code']);
            $password =trim($_POST['password']);
            $password_second =trim($_POST['password_second']);
            if($password!=$password_second){
                return ajax_error('两次密码不一致',['status'=>0]);
            }
            $data=[
                'phone_number'=>$mobile,
                'password'=>$password,
                'code'=>$code,
            ];
            if(!empty($data)){
                $res =Db::name('user')->field('phone_num')->where('phone_num',$mobile)->select();
                if(empty($res)){
                    return ajax_error('此手机号不能存在，请前往注册',$mobile);
                }else{
                    if (session('mobileCode') != $code) {
                        return ajax_error("验证码不正确",$mobile);
                    }else{
                        $passwords =md5($password);
                        $password_bool =Db::name('user')->where('phone_num',$mobile)->update(['password'=>$passwords]);
                        if(!empty($password_bool)){
                            $user_data =Db::name('user')->where('phone_num',$mobile)->find();
                            return ajax_success('密码修改成功',$user_data);
                        }else{
                            return ajax_error('密码修改失败',$password_bool);
                        }
                    }
                }
            }

        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:找回密码验证码
     **************************************
     * @param Request $request
     */
    public function sendMobileCodeByPhone(Request $request)
    {
        //接受验证码的手机号码
        if ($request->isPost()) {
            $mobile = $_POST["mobile"];
            $mobileCode = rand(100000, 999999);
            $arr = json_decode($mobile, true);
            $mobiles = strlen($arr);
            if (isset($mobiles) != 11) {
                return ajax_error("手机号码不正确");
            }
            //存入session中
            if (strlen($mobileCode)> 0){
                session('mobileCode',$mobileCode);
                session('mobile',$mobile);
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
                return ajax_error("发送失败",['status'=>0]);
            }
        }
    }

}