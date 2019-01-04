<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/1/4
 * Time: 18:41
 */
namespace app\index\controllerl;
use think\Controller;

class Service extends Controller{


    /**
     * 选择服务类型
     * 陈绪
     */
    public function type_index(){

        return view("type_index");

    }



    /**
     * 申请退款
     * 陈绪
     */
    public function refund(){

        return view("refund");

    }

}