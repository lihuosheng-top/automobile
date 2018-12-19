<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/5
 * Time: 18:48
 */
namespace app\admin\controller;
use Psy\Output\PassthruPager;
use think\Controller;
use think\Request;

class Car extends Controller{


    /**
     * 汽车logo品牌列表
     * 陈绪
     */
    public function index(){

        $car_data = db("car_images")->paginate(20);
        return view("car_index",["car_data"=>$car_data]);

    }



    /**
     * 汽车品牌添加
     * 陈绪
     */
    public function add(){

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
    public function edit($id){

        $brand = db("car_images")->where("id",$id)->select();
        $brand_name = db("car_series")->distinct(true)->field("brand")->select();
        return view("car_edit",["brand"=>$brand,"brand_name"=>$brand_name]);

    }



    /**
     * 汽车品牌更新
     * 陈绪
     */
    public function updata(Request $request){

        $id = $request->only(["id"])["id"];
        $brand_data = $request->param();
        $images = $request->file("brand_images");
        if(!empty($images)){
            $brand_images = $images->move(ROOT_PATH . 'public' . DS . 'uploads');
            $brand_data["brand_images"] = str_replace("\\", "/", $brand_images->getSaveName());
        }
        $bool = db("car_images")->where("id",$id)->update($brand_data);
        if($bool){
            $this->success("修改成功",url("admin/Car/index"));
        }else{
            $this->error("修改失败",url("admin/Car/index"));
        }


    }



    /**
     * 汽车品牌删除
     * 陈绪
     */
    public function del($id){

        $bool = db("car_images")->where("id",$id)->delete();
        if($bool){
            $this->success("删除成功",url("admin/Car/index"));
        }else{
            $this->error("删除失败",url("admin/Car/index"));
        }

    }


    /**
     * 图片删除
     * 陈绪
     * @param Request $request
     */
    public function images(Request $request){

        if($request->isPost()){
            $id = $request->only(["id"])["id"];
            $bool = db("car_images")->where("id",$id)->update(["brand_images"=>null]);
            if($bool){
                return ajax_success("修改成功");
            }else{
                return ajax_error("修改失败");
            }
        }

    }




    /**
     * 模糊搜索
     * 陈绪
     */
    public function search(Request $request){

        $brand_name = $request->only(["brand"])["brand"];
        if(!empty($brand_name)){
            $brand = db("car_images")->where("brand","like","%".$brand_name."%")->paginate(20, false, ['query' => request()->param()]);
        }
        return view("car_index",["brand"=>$brand]);


    }

}