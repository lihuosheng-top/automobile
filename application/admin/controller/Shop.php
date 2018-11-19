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
        $data =Db::name('store')->where('store_id',$id)->find(); //所有数据
        $store_work_server_range =explode(',',$data['store_work_server_range']);//服务范围
        foreach ($store_work_server_range as $k=>$v){
            $range_data[] =Db::name('service_setting')->field('service_setting_name')->where('service_setting_id',$v)->find();
        }
        $store_city_address =explode(',',$data['store_city_address']);  //三级城市
        return view("shop_add",['data'=>$data,'store_city_address'=>$store_city_address,'range_data'=>$range_data]);

    }

}