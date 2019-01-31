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
     * 商品列表(综合)
     * 陈绪
     */
    public function goods_list(Request $request)
    {
        if($request->isPost()){
            $data =Session::get("role_name_store_id");
            if(!empty($data)) {
                $goods_type_id = $request->only(["id"])["id"];
                $goods_data = [];
                $goods = db("goods")
                    ->where("goods_type_id", $goods_type_id)
                    ->whereOr("goods_brand_id", $goods_type_id)
                    ->select();
                foreach ($goods as $kye => $value) {
                    $where = "`store_is_button` = '1' and `del_status` = '1' and `operation_status` = '1'";
                    $store = db("store")->where("store_id", $value["store_id"])->where($where)->find();
                    if (!empty($store)) {
                        if ($value["goods_status"] == 1 && !empty($store)) {
                            $special_data[] = db("special")
                                ->where("goods_id", $value["id"])
                                ->select();
                            $statistical_quantity[] = db("order_parts")
                                ->where("goods_id", $value["id"])
                                ->count();
                            unset($goods[$kye]);
                            $goods_data[] = $value;
                        }
                    }
                }
                if (!empty($special_data)) {
                    foreach ($special_data as $k => $v) {
                        $goods_data[$k]["special"] = $v;
                    }
                }
                if (!empty($statistical_quantity)) {
                    foreach ($statistical_quantity as $k => $v) {
                        $goods_data[$k]["statistical_quantity"] = $v;
                    }
                }
                if ($goods_data) {
                    return ajax_success("获取成功", $goods_data);
                } else {
                    return ajax_error("获取失败");
                }
            }else{
                $goods_type_id = $request->only(["id"])["id"];
                $goods_data = [];
                $goods = db("goods")
                    ->where("goods_type_id", $goods_type_id)
                    ->whereOr("goods_brand_id", $goods_type_id)
                    ->select();
                foreach ($goods as $kye => $value) {
                    if($value["goods_standard"] == "通用") {
                        unset($goods[$kye]);
                        $where = "`store_is_button` = '1' and `del_status` = '1' and `operation_status` = '1'";
                        $store = db("store")->where("store_id", $value["store_id"])->where($where)->find();
                        if (!empty($store)) {
                            if ($value["goods_status"] == 1 && !empty($store)) {
                                $special_data[] = db("special")
                                    ->where("goods_id", $value["id"])
                                    ->select();
                                $statistical_quantity[] = db("order_parts")
                                    ->where("goods_id", $value["id"])
                                    ->count();
                                unset($goods[$kye]);
                                $goods_data[] = $value;
                            }
                        }
                    }
                }
                if (!empty($special_data)) {
                    foreach ($special_data as $k => $v) {
                        $goods_data[$k]["special"] = $v;
                    }
                }
                if (!empty($statistical_quantity)) {
                    foreach ($statistical_quantity as $k => $v) {
                        $goods_data[$k]["statistical_quantity"] = $v;
                    }
                }
                if ($goods_data) {
                    return ajax_success("获取成功", $goods_data);
                } else {
                    return ajax_error("获取失败");
                }
            }
        }
        return view("goods_list");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:商品列表销量
     **************************************
     * @param Request $request
     * @return \think\response\View|void
     */
    public function goods_list_sales_volume(Request $request)
    {
        if($request->isPost()){
            $data =Session::get("role_name_store_id");
            if(!empty($data)) {
                $goods_type_id = $request->only(["id"])["id"];
                $goods_data = [];
                $goods = db("goods")
                    ->where("goods_type_id", $goods_type_id)
                    ->whereOr("goods_brand_id", $goods_type_id)
                    ->select();
                foreach ($goods as $kye => $value) {
                    $where = "`store_is_button` = '1' and `del_status` = '1' and `operation_status` = '1'";
                    $store = db("store")->where("store_id", $value["store_id"])->where($where)->find();
                    if (!empty($store)) {
                        if ($value["goods_status"] == 1 && !empty($store)) {
                            $special_data[] = db("special")
                                ->where("goods_id", $value["id"])
                                ->select();
                            $statistical_quantity[] = db("order_parts")
                                ->where("goods_id", $value["id"])
                                ->count();
                            unset($goods[$kye]);
                            $goods_data[] = $value;
                        }
                    }
                }
                if (!empty($special_data)) {
                    foreach ($special_data as $k => $v) {
                        $goods_data[$k]["special"] = $v;
                    }
                }
                if (!empty($statistical_quantity)) {
                    foreach ($statistical_quantity as $k => $v) {
                        $goods_data[$k]["statistical_quantity"] = $v;
                    }
                }
                if ($goods_data) {
                    $ords =array();
                    foreach ($goods_data as $vl){
                        $ords[] =intval($vl["statistical_quantity"]);
                    }
                    array_multisort($ords,SORT_DESC,$goods_data);
                    return ajax_success("获取成功", $goods_data);
                } else {
                    return ajax_error("获取失败");
                }
            }else{
                $goods_type_id = $request->only(["id"])["id"];
                $goods_data = [];
                $goods = db("goods")
                    ->where("goods_type_id", $goods_type_id)
                    ->whereOr("goods_brand_id", $goods_type_id)
                    ->select();
                foreach ($goods as $kye => $value) {
                    if($value["goods_standard"] == "通用") {
                        unset($goods[$kye]);
                        $where = "`store_is_button` = '1' and `del_status` = '1' and `operation_status` = '1'";
                        $store = db("store")->where("store_id", $value["store_id"])->where($where)->find();
                        if (!empty($store)) {
                            if ($value["goods_status"] == 1 && !empty($store)) {
                                $special_data[] = db("special")
                                    ->where("goods_id", $value["id"])
                                    ->select();
                                $statistical_quantity[] = db("order_parts")
                                    ->where("goods_id", $value["id"])
                                    ->count();
                                unset($goods[$kye]);
                                $goods_data[] = $value;
                            }
                        }
                    }
                }
                if (!empty($special_data)) {
                    foreach ($special_data as $k => $v) {
                        $goods_data[$k]["special"] = $v;
                    }
                }
                if (!empty($statistical_quantity)) {
                    foreach ($statistical_quantity as $k => $v) {
                        $goods_data[$k]["statistical_quantity"] = $v;
                    }
                }
                if ($goods_data) {
                    $ords =array();
                    foreach ($goods_data as $vl){
                        $ords[] =intval($vl["statistical_quantity"]);
                    }
                    array_multisort($ords,SORT_DESC,$goods_data);
                    return ajax_success("获取成功", $goods_data);
                } else {
                    return ajax_error("获取失败");
                }
            }
        }
        return view("goods_list");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:商品列表价格
     **************************************
     * @param Request $request
     * @return \think\response\View|void
     */
    public function goods_list_sales_price(Request $request)
    {
        if($request->isPost()){
            $data =Session::get("role_name_store_id");
            if(!empty($data)) {
                $goods_type_id = $request->only(["id"])["id"];
                $goods_data = [];
                $goods = db("goods")
                    ->where("goods_type_id", $goods_type_id)
                    ->whereOr("goods_brand_id", $goods_type_id)
                    ->select();
                foreach ($goods as $kye => $value) {
                    $where = "`store_is_button` = '1' and `del_status` = '1' and `operation_status` = '1'";
                    $store = db("store")->where("store_id", $value["store_id"])->where($where)->find();
                    if (!empty($store)) {
                        if ($value["goods_status"] == 1 && !empty($store)) {
                            $special_data[] = db("special")
                                ->where("goods_id", $value["id"])
                                ->order("goods_adjusted_price","asc")
                                ->select();
                            $statistical_quantity[] = db("order_parts")
                                ->where("goods_id", $value["id"])
                                ->count();
                            unset($goods[$kye]);
                            $goods_data[] = $value;
                        }
                    }
                }
                if (!empty($special_data)) {
                    foreach ($special_data as $k => $v) {
                        $goods_data[$k]["special"] = $v;
                        $goods_data[$k]["goods_adjusted_moneys"] =$goods_data[$k]["special"][0]["goods_adjusted_price"];
                    }
                }
                if (!empty($statistical_quantity)) {
                    foreach ($statistical_quantity as $k => $v) {
                        $goods_data[$k]["statistical_quantity"] = $v;
                    }
                }
                if ($goods_data) {
                    $ords =array();
                    foreach ($goods_data as $vl){
                        $ords[] =intval($vl["goods_adjusted_moneys"]);
                    }
                    array_multisort($ords,SORT_ASC,$goods_data);
                    return ajax_success("获取成功", $goods_data);
                } else {
                    return ajax_error("获取失败");
                }
            }else{
                $goods_type_id = $request->only(["id"])["id"];
                $goods_data = [];
                $goods = db("goods")
                    ->where("goods_type_id", $goods_type_id)
                    ->whereOr("goods_brand_id", $goods_type_id)
                    ->select();
                foreach ($goods as $kye => $value) {
                    if($value["goods_standard"] == "通用") {
                        unset($goods[$kye]);
                        $where = "`store_is_button` = '1' and `del_status` = '1' and `operation_status` = '1'";
                        $store = db("store")->where("store_id", $value["store_id"])->where($where)->find();
                        if (!empty($store)) {
                            if ($value["goods_status"] == 1 && !empty($store)) {
                                $special_data[] = db("special")
                                    ->where("goods_id", $value["id"])
                                    ->select();
                                $statistical_quantity[] = db("order_parts")
                                    ->where("goods_id", $value["id"])
                                    ->count();
                                unset($goods[$kye]);
                                $goods_data[] = $value;
                            }
                        }
                    }
                }
                if (!empty($special_data)) {
                    foreach ($special_data as $k => $v) {
                        $goods_data[$k]["special"] = $v;
                        $goods_data[$k]["goods_adjusted_moneys"] =$goods_data[$k]["special"][0]["goods_adjusted_price"];
                    }
                }
                if (!empty($statistical_quantity)) {
                    foreach ($statistical_quantity as $k => $v) {
                        $goods_data[$k]["statistical_quantity"] = $v;
                    }
                }
                if ($goods_data) {
                    $ords =array();
                    foreach ($goods_data as $vl){
                        $ords[] =intval($vl["goods_adjusted_moneys"]);
                    }
                    array_multisort($ords,SORT_ASC,$goods_data);
                    return ajax_success("获取成功", $goods_data);
                } else {
                    return ajax_error("获取失败");
                }
            }
        }
        return view("goods_list");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:商品列表区域
     **************************************
     * @param Request $request
     * @return \think\response\View|void
     */
    public function goods_list_area(Request $request)
    {
        if($request->isPost()){
            $data =Session::get("role_name_store_id");
            if(!empty($data)) {
                //这个是商家
                $goods_type_id = $request->only(["id"])["id"];
                $area_address =$request->only(["area"])["area"];
                $goods_data = [];
                $goods = db("goods")
                    ->where("goods_type_id", $goods_type_id)
                    ->whereOr("goods_brand_id", $goods_type_id)
                    ->select();
                foreach ($goods as $kye => $value) {
                    $where = "`store_is_button` = '1' and `del_status` = '1' and `operation_status` = '1'";
                    $area_condition = " `real_address` like '%{$area_address}%'";
                    $store = db("store")
                        ->where("store_id", $value["store_id"])
                        ->where($area_condition)
                        ->where($where)
                        ->find();
                    if (!empty($store)) {
                        if ($value["goods_status"] == 1 && !empty($store)) {
                            $special_data[] = db("special")
                                ->where("goods_id", $value["id"])
                                ->select();
                            $statistical_quantity[] = db("order_parts")
                                ->where("goods_id", $value["id"])
                                ->count();
                            unset($goods[$kye]);
                            $goods_data[] = $value;
                        }
                    }
                }
                if (!empty($special_data)) {
                    foreach ($special_data as $k => $v) {
                        $goods_data[$k]["special"] = $v;
                    }
                }
                if (!empty($statistical_quantity)) {
                    foreach ($statistical_quantity as $k => $v) {
                        $goods_data[$k]["statistical_quantity"] = $v;
                    }
                }
                if ($goods_data) {
                    $ords =array();
                    foreach ($goods_data as $vl){
                        $ords[] =intval($vl["goods_adjusted_money"]);
                    }
                    array_multisort($ords,SORT_ASC,$goods_data);
                    return ajax_success("获取成功", $goods_data);
                } else {
                    return ajax_error("获取失败");
                }
            }else{
                //这个是车主的
                $goods_type_id = $request->only(["id"])["id"];
                $area_address =$request->only(["area"])["area"];
                $goods_data = [];
                $goods = db("goods")
                    ->where("goods_type_id", $goods_type_id)
                    ->whereOr("goods_brand_id", $goods_type_id)
                    ->select();
                foreach ($goods as $kye => $value) {
                    if($value["goods_standard"] == "通用") {
                        unset($goods[$kye]);
                        $where = "`store_is_button` = '1' and `del_status` = '1' and `operation_status` = '1'";
                        $area_condition = " `real_address` like '%{$area_address}%'";
                        $store = db("store")
                            ->where($area_condition)
                            ->where("store_id", $value["store_id"])
                            ->where($where)
                            ->find();
                        if (!empty($store)) {
                            if ($value["goods_status"] == 1 && !empty($store)) {
                                //规格
                                $special_data[] = db("special")
                                    ->where("goods_id", $value["id"])
                                    ->select();
                                //数量
                                $statistical_quantity[] = db("order_parts")
                                    ->where("goods_id", $value["id"])
                                    ->count();
                                //经度
                                $longitude[] =$store["longitude"];
                                //纬度
                                $latitude[] =$store["latitude"];
                                unset($goods[$kye]);
                                $goods_data[] = $value;
                            }
                        }
                    }
                }
                if (!empty($special_data)) {
                    foreach ($special_data as $k => $v) {
                        $goods_data[$k]["special"] = $v;
                    }
                }
                if (!empty($statistical_quantity)) {
                    foreach ($statistical_quantity as $k => $v) {
                        $goods_data[$k]["statistical_quantity"] = $v;
                    }
                }
                //经度
                if(!empty($longitude)){
                    foreach ($longitude as $k=>$v){
                        $goods_data[$k]["longitude"] =$v;
                    }
                }
                //纬度
                if(!empty($latitude)){
                    foreach ($latitude as $k=>$v){
                        $goods_data[$k]["latitude"] =$v;
                    }
                }
                if ($goods_data) {
                    $ords =array();
                    foreach ($goods_data as $vl){
                        $ords[] =intval($vl["goods_adjusted_money"]);
                    }
                    array_multisort($ords,SORT_ASC,$goods_data);
                    return ajax_success("获取成功", $goods_data);
                }else {
                    return ajax_error("获取失败");
                }
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
            $condition ="`status` != '9' and `status` != '10' and `status` != '0'";
            foreach ($goods as $key=>$value){
                $goods[$key]["goods_standard_name"] = explode(",",$value["goods_standard_name"]);
                $goods_standard_value = explode(",",$value["goods_standard_value"]);
                $goods[$key]["goods_standard_value"] = array_chunk($goods_standard_value,"8");
                $goods[$key]["goods_brand"] = db("brand")->where("id",$value["goods_brand_id"])->find();
                $goods[$key]["images"] = db("goods_images")->where("goods_id",$value["id"])->select();
                $goods[$key]["goods_standard"] = $goods_standard;
            }
            if($goods){
                foreach ($goods[0]["goods_standard"] as $k=>$v){
                    $rm_number =Db::name("order_parts")
                        ->where("special_id",$v["id"])
                        ->where($condition)
                        ->sum("order_quantity");//已买订单数量总和
                    if(empty($rm_number)){
                        $rm_number =0;
                    }
                    $goods[0]["goods_standard"][$k]["stock"] =$v["stock"]- $rm_number;
                }
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
     * Notes:分类页面搜索
     **************************************
     * @param Request $request
     * @return \think\response\View|void
     */
    public function classify_index_search(Request $request){
        if($request->isPost()) {
            $names =$request->only("names")["names"];
            $condition = " `name` like '%{$names}%'";
            $brand = db("brand")
                ->where($condition)
                ->where("status", 1)
                ->select();
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
     **************李火生*******************
     * @param Request $request
     * Notes:分类进入详情页面搜索
     **************************************
     * @param Request $request
     * @return \think\response\View|void
     */
    public function goods_list_search(Request $request)
    {
        if($request->isPost()){
            $is_boss =Session::get("role_name_store_id");
            $goods_type_id = $request->only(["id"])["id"]; //没传id
            $goods_name =$request->only(["goods_name"])["goods_name"];
            //商家（既能看到商品专用通用）
            if(!empty($is_boss)){
                if(!empty($goods_type_id)){
                    $goods_data = [];
                    $condition = " `goods_name` like '%{$goods_name}%'";
                    $goods = db("goods")
                        ->where($condition)
                        ->where("goods_brand_id",$goods_type_id)
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
                }else{
                    $goods_data = [];
                    $condition = " `goods_name` like '%{$goods_name}%'";
                    $goods = db("goods")
                        ->where($condition)
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
            }else{
                if(!empty($goods_type_id)){
                    $goods_data = [];
                    $condition = " `goods_name` like '%{$goods_name}%'";
                    $goods = db("goods")
                        ->where("goods_standard","通用")
                        ->where($condition)
                        ->where("goods_brand_id",$goods_type_id)
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
                }else{
                    $goods_data = [];
                    $condition = " `goods_name` like '%{$goods_name}%'";
                    $goods = db("goods")
                        ->where($condition)
                        ->where("goods_standard","通用")
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
            }

        }
        return view("goods_list");
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
            foreach ($goods_info as $ks=>$vs){
                $goods_info[$ks]["special_info"] =db("special")->where("goods_id",$vs["id"])->select();
            }
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
            $is_boss =Session::get("role_name_store_id");
            //是商家通用专用都能看
            if(!empty($is_boss)){
                $goods_info =Db::table("tb_goods")
                    ->field("tb_goods.*")
                    ->join("tb_store","tb_goods.store_id=tb_store.store_id","left")
                    ->where("tb_store.del_status",1)
                    ->where("tb_store.store_is_button",1)
                    ->where("tb_store.operation_status",1)
                    ->order('rand()')
                    ->limit(2)
                    ->select();
                foreach ($goods_info as $ks =>$vs){
                    $goods_info[$ks]["special_info"] =db("special")->where("goods_id",$vs["id"])->select();
                    $num =db("order_parts")->where("goods_id",$vs["id"])->sum("order_quantity");
                    if(!empty($num)){
                        $goods_info[$ks]["statistical_quantity"] =$num;
                    }else{
                        $goods_info[$ks]["statistical_quantity"] =0;
                    }
                }
                if(!empty($goods_info)){
                    return ajax_success("数据成功",$goods_info);
                }else{
                    return ajax_error("没有数据",["status"=>0]);
                }
            }else{
                $goods_info =Db::table("tb_goods")
                    ->field("tb_goods.*")
                    ->join("tb_store","tb_goods.store_id=tb_store.store_id","left")
                    ->where("tb_store.del_status",1)
                    ->where("tb_goods.goods_standard","通用")
                    ->where("tb_store.store_is_button",1)
                    ->where("tb_store.operation_status",1)
                    ->order('rand()')
                    ->limit(2)
                    ->select();
                foreach ($goods_info as $ks =>$vs){
                    $goods_info[$ks]["special_info"] =db("special")->where("goods_id",$vs["id"])->select();
                    $num =db("order_parts")->where("goods_id",$vs["id"])->sum("order_quantity");
                    if(!empty($num)){
                        $goods_info[$ks]["statistical_quantity"] =$num;
                    }else{
                        $goods_info[$ks]["statistical_quantity"] =0;
                    }
                }
                if(!empty($goods_info)){
                    return ajax_success("数据成功",$goods_info);
                }else{
                    return ajax_error("没有数据",["status"=>0]);
                }
            }


        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商商品详情页面的所有评价数据
     **************************************
     */
    public function goods_evaluate_return(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");
            $goods_id = $request->only(["goods_id"])["goods_id"];
            $evaluate_info =db("order_parts_evaluate")
                ->where("goods_id",$goods_id)
                ->select();
            foreach ($evaluate_info as $ks=>$vs){
              $evaluate_info[$ks]["images"] = db("order_parts_evaluate_images")
                  ->field("images")
                  ->where("evaluate_order_id",$vs["id"])
                  ->select();
              $evaluate_info[$ks]["order_create_time"] =db("order_parts")
                  ->where("id",$vs["order_id"])
                  ->value("order_create_time");
                $evaluate_info[$ks]["user_info"] =db("user")
                    ->where("id",$vs["user_id"])
                    ->field("user_img,phone_num")
                    ->find();
                $evaluate_info[$ks]["praise"] =Db::name("order_parts_praise")
                    ->where("parts_evaluate_id",$vs["id"])
                    ->count();
                $res =db("order_parts_praise")
                    ->where("parts_evaluate_id",$vs["id"])
                    ->where("user_id",$user_id)
                    ->find();
                if($res){
                    $evaluate_info[$ks]["is_praise"] =1;
                }else{
                    $evaluate_info[$ks]["is_praise"] =0;
                }

            }
           if(!empty($evaluate_info)){
                return ajax_success("数据返回成功",$evaluate_info);
           }else{
               return ajax_error("没有数据",["status"=>0]);
           }

        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商全部评价里面的（好评）
     **************************************
     */
    public function goods_evaluate_good(Request $request){
        if($request->isPost()){
            $goods_id = $request->only(["goods_id"])["goods_id"];
            $condition ="evaluate_stars = 4 or evaluate_stars = 5";
            $evaluate_info =db("order_parts_evaluate")
                ->where("goods_id",$goods_id)
                ->where($condition)
                ->select();
            foreach ($evaluate_info as $ks=>$vs){
                $evaluate_info[$ks]["images"] = db("order_parts_evaluate_images")
                    ->field("images")
                    ->where("evaluate_order_id",$vs["id"])
                    ->select();
                $evaluate_info[$ks]["order_create_time"] =db("order_parts")
                    ->where("id",$vs["order_id"])
                    ->value("order_create_time");
                $evaluate_info[$ks]["user_info"] =db("user")
                    ->where("id",$vs["user_id"])
                    ->field("user_img,phone_num")
                    ->find();
                $evaluate_info[$ks]["praise"] =Db::name("order_parts_praise")
                    ->where("parts_evaluate_id",$vs["id"])
                    ->count();
                $res =db("order_parts_praise")
                    ->where("parts_evaluate_id",$vs["id"])
                    ->where("user_id",$vs["user_id"])
                    ->find();
                if($res){
                    $evaluate_info[$ks]["is_praise"] =1;
                }else{
                    $evaluate_info[$ks]["is_praise"] =0;
                }
            }
            if(!empty($evaluate_info)){
                return ajax_success("数据返回成功",$evaluate_info);
            }else{
                return ajax_error("没有数据",["status"=>0]);
            }

        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商全部评价里面的（中评）
     **************************************
     */
    public function goods_evaluate_secondary(Request $request){
        if($request->isPost()){
            $goods_id = $request->only(["goods_id"])["goods_id"];
            $condition ="evaluate_stars = 2 or evaluate_stars = 3";
            $evaluate_info =db("order_parts_evaluate")
                ->where("goods_id",$goods_id)
                ->where($condition)
                ->select();
            foreach ($evaluate_info as $ks=>$vs){
                $evaluate_info[$ks]["images"] = db("order_parts_evaluate_images")
                    ->field("images")
                    ->where("evaluate_order_id",$vs["id"])
                    ->select();
                $evaluate_info[$ks]["order_create_time"] =db("order_parts")
                    ->where("id",$vs["order_id"])
                    ->value("order_create_time");
                $evaluate_info[$ks]["user_info"] =db("user")
                    ->where("id",$vs["user_id"])
                    ->field("user_img,phone_num")
                    ->find();
                $evaluate_info[$ks]["praise"] =Db::name("order_parts_praise")
                    ->where("parts_evaluate_id",$vs["id"])
                    ->count();
                $res =db("order_parts_praise")
                    ->where("parts_evaluate_id",$vs["id"])
                    ->where("user_id",$vs["user_id"])
                    ->find();
                if($res){
                    $evaluate_info[$ks]["is_praise"] =1;
                }else{
                    $evaluate_info[$ks]["is_praise"] =0;
                }
            }
            if(!empty($evaluate_info)){
                return ajax_success("数据返回成功",$evaluate_info);
            }else{
                return ajax_error("没有数据",["status"=>0]);
            }

        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商全部评价里面的（差评）
     **************************************
     */
    public function goods_evaluate_bad(Request $request){
        if($request->isPost()){
            $goods_id = $request->only(["goods_id"])["goods_id"];
            $evaluate_info =db("order_parts_evaluate")
                ->where("goods_id",$goods_id)
                ->where("evaluate_stars",1)
                ->select();
            foreach ($evaluate_info as $ks=>$vs){
                $evaluate_info[$ks]["images"] = db("order_parts_evaluate_images")
                    ->field("images")
                    ->where("evaluate_order_id",$vs["id"])
                    ->select();
                $evaluate_info[$ks]["order_create_time"] =db("order_parts")
                    ->where("id",$vs["order_id"])
                    ->value("order_create_time");
                $evaluate_info[$ks]["user_info"] =db("user")
                    ->where("id",$vs["user_id"])
                    ->field("user_img,phone_num")
                    ->find();
                $evaluate_info[$ks]["praise"] =Db::name("order_parts_praise")
                    ->where("parts_evaluate_id",$vs["id"])
                    ->count();
                $res =db("order_parts_praise")
                    ->where("parts_evaluate_id",$vs["id"])
                    ->where("user_id",$vs["user_id"])
                    ->find();
                if($res){
                    $evaluate_info[$ks]["is_praise"] =1;
                }else{
                    $evaluate_info[$ks]["is_praise"] =0;
                }
            }
            if(!empty($evaluate_info)){
                return ajax_success("数据返回成功",$evaluate_info);
            }else{
                return ajax_error("没有数据",["status"=>0]);
            }

        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商全部评价里面的（有图）
     **************************************
     * @param Request $request
     */
    public function goods_evaluate_has_img(Request $request){
        if($request->isPost()){
            $goods_id = $request->only(["goods_id"])["goods_id"];
            $evaluate_info =db("order_parts_evaluate")
                ->where("goods_id",$goods_id)
                ->select();
            foreach ($evaluate_info as $ks=>$vs){
                $img = db("order_parts_evaluate_images")
                    ->field("images")
                    ->where("evaluate_order_id",$vs["id"])
                    ->select();
                if(!empty($img)){
                    $evaluate_info[$ks]["images"] =$img;
                }else{
                    $evaluate_info[$ks]["images"] =null;
                }
                $evaluate_info[$ks]["order_create_time"] =db("order_parts")
                    ->where("id",$vs["order_id"])
                    ->value("order_create_time");
                $evaluate_info[$ks]["user_info"] =db("user")
                    ->where("id",$vs["user_id"])
                    ->field("user_img,phone_num")
                    ->find();
                $evaluate_info[$ks]["praise"] =Db::name("order_parts_praise")
                    ->where("parts_evaluate_id",$vs["id"])
                    ->count();
                $res =db("order_parts_praise")
                    ->where("parts_evaluate_id",$vs["id"])
                    ->where("user_id",$vs["user_id"])
                    ->find();
                if($res){
                    $evaluate_info[$ks]["is_praise"] =1;
                }else{
                    $evaluate_info[$ks]["is_praise"] =0;
                }
            }
            if(!empty($evaluate_info)){
                return ajax_success("数据返回成功",$evaluate_info);
            }else{
                return ajax_error("没有数据",["status"=>0]);
            }

        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商商品详情页面的评价数据查看详情
     **************************************
     */
    public function goods_evaluate_detail(Request $request){
        if($request->isPost()){
            $evaluate_id = $request->only(["id"])["id"];//评价的id
            $evaluate_info["evaluate_info"] =db("order_parts_evaluate")->where("id",$evaluate_id)->find();
            $evaluate_info["images"] =db("order_parts_evaluate_images")
                ->field("images")
                ->where("evaluate_order_id",$evaluate_id)
                ->select();
            $evaluate_info["user_info"] = db("user")
                ->where("id", $evaluate_info["evaluate_info"]["user_id"])
                ->field("user_img,phone_num")
                ->find();
            $evaluate_info["order_create_time"] =db("order_parts")
                ->where("id",$evaluate_info["evaluate_info"]["order_id"])
                ->value("order_create_time");
            if(!empty($evaluate_info)){
                return ajax_success("成功返回",$evaluate_info);
            }else{
                return ajax_error("请重新查看",["status"=>0]);
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
            return ajax_success("获取成功", $parets_id);
        }
    }

}
