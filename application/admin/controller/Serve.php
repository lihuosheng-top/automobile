<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/22
 * Time: 10:34
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Session;
use app\admin\model\ServeGoods;

class Serve extends Controller{


    /**
     * 服务商品管理
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index(){
        $admin_id = Session::get("user_id");
        $admin_role = db("admin")->where("id",$admin_id)->field("role_id")->find();
        if($admin_role["role_id"] == 2){

            $serve_goods = db("serve_goods")->order("id desc")->select();
            foreach ($serve_goods as $key=>$value){
                $serve_goods[$key]["serve_goods_name"] = db("service_setting")->where("service_setting_id",$value["service_setting_id"])->value("service_setting_name");
            }
            $store = db("store")->select();
            return view("serve_index",["store"=>$store,"serve_goods"=>$serve_goods]);

        }else {
            $admin_phone = db("admin")->where("id", $admin_id)->value("phone");
            $user_id = db("user")->where("phone_num", $admin_phone)->value("id");
            $store_id = db("store")->where("user_id", $user_id)->value("store_id");
            $serve_goods = db("serve_goods")->order("id desc")->where("store_id", $store_id)->select();
            foreach ($serve_goods as $key => $value) {
                $serve_goods[$key]["serve_goods_name"] = db("service_setting")->where("service_setting_id", $value["service_setting_id"])->value("service_setting_name");
            }
            $store = db("store")->select();
            return view("serve_index", ["store" => $store, "serve_goods" => $serve_goods]);
        }

    }



    /**
     * 服务商品添加
     * 陈绪
     */
    public function add(){

        $car = db("car_series")->distinct(true)->field("vehicle_model")->select();
        $data = [];
        foreach ($car as $key=>$value){
            $arr[] = trim($value["vehicle_model"]);
            $data = array_unique($arr);
        }
        foreach ($data as $k=>$val){
            if(empty($val)){
                unset($data[$k]);
            }
        }
        $service_setting = db("service_setting")->select();
        return view("serve_add",["data"=>$data,"service_setting"=>$service_setting]);

    }



    /**
     * 服务商品查看详情
     * 陈绪
     */
    public function look($id){

        $car = db("car_series")->distinct(true)->field("vehicle_model")->select();
        $data = [];
        foreach ($car as $key=>$value){
            $arr[] = trim($value["vehicle_model"]);
            $data = array_unique($arr);
        }
        foreach ($data as $k=>$val){
            if(empty($val)){
                unset($data[$k]);
            }
        }
        $service_setting = db("service_setting")->select();
        $serve = db("serve_goods")->where("id",$id)->select();
        return view("serve_look",["data"=>$data,"service_setting"=>$service_setting,"serve"=>$serve]);

    }



    /**
     * 服务商品入库
     * 陈绪
     */
    public function save(Request $request){

        $serve_goods = $request->param();
        $admin_id = Session::get("user_id");
        $admin_phone = db("admin")->where("id",$admin_id)->value("phone");
        $user_id = db("user")->where("phone_num",$admin_phone)->value("id");
        $store_id = db("store")->where("user_id",$user_id)->value("store_id");
        $serve_goods["store_id"] = $store_id;
        $bool = db("serve_goods")->insert($serve_goods);
        if($bool){
            $this->success("保存成功",url("admin/Serve/index"));
        }else{
            $this->error("保存失败",url("admin/Serve/index"));
        }

    }



    /**
     * 服务商品编辑
     * 陈绪
     */
    public function edit($id){

        $car = db("car_series")->distinct(true)->field("vehicle_model")->select();
        $data = [];
        foreach ($car as $key=>$value){
            $arr[] = trim($value["vehicle_model"]);
            $data = array_unique($arr);
        }
        foreach ($data as $k=>$val){
            if(empty($val)){
                unset($data[$k]);
            }
        }
        $service_setting = db("service_setting")->select();
        $serve = db("serve_goods")->where("id",$id)->select();
        return view("serve_edit",["data"=>$data,"service_setting"=>$service_setting,"serve"=>$serve]);


    }


    /**
     * 服务商品更新
     * 陈绪
     * @param Request $request
     */
    public function updata(Request $request){

        $id = $request->only(["id"])["id"];
        $serve_goods = $request->param();
        $admin_id = Session::get("user_id");
        $admin_phone = db("admin")->where("id",$admin_id)->value("phone");
        $user_id = db("user")->where("phone_num",$admin_phone)->value("id");
        $store_id = db("store")->where("user_id",$user_id)->value("store_id");
        $serve_goods["store_id"] = $store_id;
        $bool = db("serve_goods")->where("id",$id)->update($serve_goods);
        if($bool){
            $this->success("更新成功",url("admin/Serve/index"));
        }else{
            $this->error("更新失败",url("admin/Serve/index"));
        }
    }




    /**
     * 服务商品删除
     * 陈绪
     */
    public function del($id){

        $bool = db("serve_goods")->where("id",$id)->delete();
        if($bool){
            $this->success("删除成功",url("admin/Serve/index"));
        }else{
            $this->error("删除失败",url("admin/Serve/index"));
        }

    }



    /**
     * [服务商品批量删除]
     * 陈绪
     */
    public function batches(Request $request){
        if($request->isPost()) {
            $id = $request->only(["ids"])["ids"];
            foreach ($id as $value) {
                $bool = ServeGoods::destroy($value);
            }
            if ($bool) {
                return ajax_success("删除成功");
            } else {
                return ajax_error("删除失败");
            }

        }
    }




    /**
     * [服务添加商品检测]
     * 陈绪
     */
    public function serve_show(Request $request){

        if($request->isPost()){
            $service_setting_id = $request->only(["service_setting_id"])["service_setting_id"];
            $vehicle_model = $request->only(["vehicle_model"])["vehicle_model"];
            $serve_data = db("serve_goods")->where("service_setting_id",$service_setting_id)->where("vehicle_model",$vehicle_model)->find();
            if($serve_data["service_money"] != null){
                return ajax_success("已经添加过相应的服务商品");
            }else{
                return ajax_error("没有添加过相应的服务商品");
            }
        }

    }

}