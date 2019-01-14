<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/23
 * Time: 14:00
 */
namespace app\index\controller;
use think\Controller;
use think\Request;

class Parts extends Controller{


    /**
     * 配件商城
     * 陈绪
     */
    public function parts_index(Request $request){

        if($request->isPost()) {
            $parets_category = db("goods_type")->where("status", 1)->where("pid", 0)->select();
            return ajax_success("获取成功",$parets_category);
        }
        return view("parts_index");

    }

}