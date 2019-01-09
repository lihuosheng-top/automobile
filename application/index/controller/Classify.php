<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;

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
        if($request->get()){
            $parets_id = $request->only(["parets_id"])["parets_id"];
            Session::set("parets_id",$parets_id);
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
            $goods_data = [];
            $goods = db("goods")
                ->where("goods_type_id",$goods_type_id)
                ->whereOr("goods_brand_id",$goods_type_id)
                ->select();
            foreach ($goods as $kye=>$value){
                $where = "`store_is_button` = '1' and `del_status` = '1' and `operation_status` = '1'";
                $store = db("store")->where("store_id",$value["store_id"])->where($where)->find();
                if(!empty($store)){
                    if($value["goods_status"] == 1 && !empty($store)){
                        $special_data[] =db("special")
                            ->where("goods_id",$value["id"])
                            ->select();
                        $statistical_quantity[] =db("order_parts")
                            ->where("goods_id",$value["id"])
                            ->count();
                        unset($goods[$kye]);
                        $goods_data[] = $value;
                    }
                }
            }
            if(!empty($special_data)){
                foreach ($special_data as $k=>$v){
                    $goods_data[$k]["special"] =$v;
                }
            }
            if(!empty($statistical_quantity)){
                foreach ($statistical_quantity as $k=>$v){
                    $goods_data[$k]["statistical_quantity"] =$v;
                }
            }
            if($goods_data){
                return ajax_success("获取成功",$goods_data);
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
            $goods_standard = db("special")->where("goods_id", $goods_id)->select();
            foreach ($goods as $key=>$value){
                $goods[$key]["goods_standard_name"] = explode(",",$value["goods_standard_name"]);
                $goods_standard_value = explode(",",$value["goods_standard_value"]);
                $goods[$key]["goods_standard_value"] = array_chunk($goods_standard_value,"8");
                $goods[$key]["goods_brand"] = db("brand")->where("id",$value["goods_brand_id"])->find();
                $goods[$key]["images"] = db("goods_images")->where("goods_id",$value["id"])->select();
                $goods[$key]["goods_standard"] = $goods_standard;
            }
            if($goods){
                return ajax_success("获取成功",$goods);
            }else{
                return ajax_error("获取失败");
            }
        }
        return view("goods_detail");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:进入店铺信息
     **************************************
     */
    public function go_to_store(Request $request){
        if($request->isPost()){
            $goods_id = $request->only(["goods_id"])["goods_id"];
            $store_id = db("goods")->where("id",$goods_id)->value("store_id");
            $store_data =db("store")->field("store_name,store_logo_images")->where("store_id",$store_id)->find();
            $goods_info =db("goods")
                ->where("store_id",$store_id)
                ->order('rand()')
                ->limit(3)
                ->select();
            $data =[
                "goods_info"=>$goods_info,
                "store_id"=>$store_id,
                "store_data"=>$store_data,
            ];
            if(!empty($data)){
                return ajax_success("数据返回成功",$data);
            }else{
                return ajax_error("没有数据",["status"=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:你可能还喜欢
     **************************************
     * @param Request $request
     */
    public function may_like_goods(Request $request){
        if($request->isPost()){
            $goods_info =db("goods")
                ->order('rand()')
                ->limit(2)
                ->select();
            foreach ($goods_info as $ks =>$vs){
                $goods_info[$ks]["special_info"] =db("special")->where("goods_id",$vs["id"])->select();
                $goods_info[$ks]["statistical_quantity"] =db("order_parts")->where("goods_id",$vs["id"])->sum("order_quantity");
            }
            if(!empty($goods_info)){
                return ajax_success("数据成功",$goods_info);
            }else{
                return ajax_error("没有数据",["status"=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商商品详情页面的评价数据
     **************************************
     */
    public function goods_evaluate_return(Request $request){
        if($request->isPost()){
            $goods_id = $request->only(["goods_id"])["goods_id"];
            $evaluate_info =db("order_parts_evaluate")->where("goods_id",$goods_id)->select();
            foreach ($evaluate_info as $ks=>$vs){
              $evaluate_info[$ks]["images"] = db("order_parts_evaluate_images")
                  ->field("images")
                  ->where("evaluate_order_id",$vs["id"])
                  ->select();
              $evaluate_info[$ks]["order_create_time"] =db("order_parts")
                  ->where("id",$vs["order_id"])
                  ->field("order_create_time")
                  ->find();
            }
           if(!empty($evaluate_info)){
                return ajax_success("数据返回成功",$evaluate_info);
           }else{
               return ajax_error("没有数据",["status"=>0]);
           }

        }
    }



    /**
     * 获取配件城id
     * 陈绪
     */
    public function parets_id(Request $request){

        if($request->isPost()) {
            $parets_id = Session::get("parets_id");
            Session::delete("parets_id");
            return ajax_success("获取成功", $parets_id);
        }
    }

}
