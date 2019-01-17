<?php
/**
 * Created by PhpStorm.
 * User: CHEN
 * Date: 2018/7/10
 * Time: 18:20
 */
namespace app\admin\controller;

use think\Controller;
use think\Config;
use think\captcha\Captcha;
use think\Request;
use think\Session;

class Index extends Controller{


    /**
     * [后台首页]
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     * 陈绪
     */
    public function index(Request $request){
        $menu_list = Config::get("menu_list");
        $admin = Config::get("admin");
        $admin_id = Session::get("user_id");
        $admin_role = db("admin")->where("id", $admin_id)->field("role_id")->find();
        if ($admin_role["role_id"] == 2) {
            $goods_year = db("goods")->field("goods_year_id,id")->select();
            $time = date("Y-m-d");
            foreach ($goods_year as $key => $value) {
                $year = db("year")->where("id", $value["goods_year_id"])->value("year");
                $date = date("Y-m-d", strtotime("+$year year"));
                if ($time == $date) {
                    $bool = db("goods")->where("id", $value["id"])->update(["goods_status" => 0, "putaway_status" => null]);
                }
            }
        }else{
            $goods_year = db("goods")->field("goods_year_id,id")->select();
            $time = date("Y-m-d");
            foreach ($goods_year as $key => $value) {
                $year = db("year")->where("id", $value["goods_year_id"])->value("year");
                $date = date("Y-m-d", strtotime("+$year year"));
                if ($time == $date) {
                    $bool = db("goods")->where("id", $value["id"])->update(["goods_status" => 0, "putaway_status" => null]);
                }
            }
        }
        return view("index",["menu_list"=>Config::get("menu_list"),"admin"=>$admin]);
    }


}