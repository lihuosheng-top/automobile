<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class My extends Controller
{
    public function My_index()
    {
        return view("my_index");
    }
    // 登录
    public function Login()
    {
        return view("login");
    }

    // 设置
    public function Setting()
    {
        return view("setting");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:判断是否登录
     **************************************
     * @param Request $request
     */
    public function isLogin(Request $request){
        if($request->isPost()){
            $member_data =session('member');
            if(!empty($member_data)){
               $phone_num = $member_data['phone_num'];
               if(!empty($phone_num)){
                   $return_data =Db::name('user')->field('user_name,user_img,id,user_grade,user_wallet,user_integral_wallet')->find();
                   if(!empty($return_data)){
                       return ajax_success('用户信息返回成功',$return_data);
                   }else{
                       return ajax_error('没有该用户信息',['status'=>0]);
                   }
               }
            }else{
                return ajax_error('请前往登录',['status'=>0]);
            }
        }
    }
}
