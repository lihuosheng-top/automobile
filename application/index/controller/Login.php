<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/6
 * Time: 15:59
 */

namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Session;

class Login extends Controller{


    /**
     * 注册首页
     */
    public function index(){
       return view("login_index");
    }



    /**
     **************李火生*******************
     * @param Request $request
     * Notes:登陆操作
     **************************************
     * @param Request $request
     */
    public function Dolog(Request $request){
        if($request->isGet()){
            $data = $_GET;
            $user_mobile =$data['account'];
            $password =$data['passwd'];
            if(empty($user_mobile)){
                return  ajax_error('手机号不能为空',$user_mobile);
            }
            if(empty($password)){
                return ajax_error('密码不能为空',['status'=>0]);
            }
            $res = Db::name('user')->field('password')->where('phone_num',$user_mobile)->find();
            if(!$res)
            {
                $is_express =Db::name("delivery")->where("account",$user_mobile)->find();
               if(!$is_express){
                   return ajax_error('手机号不存在',['status'=>0]);
               }else{
                   if(password_verify($password , $is_express["passwords"])){
                        Session::set("delivery_id",$is_express["id"]);
                       exit(json_encode(array("status" => 2, "info" => "登录成功")));
                   }else{
                       return ajax_error('密码错误',['status'=>0]);
                   }
               }
            }
            $datas =[
                'phone_num'=> $user_mobile,
            ];
            if(password_verify($password , $res["password"])){
                if($res){
                    $ress =Db::name('user')->where('phone_num',$user_mobile)->where('status',1)->field("id")->find();
                    if($ress)
                    {
                        Session::set("user",$ress["id"]);
                        Session::set('member',$datas);
                        return ajax_success('登录成功',$datas);
                    }else{
                        ajax_error('此用户已被管理员设置停用',$datas);
                    }
                }
            }else{
                return ajax_error('密码错误',['status'=>0]);
            }

        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:退出操作
     **************************************
     */
    public function logout(Request $request){
       if($request->isPost()){
          Session('member',null);
          Session::delete("user");//用户推出
          Session::delete("role_name_store_id");//商家退出
          return ajax_success('退出成功',['status'=>1]);
       }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:第三方快捷登录绑定手机
     **************************************
     * @param Request $request
     */
    public function user_bind_phone(Request $request){
        if($request->isPost()){
            $phone_number =$request->only("phone_number")["phone_number"];//手机号码

        }
        return view("user_bind_phone");
    }


}
