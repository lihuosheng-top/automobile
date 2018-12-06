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

        $brand_data = db("brand")->where("status",1)->order("sort_number")->select();
        return view("brand_index",["brand_data"=>$brand_data]);

    }



    /**
     * 商品品牌添加
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function add($pid = 0){
        $brand_list = [];
        if($pid == 0) {
            $brand_list = getSelectList("brand");
        }
        return view("brand_add",["brand_list"=>$brand_list]);

    }



    /**
     * 商品品牌入库
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
     * 商品品牌编辑
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function edit($id,$pid=0){

        $brand_data = db("brand")->where("id",$id)->select();
        if($pid == 0) {
            $brand_list = getSelectList("brand");
        }
        return view("brand_edit",["brand_list"=>$brand_list,"brand_data"=>$brand_data]);

    }



    /**
     * 商品品牌更新
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function updata(Request $request){

        $id = $request->only(["id"])["id"];
        $brand_data = $request->param();
        $brand_images = $request->file("brand_images");
        if(!empty($brand_images)){
            $brand_image = $brand_images->move(ROOT_PATH . 'public' . DS . 'uploads');
            $brand_data["brand_images"] = str_replace("\\", "/", $brand_image->getSaveName());
        }
        $bool = db("brand")->where("id",$id)->update($brand_data);
        if($bool){
            $this->success("编辑成功",url("admin/Brand/index"));
        }else{
            $this->success("编辑失败",url("admin/Brand/index"));
        }

    }




    /**
     * 商品品牌删除
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function del($id){

        $brand_images = db("brand")->where("id",$id)->find();
        $bool = db("brand")->where("id",$id)->delete();
        if($bool){
            if(!empty($brand_images)){
                unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$brand_images['brand_images']);
            }
            $this->success("删除成功",url("admin/Brand/index"));
        }else{
            $this->success("删除失败",url("admin/Brand/index"));
        }

    }




    /**
     * 商品品牌状态修改
     * 陈绪
     */
    public function status(Request $request){
        if($request->isPost()) {
            $status = $request->only(["status"])["status"];
            if($status == 0) {
                $id = $request->only(["id"])["id"];
                $bool = db("brand")->where("id", $id)->update(["status" => 0]);
                if ($bool) {
                    $this->redirect(url("admin/admin/index"));
                } else {
                    $this->error("修改失败", url("admin/admin/index"));
                }
            }
            if($status == 1){
                $id = $request->only(["id"])["id"];
                $bool = db("brand")->where("id", $id)->update(["status" => 1]);
                if ($bool) {
                    $this->redirect(url("admin/admin/index"));
                } else {
                    $this->error("修改失败", url("admin/admin/index"));
                }
            }
        }
    }





    /**
     * [图片删除]
     * 陈绪
     */
    public function images(Request $request){
        if($request->isPost()){
            $id = $request->only(['id'])['id'];
            $brand_images = db("brand")->where("id",$id)->find();
            $image_bool = db("brand")->where("id",$id)->update(["brand_images"=>null]);
            if($image_bool){
                unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$brand_images['brand_images']);
                return ajax_success("删除成功");
            }else{
                return ajax_error("删除失败");
            }
        }
    }

}