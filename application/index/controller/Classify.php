<?php
namespace app\index\controller;

use think\Controller;

class Classify extends Controller
{
    // 商品分类 商品品牌分类
    public function Classify_index()
    {

        $brand = db("brand")->where("status",1)->select();
        $goods_type = db("goods_type")->where("status",1)->select();
        $goods_type = _tree_sort(recursionArr($goods_type),'sort_number');

        return ajax_success("获取成功",$goods_type);
        return view("classify_index");
    }





    // 分类推荐
    public function Classify_recommend()
    {
        return view("classify_recommend");
    }




    // 商品分类列表
    public function Goods_list()
    {
        return view("goods_list");
    }




    // 商品详情
    public function Goods_detail()
    {
        return view("goods_detail");
    }
}
