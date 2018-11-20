<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/22
 * Time: 10:34
 */
namespace app\admin\controller;
use think\Controller;

class Serve extends Controller{


    /**
     * 服务商品管理
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index(){

        return view("serve_index");

    }



    /**
     * 服务商品添加
     * 陈绪
     */
    public function add(){

        $car = db("car_series")->distinct(true)->field(trim("vehicle_model"))->select();
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
        return view("serve_add",["data"=>$data]);

    }
	  /**
	     * 服务商品查看详情
	     * 陈绪
	     */
	    public function look(){
	
	        return view("serve_look");
	
	    }

}