<?php
/**
 * Created by PhpStorm.
 * User: CHEN
 * Date: 2018/7/11
 * Time: 16:12
 */

namespace app\admin\controller;

use think\console\Input;
use think\Controller;
use think\Db;
use think\Request;
use think\Image;
use app\admin\model\Good;
use app\admin\model\GoodsImages;
use think\Session;
use think\Loader;

class Goods extends Controller{

    public $goods_status = [0,1];
    /**
     * [商品列表]
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     * 陈绪
     */
    public function index(Request $request){
        $admin_id = Session::get("user_id");
        $admin_role = db("admin")->where("id",$admin_id)->field("role_id")->find();
        if($admin_role["role_id"] == 2){
            $goods = db("goods")->order("id desc")->paginate(10);
            $goods_year = db("goods")->field("goods_year_id,id")->select();
            $time = date("Y-m-d");
            foreach ($goods_year as $key=>$value){
                $year = db("year")->where("id",$value["goods_year_id"])->value("year");
                $date = date("Y-m-d",strtotime("+$year year"));
                if($time == $date){
                    $bool = db("goods")->where("id",$value["id"])->update(["goods_status"=>0,"putaway_status"=>null]);
                }
            }
            $goods_money = db("goods")->field("goods_new_money,id")->select();
            foreach ($goods_money as $k=>$val){
                $goods_ratio[] = db("goods_ratio")->where("min_money","<=",$val["goods_new_money"])->where("max_money",">=",$val["goods_new_money"])->field("ratio")->find();
                $goods_adjusted_money[] = $val["goods_new_money"]+($val["goods_new_money"] * $goods_ratio[$k]["ratio"]);
                db("goods")->where("id",$val["id"])->update(["goods_adjusted_money"=>$goods_adjusted_money[$k]]);
            }

            $year = db("year")->select();
            $user_id = Session::get("user_id");
            $role_name = db("admin")->where("id",$user_id)->select();
            $store = db("store")->select();
            return view("goods_index",["store"=>$store,"goods"=>$goods,"year"=>$year,"role_name"=>$role_name]);
        }else {
            $admin_phone = db("admin")->where("id", $admin_id)->value("phone");
            $user_id = db("user")->where("phone_num", $admin_phone)->value("id");
            $store_id = db("store")->where("user_id", $user_id)->value("store_id");
            $goods = db("goods")->order("id desc")->where("store_id", $store_id)->paginate(10);
            $goods_year = db("goods")->field("goods_year_id,id")->select();
            $time = date("Y-m-d");
            foreach ($goods_year as $key => $value) {
                $year = db("year")->where("id", $value["goods_year_id"])->value("year");
                $date = date("Y-m-d", strtotime("+$year year"));
                if ($time == $date) {
                    $bool = db("goods")->where("id", $value["id"])->update(["goods_status" => 0, "putaway_status" => null]);
                }
            }
            $goods_money = db("goods")->field("goods_new_money,id")->select();
            foreach ($goods_money as $k => $val) {
                $goods_ratio[] = db("goods_ratio")->where("min_money", "<=", $val["goods_new_money"])->where("max_money", ">=", $val["goods_new_money"])->field("ratio")->find();
                $goods_adjusted_money[] = $val["goods_new_money"] + ($val["goods_new_money"] * $goods_ratio[$k]["ratio"]);
                db("goods")->where("id", $val["id"])->update(["goods_adjusted_money" => $goods_adjusted_money[$k]]);
            }

            $year = db("year")->select();
            $user_id = Session::get("user_id");
            $role_name = db("admin")->where("id", $user_id)->select();
            $store = db("store")->select();
            return view("goods_index", ["store" => $store, "goods" => $goods, "year" => $year, "role_name" => $role_name]);
        }

    }



    /**
     * 模糊查询
     * 陈绪
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function seach(Request $request){
        $search_keys = $request->param("search_key");
        $search_bts = $request->param("search_bt");

        $search_key = isset($search_keys) ? $search_keys : '%';
        $search_bt = isset($search_bts) ? $search_bts : false;

        if ($search_key) {
            $good = db("goods")->where("goods_name", "like", "%" . $search_key . "%")->paginate(5, false,['query' => request()->param()]);
        } else {
            $good = db("goods")->paginate(5,false,['query' => request()->param()]);
            $this->assign("good", $good);
        }
        return view("goods_index", [
            'good' => $good,
            'search_key' => $search_key,
        ]);

    }


    /**
     * @param int $pin
     * 商品添加页面
     * 陈绪
     */
    public function add(Request $request,$pid=0){
        $goods_list = [];
        $goods_brand = [];
        if($pid == 0){
            $goods_list = getSelectList("goods_type");
            $goods_brand = getSelectList("brand");
        }
        $year = db("year")->select();
        if($request->isPost()){
            $car_series = db("car_series")->distinct(true)->field("brand")->select();
            $car_brand = db("car_series")->field("series,brand,year,displacement")->select();
            return ajax_success("获取成功",array("car_series"=>$car_series,"car_brand"=>$car_brand));
        }
        return view("goods_add",["year"=>$year,"goods_list"=>$goods_list,"goods_brand"=>$goods_brand]);
    }



    /**
     * [商品添加]
     * 陈绪
     * @param Request $request
     */
    public function save(Request $request)
    {
        if ($request->isPost()) {
            $goods_data = $request->param();
            if($goods_data["goods_standard"] == "通用"){
                unset($goods_data["dedicated_vehicle"]);
                unset($goods_data["goods_car_brand"]);
                unset($goods_data["dedicated_property"]);
            }
            if(!empty($goods_data["goods_standard_name"])){
                $goods_standard_name = implode(",",$goods_data["goods_standard_name"]);
                $goods_standard_value = implode(",",$goods_data["goods_standard_value"]);
                $goods_data["goods_standard_name"] = $goods_standard_name;
                $goods_data["goods_standard_value"] = $goods_standard_value;
            }
            if(!empty($goods_data["goods_delivery"])){
                $goods_delivery = implode(",",$goods_data["goods_delivery"]);
                $goods_data["goods_delivery"] = $goods_delivery;
            }

            //图片添加
            $show_images = $request->file("goods_show_images");

            if(!empty($show_images)) {
                $show_image = $show_images->move(ROOT_PATH . 'public' . DS . 'uploads');
                $goods_data["goods_show_images"] = str_replace("\\", "/", $show_image->getSaveName());
            }
            $admin_id = Session::get("user_id");
            $admin_phone = db("admin")->where("id",$admin_id)->value("phone");
            $user_id = db("user")->where("phone_num",$admin_phone)->value("id");
            $store_id = db("store")->where("user_id",$user_id)->value("store_id");
            $goods_data["store_id"] = $store_id;
            $bool = db("goods")->insert($goods_data);
            if ($bool) {
                //取出图片在存到数据库
                $goods_images = [];
                $goodsid = db("goods")->getLastInsID();
                $file = request()->file('goods_images');
                if(!empty($file)) {
                    foreach ($file as $key => $value) {
                        $info = $value->move(ROOT_PATH . 'public' . DS . 'uploads');
                        $goods_url = str_replace("\\", "/", $info->getSaveName());
                        $goods_images[] = ["goods_images" => $goods_url, "goods_id" => $goodsid];
                    }
                }
                $booldata = model("goods_images")->saveAll($goods_images);
                if ($booldata) {
                    $this->success("添加成功",url("admin/Goods/index"));
                } else {
                    $this->success("添加失败",url('admin/Goods/add'));
                }
            }
        }
    }


    /**
     * [商品修改]
     * 陈绪
     */
    public function edit(Request $request,$id){
        $goods = db("goods")->where("id",$id)->select();
        foreach ($goods as $key=>$value){
            $goods[$key]["goods_standard_name"] = explode(",",$value["goods_standard_name"]);
            $goods_standard_value = explode(",",$value["goods_standard_value"]);
            $goods_standard_value = array_chunk($goods_standard_value,8);
            $goods_delivery = explode(",",$value["goods_delivery"]);
            $goods[$key]["goods_delivery"] = $goods_delivery;
            $goods[$key]["goods_standard_value"] = $goods_standard_value;
            $goods[$key]["goods_images"] = db("goods_images")->where("goods_id",$value["id"])->select();

        }
        $goods_standard_name = array();
        foreach ($goods as $k=>$val){
            foreach ($val["goods_standard_name"] as $k_1=>$v_2){
                $goods_standard_name[$k_1] = array(
                    "goods_standard_name" =>$val["goods_standard_name"][$k_1],
                    "goods_standard_value"=>$val["goods_standard_value"][$k_1]
                );
            }
        }
        $goods_list = getSelectList("goods_type");
        $goods_brand = getSelectList("brand");
        $year = db("year")->select();
        $car_series = db("car_series")->distinct(true)->field("brand")->select();
        if($request->isPost()){
            $car_series = db("car_series")->distinct(true)->field("brand")->select();
            $car_brand = db("car_series")->field("series,brand")->select();
            return ajax_success("获取成功",array("car_series"=>$car_series,"car_brand"=>$car_brand));
        }

        return view("goods_edit",["car_series"=>$car_series,"year"=>$year,"goods_brand"=>$goods_brand,"goods_standard_name"=>$goods_standard_name,"goods"=>$goods,"goods_list"=>$goods_list,"goods_brand"=>$goods_brand]);
    }


    /**
     * [图片删除]
     * 陈绪
     */
    public function images(Request $request){
        if($request->isPost()){
            $id = $request->param();
            if(!empty($id["id"])){
                $image = db("goods")->where("id",$id["id"])->field("goods_show_images")->find();
                $bool = db("goods")->where("id",$id["id"])->update(["goods_show_images"=>null]);
                if ($bool){
                    if(!empty($image)){
                        unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$image['goods_show_images']);
                    }
                    return ajax_success("成功");
                }
            }else{
                $images_id = $request->only(["images_id"])["images_id"];
                $goods_images = db("goods_images")->where("id",$images_id)->field("goods_images")->find();
                $bool = db("goods_images")->where("id",$images_id)->delete();
                if($bool){
                    if(!empty($goods_images)){
                        unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$goods_images['goods_images']);
                    }
                    return ajax_success("成功");
                }

            }
        }
    }



    /**
     * [商品删除]
     * 陈绪
     */
    public function del(Request $request){
        $id = $request->only(["id"])["id"];
        $image_url = db("goods_images")->where("goods_id", $id)->field("goods_images,id")->select();
        $bool = db("goods")->where("id", $id)->delete();
        if($bool){
            foreach ($image_url as $value) {
                if ($value['goods_images'] != null) {
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $value['goods_images']);
                }
                $bool_data = db("goods_images")->where("id", $value['id'])->delete();
            }
            if ($bool_data) {
                $this->success("添加成功",url("admin/Goods/index"));
            } else {
                $this->success("添加失败",url('admin/Goods/add'));
            }

        }
    }



    /**
     * [产品更新]
     * 陈绪
     * @param Request $request
     */
    public function updata(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->only(["id"])["id"];
            $goods_data = $request->param();

            if($goods_data["goods_standard"] == "通用"){
                unset($goods_data["dedicated_vehicle"]);
                unset($goods_data["goods_car_brand"]);
                unset($goods_data["dedicated_property"]);
            }
            if(!empty($goods_data["goods_standard_name"])){
                $goods_standard_name = implode(",",$goods_data["goods_standard_name"]);
                $goods_standard_value = implode(",",$goods_data["goods_standard_value"]);
                $goods_data["goods_standard_name"] = $goods_standard_name;
                $goods_data["goods_standard_value"] = $goods_standard_value;
            }
            if(!empty($goods_data["goods_delivery"])){
                $goods_delivery = implode(",",$goods_data["goods_delivery"]);
                $goods_data["goods_delivery"] = $goods_delivery;
            }
            //图片添加
            $show_images = $request->file("goods_show_images");

            if(!empty($show_images)) {
                $show_image = $show_images->move(ROOT_PATH . 'public' . DS . 'uploads');
                $goods_data["goods_show_images"] = str_replace("\\", "/", $show_image->getSaveName());
            }
            $admin_id = Session::get("user_id");
            $admin_phone = db("admin")->where("id",$admin_id)->value("phone");
            $user_id = db("user")->where("phone_num",$admin_phone)->value("id");
            $store_id = db("store")->where("user_id",$user_id)->value("store_id");
            $goods_data["store_id"] = $store_id;
            $bool = db("goods")->where("id",$id)->update($goods_data);
            if ($bool) {
                //取出图片在存到数据库
                $goods_images = [];
                $file = request()->file('goods_images');
                if(!empty($file)) {
                    foreach ($file as $key => $value) {
                        $info = $value->move(ROOT_PATH . 'public' . DS . 'uploads');
                        $goods_url = str_replace("\\", "/", $info->getSaveName());
                        $goods_images[] = ["goods_images" => $goods_url, "goods_id" => $id];
                    }
                    $booldata = model("goods_images")->saveAll($goods_images);
                    if ($booldata) {
                        $this->success("更新成功",url("admin/Goods/index"));
                    } else {
                        $this->success("更新失败",url('admin/Goods/add'));
                    }
                }else{
                    $this->success("更新成功",url("admin/Goods/index"));
                }

            }
        }

    }


    /**
     * [商品状态]
     * 陈绪
     */
    public function status(Request $request){

        if($request->isPost()) {
            $status = $request->only(["status"])["status"];
            if($status == 0) {
                $id = $request->only(["id"])["id"];
                $admin_id = Session::get("user_id");
                foreach ($id as $value){
                    if($admin_id == 2){
                        $bool = db("goods")->where("id", $value)->update(["goods_status" => 0]);
                    }else{
                        $bool = db("goods")->where("id", $value)->update(["goods_status" => 0]);
                    }
                }
                if ($bool) {
                    return ajax_success("成功");
                } else {
                    return ajax_error("失败");
                }

            }
            if($status == 1){
                $id = $request->only(["id"])["id"];
                $admin_id = Session::get("user_id");
                foreach ($id as $val){
                    $goods = db("goods")->where("id",$val)->field("putaway_status")->find();
                    if($admin_id == 2 || $goods["putaway_status"] != null){
                        $bool = db("goods")->where("id", $val)->update(["goods_status" => 1,"putaway_status"=>1]);
                    }
                }
                if ($bool) {
                    return ajax_success("成功");
                } else {
                    return ajax_error("失败");
                }

            }
        }

    }




    /**
     * [商品批量删除]
     * 陈绪
     */
    public function batches(Request $request){
        if($request->isPost()) {
            $id = $request->only(["ids"])["ids"];
            foreach ($id as $value) {
                $goods_images = db("goods_images")->where("goods_id", $value)->select();
                foreach ($goods_images as $val) {
                    if ($val['goods_images'] != null) {
                        unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $val['goods_images']);
                    }
                    GoodsImages::destroy($val['id']);
                }
                $bool = Good::destroy($value);
            }
            if ($bool) {
                return ajax_success("删除成功");
            } else {
                return ajax_error("删除失败");
            }

        }
    }




    /**
     * 商品付费详情
     * 陈绪
     */
    public function pay($id){


        return view("goods_pay");

    }




    /**
     * 商品确认付费
     * 陈绪
     */
    public function affirm(){

        return view("affirm_pay");

    }




    /**
     * 商品查看
     * 陈绪
     */
    public function look(Request $request,$id){

        $goods = db("goods")->where("id",$id)->select();
        foreach ($goods as $key=>$value){
            $goods[$key]["goods_standard_name"] = explode(",",$value["goods_standard_name"]);
            $goods_standard_value = explode(",",$value["goods_standard_value"]);
            $goods_standard_value = array_chunk($goods_standard_value,8);
            $goods[$key]["goods_standard_value"] = $goods_standard_value;
            $goods[$key]["goods_images"] = db("goods_images")->where("goods_id",$value["id"])->select();

        }
        $goods_standard_name = array();
        foreach ($goods as $k=>$val){
            foreach ($val["goods_standard_name"] as $k_1=>$v_2){
                $goods_standard_name[$k_1] = array(
                    "goods_standard_name" =>$val["goods_standard_name"][$k_1],
                    "goods_standard_value"=>$val["goods_standard_value"][$k_1]
                );
            }
        }
        $goods_list = getSelectList("goods_type");
        $goods_brand = getSelectList("brand");
        $year = db("year")->select();
        if($request->isPost()){
            $car_series = db("car_series")->distinct(true)->field("brand")->select();
            $car_brand = db("car_series")->field("series,brand")->select();
            return ajax_success("获取成功",array("car_series"=>$car_series,"car_brand"=>$car_brand));
        }
        return view("goods_look",["year"=>$year,"goods_brand"=>$goods_brand,"goods_standard_name"=>$goods_standard_name,"goods"=>$goods,"goods_list"=>$goods_list,"goods_brand"=>$goods_brand]);
    }



    /**
     * 通用商品规格名添加
     * 陈绪
     */
    public function name(Request $request){

        if($request->isPost()){
            $standard_name = $request->only(["goods_name"])["goods_name"];
            $standard =  db("goods_standard_name")->where("standard_name",$standard_name)->select();
            if(empty($standard)){
                $goods_name_bool = db("goods_standard_name")->insert(["standard_name"=>$standard_name]);
                if($goods_name_bool){
                    $goods_name = db("goods_standard_name")->order("id desc")->select();
                    return ajax_success("成功",$goods_name);
                }else{
                   return 2;
                }

            }else{
                return ajax_error("已存在");
            }
        }

    }





    /**
     * 通用商品规格名显示
     * 陈绪
     */
    public function standard_name(Request $request){

        if($request->isPost()){
            $goods_name = db("goods_standard_name")->order("id desc")->select();
            if($goods_name){
                return ajax_success("获取成功",$goods_name);
            }else{
                return ajax_error("失败");
            }

        }

    }




    /**
     * 专用商品属性入库
     * 陈绪
     */
    public function property_name(Request $request){

        if($request->isPost()){
            $property_name = $request->only(["property_name"])["property_name"];
            $property = db("goods_property_name")->where("property_name",$property_name)->select();
            if(empty($property)){
                $bool = db("goods_property_name")->insert(["property_name"=>$property_name]);
                if($bool){
                    $goods_property_name = db("goods_property_name")->order("id desc")->select();
                    return ajax_success("成功",$goods_property_name);
                }else{
                    return 2;
                }
            }else{
                return ajax_error("已存在");
            }
        }

    }





    /**
     * 专用商品属性显示
     * 陈绪
     */
    public function property_show(Request $request){

        if($request->isPost()){
            $property_name = db("goods_property_name")->order("id desc")->select();
            if($property_name){
                return ajax_success("获取成功",$property_name);
            }else{
                return ajax_error("失败");
            }

        }

    }




    /**
     * 角色检测
     * 陈绪
     */
    public function role_name(Request $request){

        if($request->isPost()) {
            $user_id = Session::get("user_id");
            $admin = db("admin")->where("id", $user_id)->select();
            return ajax_success("获取成功",array("admin"=>$admin));
        }

    }




    /**
     * 商品提交订单
     * 陈绪
     */
    public function alipay(Request $request){

        $config = array (
            //应用ID,您的APPID。
            'app_id' => "2018120762470526",

            //商户私钥，您的原始格式RSA私钥
            'merchant_private_key' => "MIIEpAIBAAKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQABAoIBAQCBQK730TFmpuTOtc669y6BOzUX1EWe+C/mYO28Dn7vqUGbU7UkuihtQIpcNCHhhGAXIHEH0zzrMH3b8XXdXjmo2ChBstr7elJlX2a7WYf9kHNTfRDCE+q5Xj7niSSYE6HOgvWDFMg9nyE3P0WRmTeEvjfVsv2SMoxxIBd8yD1Vxr3Gbg+gT8zWDrqXQ1Ap1gg5jNS14CFE3uKKwQ4n5JZWnIQ+jw3LZcpk9Eb/mrQ9kbnU7g0ikx8sYJpTiP7lAlb3dq1tdUmRV8+HfWYC/a8MbZtO6UyDWvms5Lb5g4we7FCmBAkG+zv62PxG9sQAvrQoSwKTOj/7LSeTgJsT97QNAoGBAPuQUNZEhVODVhCCISg84TGi0BozU64PqegJXFxbR++hQC2EsN6L2Mk2ftpd+J/9XRD0ffcBMea+H4N7ui4Y+OHoED/8d76dTX06PWfAYYJMu/o65c3IBSBiwgREuRo38a20CZ8hKr8LVpLXbtCB8WJ1kp5QeqqSPpwnjFncyBorAoGBAMu3Hokjze+FPpeFQ3tYVt9G/VSAhRMVAb5ZQClQH9plpVM9aMukp8jiaeSBg7d5RzNRGRU5ouKQ1AVs3jkgvVzUWRMKM+VkW4lzAhEkM766egpzngs9z4YXHcBW1bPJQap2TVLRcFmueDsVABXF5XZSgAwenBhtvmZ9X/UDCD+LAoGBALmXaOwLNUm9lVsshgXHlGQoN9t8jnnV+IXFkixY86NolY5/XHVzOwaHe+LifTCbnXOKzPvUF9qh3WIFf//OUJ9ps8NhIX6xUp/WvcKzfbzBm9Uqaqv8qzuPYJABm4YqS9TZBFgwAfdcCAzhf1G47Dq1fuvpd/YrWqGd07/gUIhtAoGAHDSkg7RzZQB75BrNdxyKGqwHk1WgFz5HWYWd/ppbbq+4LkhIZDnOCWBf7QWJqTOfihlmcavjQ59t27pxIlPIJDw6gQpemRpGGkfUN29dwsCq+Rt8/G14eEZnFiRvvk7VSrbKifb5qVEg0H1d36Xg2Xsew47Ragh33lTpnlDnKXUCgYBIuk9VU3DkITWsy+xiQbN4eQqbiFB7BA55xIjwPqK8K+0PVzRyObUEF6m9KSz2mEB1CHwr1fHj8qzJ/0CgKUeCONm5crLEGCGMbGUzMloGmVLSJz6+4xT8mwKOv/BcpTqkDLx+8HBaJppJnjWn0OmHLNa1JhAaVuef8eheH546kw==",

            //异步通知地址
            'notify_url' => "http://localhost/automobile/public/admin/goods_pay_code",

            //同步跳转
            'return_url' => "http://localhost/automobile/public/admin/goods_pay_code",

            //编码格式
            'charset' => "UTF-8",

            //签名方式
            'sign_type'=>"RSA2",

            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQAB",

        );

        //Loader::import("Alipay.wappay.buildermodel.AlipayTradeWapPayContentBuilder");
        //Loader::import('Alipay.wappay.service.AlipayTradeService');
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = date("YmdHis").uniqid();

        //订单名称，必填
        $subject = $_POST['WIDsubject'];
        //付款金额，必填
        $total_amount = $_POST['WIDtotal_amount'];

        //商品描述，可空
        $body = $_POST['WIDbody'];
        //超时时间
        $timeout_express="1m";
        include('../extend/AliPay/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php');

        $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setTimeExpress($timeout_express);
        include('../extend/AliPay/wappay/service/AlipayTradeService.php');

        $payResponse = new \AlipayTradeService($config);
        $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
        Session("goods_id",$body);
        return ;


    }


    /**
     * 回调地址
     * 陈绪
     * @param Request $request
     */
    public function pay_code(Request $request){

        if($request->isGet()){
            $id = Session::get("goods_id");
            $goods_id = explode(",",$id);
            foreach ($goods_id as $value){
                $bool = db("goods")->where("id",$value)->update(["goods_status"=>1,"putaway_status"=>1]);
            }
            if($bool){
                $this->success("上架成功",url("admin/Goods/index"));
            }else{
                $this->error("上架失败",url("admin/Goods/index"));
            }
        }
    }




    /**
     * 专用适用车型编辑显示
     * 陈绪
     */
    public function edit_show(Request $request){

        if($request->isPost()){
            $id = $request->only(["id"])["id"];
            $goods = db("goods")->where("id",$id)->field("dedicated_vehicle,goods_car_year,goods_car_displacement,goods_car_series")->select();
            foreach ($goods as $key=>$value){

                $goods[$key]["goods_car_year"] = explode(",",$value["goods_car_year"]);
                $goods[$key]["goods_car_displacement"] = explode(",",$value["goods_car_displacement"]);
                $goods[$key]["goods_car_series"] = explode(",",$value["goods_car_series"]);

            }
            if($goods){
                return ajax_success("获取成功",$goods);
            }else{
                return ajax_error("获取失败");
            }
        }

    }





    /**
     * 微信支付
     * 陈绪
     */
    public function WeiAlpay(Request $request){

        /*$out_trade_no = date("YmdHis").uniqid();

        //订单名称，必填
        $subject = $_POST['WIDsubject'];
        //付款金额，必填
        $total_amount = $_POST['WIDtotal_amount'];

        //商品描述，可空
        $body = $_POST['WIDbody'];*/


        header("Content-type: text/html; charset=utf-8");
        ini_set('date.timezone','Asia/Shanghai');

        include ("../extend/WxpayAPI/lib/WxPay.Api.php");
        include ('../extend/WxpayAPI/example/WxPay.NativePay.php');
        include ('../extend/WxpayAPI/example/log.php');

        /**
         * 流程：
         * 1、组装包含支付信息的url，生成二维码
         * 2、用户扫描二维码，进行支付
         * 3、确定支付之后，微信服务器会回调预先配置的回调地址，在【微信开放平台-微信支付-支付配置】中进行配置
         * 4、在接到回调通知之后，用户进行统一下单支付，并返回支付信息以完成支付（见：native_notify.php）
         * 5、支付完成之后，微信服务器会通知支付成功
         * 6、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
         */
        $notify = new \NativePay();
//$url1 = $notify->GetPrePayUrl("123456789");

//模式二
        /**
         * 流程：
         * 1、调用统一下单，取得code_url，生成二维码
         * 2、用户扫描二维码，进行支付
         * 3、支付完成之后，微信服务器会通知支付成功
         * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
         */



        $input = new \WxPayUnifiedOrder();
        /**
         * 设置商品或支付单简要描述
         */
        $input->SetBody("测试商品支付");
        /**
         * 设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
         */
        $input->SetAttach("ceshidingdan1");
        /**
         * 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
         */
        $input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
        /**
         * 设置订单总金额，只能为整数，详见支付金额
         * @param string $value
         **/
        $input->SetTotal_fee("1");
        /**
         * 设置订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则
         * @param string $value
         **/
        $input->SetTime_start(date("YmdHis"));
        /**
         * 设置订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则
         * @param string $value
         **/
        $input->SetTime_expire(date("YmdHis", time() + 600));
        /**
         * 设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
         * @param string $value
         **/
        $input->SetGoods_tag("无");
        /**
         * 设置接收微信支付异步通知回调地址
         * @param string $value
         **/
        $input->SetNotify_url("http://localhost/automobile/public/indexs");
        /**
         * 设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
         * @param string $value
         **/
        $input->SetTrade_type("NATIVE");
        /**
         * 设置trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
         * @param string $value
         **/
        $input->SetProduct_id("123456789");
        /**
         * 生成直接支付url，支付url有效期为2小时,模式二
         * @param UnifiedOrderInput $input
         */

        $result = $notify->GetPayUrl($input);
        $url2 = $result["code_url"];

        return ajax_success("获取成功",$url2);
        //return view("WeiAlpay_code",["url2"=>$url2]);


    }


    public function qrcode(){

        error_reporting(E_ERROR);
        include ('../extend/WxpayAPI/example/phpqrcode/phpqrcode.php');
        $url = urldecode($_GET["url2"]);
        \QRcode::png($url);

    }



}