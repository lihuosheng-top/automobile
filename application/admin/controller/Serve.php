<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/22
 * Time: 10:34
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;

class Serve extends Controller{


    /**
     * 服务商品管理
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index(){

        $serve_goods = db("serve_goods")->order("id desc")->select();
        foreach ($serve_goods as $key=>$value){
            $serve_goods[$key]["serve_goods_name"] = db("service_setting")->where("service_setting_id",$value["service_setting_id"])->value("service_setting_name");
        }
        return view("serve_index",["serve_goods"=>$serve_goods]);

    }



    /**
     * 服务商品添加
     * 陈绪
     */
    public function add(){

        $car = db("car_series")->distinct(true)->field("vehicle_model")->select();
        $data = [];
        foreach ($car as $key=>$value){
            $arr[] = trim($value["vehicle_model"]);
            $data = array_unique($arr);
        }
        foreach ($data as $k=>$val){
            if(empty($val)){
                unset($data[$k]);
            }
        }
        $service_setting = db("service_setting")->select();
        return view("serve_add",["data"=>$data,"service_setting"=>$service_setting]);

    }



    /**
     * 服务商品查看详情
     * 陈绪
     */
    public function look(){

        return view("serve_look");

    }



    /**
     * 服务商品入库
     * 陈绪
     */
    public function save(Request $request){

        $serve_goods = $request->param();
        $bool = db("serve_goods")->insert($serve_goods);
        if($bool){
            $this->success("保存成功",url("admin/Serve/index"));
        }else{
            $this->error("保存失败",url("admin/Serve/index"));
        }

    }

}