<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Session;

class Index extends Controller
{


    /**
     * 首页
     * 陈绪
     */
    public function index(Request $request)
    {

        if($request->isPost()) {
            $user_id = Session::get("user");
            if (!empty($user_id)) {
                $user_car = db("user_car")->where("user_id", $user_id)->where("status", 1)->select();
                if ($user_car) {
                    return ajax_success("获取成功", $user_car);
                } else {
                    return ajax_error("获取失败");
                }

            } else {
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
        }

        return view("index");
    }





    public function home()
    {
        return view("home");
    }


}
