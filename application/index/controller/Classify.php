<?php
namespace app\index\controller;

use think\Controller;
use think\Request;

class Classify extends Controller
{
    // 商品分类 商品品牌分类
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





    // 分类推荐
    public function classify_recommend()
    {
        return view("classify_recommend");
    }




    // 商品分类列表
    public function goods_list(Request $request)
    {

        if($request->isPost()){

        }
        return view("goods_list");
    }




    // 商品详情
    public function goods_detail()
    {
        return view("goods_detail");
    }
}
