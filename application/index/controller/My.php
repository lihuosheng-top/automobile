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
                   $return_data =Db::table('tb_user')
                       ->field('tb_user.*,tb_user_grade.user_grade_content user_grade_content')
                       ->join("tb_user_grade","tb_user.user_grade=tb_user_grade.grade_id",'left')
                       ->find();
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




    /**
     * 我的消费
     * 陈绪
     */
    public function consume(){
        return view("my_consume");

    }




    /**
     * 个人信息
     * 陈绪
     */
    public function message(){
        return view("my_message");
    }
    




    /**
     * 修改手机号码
     * 陈绪
     */
    public function phone_edit(){
        return view("phone_edit");

    }



    /**
     * 真实姓名
     * 陈绪
     */
    public function true_name(){
        return view("true_name");

    }



    /**
     * 昵称
     * 陈绪
     */
    public function nickname(){
        return view("my_nickname");

    }
    
    
    /**
     * 昵称
     * 陈绪
     */
    public function integral(){
        return view("my_integral");

    }



    /**
     * 消费详情
     * 陈绪
     */
    public function consume_message(){
        return view("consume_message");

    }
}
