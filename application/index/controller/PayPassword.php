<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/18 0018
 * 修改支付密码
 * Time: 14:48
 */

namespace  app\index\controller;


use think\Controller;
use think\Session;
use think\Db;
use think\Request;
class PayPassword extends  Controller{



    /**
     **************李火生*******************
     * @param Request $request
     * Notes:支付密码修改验证码
     **************************************
     * @param Request $request
     */
    public function sendMobileCodes(Request $request)
    {
        //接受验证码的手机号码
        if ($request->isPost()) {
            $mobile = $_POST["mobile"];
            $user_id = Session::get("user");
            $is_set_mobile =Db::name('user')->where('id',$user_id)->where('phone_num',$mobile)->find();
            if(empty($is_set_mobile)){
                return ajax_error("请输入登录的手机号",['status'=>0]);
            }
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




    /**
     **************李火生*******************
     * @param Request $request
     * Notes: 修改支付密码
     **************************************
     */
    public function  pay_password_update(Request $request){
        if($request->isPost()){
            $mobile =trim($_POST['phone']);
            $code =trim($_POST['phoneCode']);
            $password =trim($_POST['newPwd']);
            $password_second =trim($_POST['repeatPwd']);
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
                        $passwords =password_hash($password,PASSWORD_DEFAULT);
                        $password_bool =Db::name('user')->where('phone_num',$mobile)->update(['pay_passwd'=>$passwords]);
                        if($password_bool){
                            return ajax_success('密码修改成功',["status"=>1]);
                        }else{
                            return ajax_error('密码修改失败',["status"=>0]);
                        }
                    }
                }
            }

        }
    }
}