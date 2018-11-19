<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/22
 * Time: 19:53
 */
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Shop extends Controller{


    /**
     **************李火生*******************
     * @return \think\response\View
     * 店铺列表
     **************************************
     */
    public function index(){
        $store_data  =Db::name('store')->paginate(3);
        return view("shop_index",['store_data'=>$store_data]);

    }



    /**
     * 店铺基本信息
     * 陈绪
     */
    public function add($id){
        $data =Db::name('store')->where('store_id',$id)->find();
        return view("shop_add",['data'=>$data]);

    }

}