<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/5
 * Time: 18:48
 */
namespace app\admin\controller;
use think\Controller;

class Car extends Controller{


    /**
     * 汽车logo品牌列表
     * 陈绪
     */
    public function index(){

        return view("car_index");

    }



    /**
     * 汽车品牌添加
     * 陈绪
     */
    public function add(){

        return view("car_add");

    }



    /**
     * 汽车品牌入库
     * 陈绪
     */
    public function save(){



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