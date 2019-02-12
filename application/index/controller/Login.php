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
                //快递员
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
    public function user_bind_phone(Request $request)
    {
        if ($request->isPost()) {
            $open_id =trim($_POST['id']);
            $is_wechat =trim($_POST['is_wechat']); // (1为微信，2为qq)
            $mobile = trim($_POST['mobile']);//手机号码
            $is_reg = Db::name("user")->where("phone_num", $mobile)->find();
            if (!empty($is_reg)) {
                return ajax_error("此手机已注册，可以直接登录");
            }
            $code = trim($_POST['mobile_code']);
            $password = trim($_POST['password']);
            $create_time = date('Y-m-d H:i:s');
            if($is_wechat==1){
                $boolss =Db::name("wechat")->where("open_id",$open_id)->find();
                if($boolss){
                    //如果qq存在则进行直接授权登录
                    $user_id = Db::name("wechat")->where("open_id",$open_id)->value("user_id");
                    $user_res =Db::name('user')->where('id',$user_id)->where('status',1)->field("id,phone_num")->find();
                    if($user_res)
                    {
                        $user_data =[
                            'phone_num'=> $user_res,
                        ];
                        Session::set("user",$user_res["id"]);
                        Session::set('member',$user_data);
                        $this->redirect("my_index");
                        return ajax_success('登录成功',$user_data);
                    }else{
                         ajax_error('此用户已被管理员设置停用');
                    }
//                    return ajax_error('此微信号已绑定过', ['status' => 0]);
                }
            }else if($is_wechat==2){
                $boolss =Db::name("qq")->where("open_id",$open_id)->find();
                if($boolss){
                    //如果qq存在则进行直接授权登录
//                    return ajax_error('此qq号已绑定过', ['status' => 0]);
                }
            }
            if (strlen($mobile) != 11 || substr($mobile, 0, 1) != '1' || $code == '') {
                return ajax_error("手机号格式错误，请重填");
            }
            if (session('mobileCode') != $code || $mobile != $_SESSION['mobile']) {
                return ajax_error("验证码不正确");
            } else {
                $passwords = password_hash($password, PASSWORD_DEFAULT);
                $datas = [
                    'phone_num' => $mobile,
                    'password' => $passwords,
                    'create_time' => strtotime($create_time),
                    "status" => 1,
                ];
                $res = Db::name('user')->insertGetId($datas);
                    //无邀请码
                    if ($res) {
                        //插入到微信或者qq快捷登录（1为微信，2为Qq）
                        if($is_wechat==1){
                            $boolss =Db::name("wechat")->where("open_id",$open_id)->find();
                            if($boolss){
                                return ajax_error('微信号也绑定过', ['status' => 0]);
                            }
                            $bools =Db::name("wechat")->insertGetId(["open_id"=>$open_id,"user_id"=>$res]);
                            $result = Db::name('user')->where("id",$res)->update(["wechat_id"=>$bools]);
                        }else if($is_wechat==2){
                            $boolss =Db::name("qq")->where("open_id",$open_id)->find();
                            if($boolss){
                                return ajax_error('qq号也绑定过', ['status' => 0]);
                            }
                            $bools =Db::name("qq")->insertGetId(["open_id"=>$open_id,"user_id"=>$res]);
                            $result = Db::name('user')->where("id",$res)->update(["qq_id"=>$bools]);
                        }
                        //如果注册成功（自己获取积分）
                        $send_integral = Db::name("recommend_integral")->where("id", 1)->value("register_integral");
                        $inv_num = createCode($res);
                        $inv = [
                            "invitation" => $inv_num,//生成邀请码
                            "user_name" => "QC" . $inv_num, //默认用户名
                            "user_integral_wallet" => $send_integral,//注册的积分
                        ];
                        Db::name("user")->where("id", $res)->update($inv);
                        if ($send_integral > 0) {
                            //插入积分记录
                            $integral_data = [
                                "user_id" => $res,
                                "integral_operation" => $send_integral,//获得积分
                                "integral_balance" => $send_integral,//积分余额
                                "integral_type" => 1, //积分类型（1获得，-1消费）
                                "operation_time" => date("Y-m-d H:i:s"), //操作时间
                                "integral_remarks" => "成功注册送" . $send_integral . "积分",
                            ];
                            Db::name("integral")->insert($integral_data);
                        }
                        return ajax_success('绑定成功', $datas);
                    } else {
                        return ajax_error('请重新绑定手机', ['status' => 0]);
                }
            }
        }
        return view("user_bind_phone");
    }


}
