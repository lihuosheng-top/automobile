<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/23
 * Time: 17:38
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;

class Brand extends Controller{

    /**
     * 商品品牌
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index(){

        $brand_data = db("brand")->where("brand_status",1)->order("sort_number")->select();
        return view("brand_index",["brand_data"=>$brand_data]);

    }



    /**
     * 商品添加
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function add(){

        return view("brand_add");

    }



    /**
     * 商品入库
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function save(Request $request){
        $brand_data = $request->param();
        $brand_images = $request->file("brand_images");
        if(!empty($brand_images)){
            $brand_image = $brand_images->move(ROOT_PATH . 'public' . DS . 'uploads');
            $brand_data["brand_images"] = str_replace("\\", "/", $brand_image->getSaveName());
        }
        $bool = db("brand")->insert($brand_data);
        if($bool){
           $this->success("添加成功",url("admin/Brand/index"));
        }else{
            $this->success("添加成功",url("admin/Brand/index"));
        }
    }




    /**
     * 商品编辑
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function edit(){

        return view("brand_edit");

    }



    /**
     * 商品更新
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function updata(){


    }




    /**
     * 商品删除
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function del(){


    }

}