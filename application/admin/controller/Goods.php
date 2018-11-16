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

class Goods extends Controller{

    public $goods_status = [0,1];
    /**
     * [商品列表]
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     * 陈绪
     */
    public function index(Request $request){
        $datemins = $request->param("datemin");
        $datemaxs = $request->param("datemax");
        $search_keys = $request->param("search_key");
        $search_bts = $request->param("search_bt");
        $datemin = isset($datemins) ? $datemins : false;
        $datemax = isset($datemaxs) ? $datemaxs : false;
        $search_key = isset($search_keys) ? $search_keys : '%';
        $search_bt = isset($search_bts) ? $search_bts : false;
        if($request->isPost()) {
            if ($datemin && $datemax) {
               $good = db("goods")->where('create_time','>',strtotime($datemin))->where('create_time','<',strtotime($datemax))->paginate(5);
            }

            if ($search_key) {
                $good = db("goods")->where("goods_name","like","%".$search_key."%")->paginate(5);

            }else {
                $good = db("goods")->paginate(5);
            }

            return view("goods_index", [
                'good' => $good,
                'search_key' => $search_key,
                'datemax' => $datemax,
                'datemin' => $datemin
            ]);
        }else{
            $goods = db("goods")->paginate(10);
            $year = db("year")->select();
            return view("goods_index",["goods"=>$goods,"year"=>$year]);
        }

    }


    /**
     * @param int $pin
     * 商品添加页面
     * 陈绪
     */
    public function add($pid=0){
        $goods_list = [];
        $goods_brand = [];
        if($pid == 0){
            $goods_list = getSelectList("goods_type");
            $goods_brand = getSelectList("brand");
        }
        $year = db("year")->select();
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
            $goods_standard_name = implode(",",$goods_data["goods_standard_name"]);
            $goods_standard_value = implode(",",$goods_data["goods_standard_value"]);
            $goods_data["goods_standard_name"] = $goods_standard_name;
            $goods_data["goods_standard_value"] = $goods_standard_value;
            $goods_delivery = implode(",",$goods_data["goods_delivery"]);
            $goods_data["goods_delivery"] = $goods_delivery;
            //图片添加
            $show_images = $request->file("goods_show_images");

            if(!empty($show_images)) {
                $show_image = $show_images->move(ROOT_PATH . 'public' . DS . 'uploads');
                $goods_data["goods_show_images"] = str_replace("\\", "/", $show_image->getSaveName());
            }

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
        $goods_brand = db("brand")->select();
        return view("goods_edit",["goods_standard_name"=>$goods_standard_name,"goods"=>$goods,"goods_list"=>$goods_list,"goods_brand"=>$goods_brand]);
    }


    /**
     * [图片删除]
     * 陈绪
     */
    public function images(Request $request){
        if($request->isPost()){
            $id = $request->only(['id'])['id'];
            if(!empty($id)){
                $image = db("goods")->where("id",$id)->field("goods_show_images")->find();
                $bool = db("goods")->where("id",$id)->update(["goods_show_images"=>null]);
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
            $goods_standard_name = implode(",",$goods_data["goods_standard_name"]);
            $goods_standard_value = implode(",",$goods_data["goods_standard_value"]);
            $goods_data["goods_standard_name"] = $goods_standard_name;
            $goods_data["goods_standard_value"] = $goods_standard_value;
            $goods_delivery = implode(",",$goods_data["goods_delivery"]);
            $goods_data["goods_delivery"] = $goods_delivery;
            //图片添加
            $show_images = $request->file("goods_show_images");

            if(!empty($show_images)) {
                $show_image = $show_images->move(ROOT_PATH . 'public' . DS . 'uploads');
                $goods_data["goods_show_images"] = str_replace("\\", "/", $show_image->getSaveName());
            }
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
                }
                $booldata = model("goods_images")->saveAll($goods_images);
                if ($booldata) {
                    $this->success("更新成功",url("admin/Goods/index"));
                } else {
                    $this->success("更新失败",url('admin/Goods/add'));
                }
            }
        }

    }


    /**
     * [商品状态]
     * 陈绪
     */
    public function status(Request $request){

        if ($request->isPost()){
            if($request->isPost()) {
                $status = $request->only(["status"])["status"];
                if($status == 0) {
                    $id = $request->only(["id"])["id"];
                    $bool = db("goods")->where("id", $id)->update(["goods_status" => 0]);
                    if ($bool) {
                        return ajax_success("成功");
                    } else {
                        return ajax_error("失败");
                    }
                }
                if($status == 1){
                    $id = $request->only(["id"])["id"];
                    $bool = db("goods")->where("id", $id)->update(["goods_status" => 1]);
                    if ($bool) {
                        return ajax_success("成功");
                    } else {
                        return ajax_error("失败");
                    }
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
                $goods_url = db("goods")->where("id", $value)->find();
                $goods_images = db("goods_images")->where("goods_id", $value)->select();
                if($goods_url['goods_show_images'] != null){
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $goods_url['goods_show_images']);
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $goods_url['goods_parts_big_img']);
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $goods_url['goods_spec_img']);
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $goods_url['goods_parts_img']);
                }
                foreach ($goods_images as $val) {
                    if ($val['goods_images'] != null) {
                        unlink(ROOT_PATH . 'public' . DS . 'upload/' . $val['goods_images']);
                    }
                    if ($val['goods_quality_img'] != null) {
                        unlink(ROOT_PATH . 'public' . DS . 'upload/' . $val['goods_quality_img']);
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
        $goods_brand = db("brand")->select();
        return view("good_look",["goods_standard_name"=>$goods_standard_name,"goods"=>$goods,"goods_list"=>$goods_list,"goods_brand"=>$goods_brand]);

    }



    /**
     * 通用商品规格名添加
     * 陈绪
     */
    public function name(Request $request){

        if($request->isPost()){
            $standard_name = $request->only(["goods_name"])["goods_name"];
            $goods_name_bool = db("standard_name")->insert(["standard_name"=>$standard_name]);
            if($goods_name_bool){
                $goods_name = db("standard_name")->order("id desc")->select();
                return ajax_success("成功",$goods_name);
            }else{
                return ajax_error("失败",$standard_name);
            }

        }

    }





    /**
     * 通用商品规格名显示
     * 陈绪
     */
    public function standard_name(Request $request){

        if($request->isPost()){
            $goods_name = db("standard_name")->order("id desc")->select();
            if($goods_name){
                return ajax_success("获取成功",$goods_name);
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
            return ajax_success("获取成功",$admin);
        }

    }



}