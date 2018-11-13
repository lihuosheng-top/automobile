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
                return ajax_error('手机号不存在',['status'=>0]);
            }
            $datas=[
                "phone_num" => $user_mobile,
                "password" => md5($password),
            ];
            $res =Db::name('user')->where($datas)->find();
            if(!$res && $res == null){
                return ajax_error('密码错误',['status'=>0]);
            }
            if($res){
                $res =Db::name('user')->where($datas)->where('status',1)->find();
                if($res)
                {
                    session('member',$datas);
                    return ajax_success('登录成功',$datas);
                }else{
                    ajax_error('此用户已被管理员设置停用',$datas);
                }
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
          return ajax_success('退出成功',['status'=>1]);
       }
    }
}