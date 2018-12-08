<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use  think\Session;

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
     **************李火生*******************
     * @param Request $request
     * Notes:用户个人信息更新
     * 头像,真实姓名，昵称，性别
     **************************************
     */
    public function member_information_update(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");//用户id
            //头像
            $user_img = $request->file('user_img');
            if(!empty($user_img)){
                $info = $user_img->move(ROOT_PATH . 'public' . DS . 'userimg');
                $user_img_url = str_replace("\\","/",$info->getSaveName());
                $data['user_img'] =$user_img_url;
                $del_user_img_url= Db::name("user")->field('user_img')->where('id',$user_id)->find();
            }
            //真实姓名
            $real_name =$request->only('real_name')['real_name'];
            if(!empty($real_name)){
                $data['real_name']=$real_name;
            }
            //昵称
            $user_name =$request->only('user_name')['user_name'];
            if(!empty($user_name)){
                $data['user_name']=$user_name;
            }
            //性别
            $sex =$request->only('sex')['sex'];
            if(!empty($sex)){
                $data['sex']=$sex;
            }
            if(!empty($data)){
               $bool = Db::name("user")->where('id',$user_id)->update($data);
                if($bool){
                    //删除头像
                    if($del_user_img_url['user_img'] !=null){
                        unlink(ROOT_PATH . 'public' . DS . 'userimg/'.$del_user_img_url['user_img']);//更换头像的时候删了
                    }
                    //管理员列表真实姓名
                    if(!empty($real_name)){
                        $phone_num =Db::name("user")->field('phone_num')->where("id",$user_id)->find();
                        $is_set_admin =Db::name("admin")->where("phone",$phone_num['phone_num'])->find();
                        if(!empty($is_set_admin)){
                            Db::name("admin")->where("phone",$phone_num['phone_num'])->update(['name'=>$real_name]);
                        }
                    }
                    //性别
                    if(!empty($sex)){
                        $phone_num =Db::name("user")->field('phone_num')->where("id",$user_id)->find();
                        $is_set_admin =Db::name("admin")->where("phone",$phone_num['phone_num'])->find();
                        if(!empty($is_set_admin)){
                            Db::name("admin")->where("phone",$phone_num['phone_num'])->update(['sex'=>$sex]);
                        }
                    }
                    return ajax_success('更新成功',['status'=>1]);
                }else {
                    return ajax_error('更新失败', ['status' => 0]);
                }
            }else{
                return ajax_error('所修改值不能为空',['status'=>0]);
            }

        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:修改手机
     **************************************
     * @param Request $request
     */
    public function member_update_mobile(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");
            $phone_num =$request->only('phone_num')['phone_num'];
            $code =$request->only('code')['code'];
           $mobileCode =  session('mobileCode');
            if(!empty(trim($code))){
                $user_isset =Db::name('user')->where('phone_num',$phone_num)->find();
                if(empty($user_isset)){
                    return ajax_error('用户不存在',['status'=>0]);
                }
                if($code != $mobileCode){
                    return ajax_error('验证码不正确',['status'=>0]);
                }
                $is_admin =Db::name('admin')->field('id')->where('phone',$phone_num)->find();
                if(!empty($is_admin)){
                    Db::name('admin')->where('id',$is_admin['id'])->update(['phone'=>$phone_num]);
                }
                $bool =Db::name('user')->where('user_id',$user_id)->update(['phone_num'=>$phone_num]);
                if($bool){
                    return ajax_success('修改成功',['status'=>1]);
                }else{
                    return ajax_error('修改失败',['status'=>0]);
                }
            }
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:用户个人信息数据返回
     **************************************
     */
    public function member_information_data(Request $request){
            if($request->isPost()){
                $user_id =Session::get("user");//用户id
                $data =Db::name("user")->where('id',$user_id)->find();
                if(!empty($data)){
                    if(!empty($data['user_img'])){
                        return ajax_success('信息返回成功',$data);
                    }
                }else{
                    return ajax_success('用户不存在');
                }
            }
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
