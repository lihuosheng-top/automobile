<?php
namespace app\index\controller;

use think\Controller;
use think\Request;

class Classify extends Controller
{

    /**
     * 商品分类 商品品牌分类
     * 陈绪
     */
    public function classify_index(Request $request)
    {
        if($request->isPost()) {
            $brand = db("brand")->where("status", 1)->select();
            $goods_type = db("goods_type")->where("status", 1)->select();
            $goods_type = _tree_sort(recursionArr($goods_type), 'sort_number');
            $goods_brand = _tree_sort(recursionArr($brand), 'sort_number');
            return ajax_success("获取成功",array("goods_brand"=>$goods_brand,"goods_type"=>$goods_type));
        }
        return view("classify_index");
    }





    /**
     * 分类推荐
     * 陈绪
     */
    public function classify_recommend()
    {
        return view("classify_recommend");
    }




    /**
     * 商品列表
     * 陈绪
     */
    public function goods_list(Request $request)
    {

        if($request->isPost()){
            $goods_type_id = $request->only(["id"])["id"];
            $goods = db("goods")->where("goods_type_id",$goods_type_id)->where("goods_status",1)->select();
            if($goods){
                return ajax_success("获取成功",$goods);
            }else{
                return ajax_error("获取失败");
            }
        }
        return view("goods_list");
    }




    // 商品详情
    public function goods_detail(Request $request)
    {

        if($request->isPost()){
            $goods_id = $request->only(["id"])["id"];
            $goods = db("goods")->where("id",$goods_id)->select();
            foreach ($goods as $key=>$value){
                $goods[$key]["goods_standard_name"] = explode(",",$value["goods_standard_name"]);
                $goods_standard_value = explode(",",$value["goods_standard_value"]);
                $goods[$key]["goods_standard_value"] = array_chunk($goods_standard_value,"8");
                $goods[$key]["goods_brand"] = db("brand")->where("id",$value["goods_brand_id"])->find();
                $goods[$key]["images"] = db("goods_images")->where("goods_id",$value["id"])->select();
            }
            if($goods){
                return ajax_success("获取成功",$goods);
            }else{
                return ajax_error("获取失败");
            }
        }
        return view("goods_detail");
    }
}
