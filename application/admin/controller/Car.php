<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/5
 * Time: 18:48
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;

class Car extends Controller{


    /**
     * 汽车logo品牌列表
     * 陈绪
     */
    public function index(){

        $car_data = db("car_images")->select();
        return view("car_index",["car_data"=>$car_data]);

    }



    /**
     * 汽车品牌添加
     * 陈绪
     */
    public function add(Request $request){

        $brand_name = db("car_series")->distinct(true)->field("brand")->select();
        return view("car_add",["brand_name"=>$brand_name]);

    }



    /**
     * 汽车品牌入库
     * 陈绪
     */
    public function save(Request $request){


        $brand_data = $request->param();
        $images = $request->file("brand_images");
        if(!empty($images)){
            $brand_images = $images->move(ROOT_PATH . 'public' . DS . 'uploads');
            $brand_data["brand_images"] = str_replace("\\", "/", $brand_images->getSaveName());
        }
        $bool = db("car_images")->insert($brand_data);
        if($bool){
            $this->success("添加成功",url("admin/Car/index"));
        }else{
            $this->error("添加失败",url("admin/Car/index"));
        }

    }



    /**
     * 汽车品牌修改
     * 陈绪
     */
    public function edit(){

        return view("car_edit");

    }



    /**
     * 汽车品牌更新
     * 陈绪
     */
    public function updata(){



    }



    /**
     * 汽车品牌删除
     * 陈绪
     */
    public function del(){



    }

}