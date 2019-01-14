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
use think\paginator\driver\Bootstrap;

class Goods extends Controller
{

    public $goods_status = [0, 1];
    /**
     * [商品列表]
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     * 陈绪
     */
    public function index(Request $request)
    {
        $admin_id = Session::get("user_id");
        $admin_role = db("admin")->where("id", $admin_id)->field("role_id")->find();
        if ($admin_role["role_id"] == 2) {
            $goods = db("goods")->order("id desc")->select();
            $goods_year = db("goods")->field("goods_year_id,id")->select();
            $time = date("Y-m-d");
            foreach ($goods_year as $key => $value) {
                $year = db("year")->where("id", $value["goods_year_id"])->value("year");
                $date = date("Y-m-d", strtotime("+$year year"));
                if ($time == $date) {
                    $bool = db("goods")->where("id", $value["id"])->update(["goods_status" => 0, "putaway_status" => null]);
                }
            }
            // $goods_money = db("goods")->field("goods_new_money,id")->select();
            
            // foreach ($goods_money as $k => $val) {
            //     $goods_ratio[] = db("goods_ratio")->where("min_money", "<=", $val["goods_new_money"])->where("max_money", ">=", $val["goods_new_money"])->field("ratio")->find();
            //     $goods_adjusted_money[] = $val["goods_new_money"] + ($val["goods_new_money"] * $goods_ratio[$k]["ratio"]);
            //     db("goods")->where("id", $val["id"])->update(["goods_adjusted_money" => $goods_adjusted_money[$k]]);
            // }
   
            foreach ($goods as $key => $value) {
                $max[$key] = db("special")->where("goods_id", $goods[$key]['id'])->max("price");//最高价格
                $min[$key] = db("special")->where("goods_id", $goods[$key]['id'])->min("price");//最低价格
                $goods[$key]["goods_repertory"] = db("special")->where("goods_id", $goods[$key]['id'])->sum("stock");//库存
                $goods[$key]["max_price"] = $max[$key];
                $goods[$key]["min_price"] = $min[$key];
                        
            }
            //调整规格后的价格显示
            $adjusted_price = db("special")->field("price,id")->select();          
            foreach ($adjusted_price as $kw => $vl) {
                $ratio[] = db("goods_ratio")->where("min_money", "<=", $vl["price"])->where("max_money", ">=", $vl["price"])->field("ratio")->find();
                $goods_adjusted_moneys[] = $vl["price"] + ($vl["price"] * $ratio[$kw]["ratio"]);
                db("special")->where("id", $vl["id"])->update(["goods_adjusted_price" => $goods_adjusted_moneys[$kw]]);
            }

            $year = db("year")->select();
            $user_id = Session::get("user_id");
            $role_name = db("admin")->where("id", $user_id)->select();
            $store = db("store")->select();
            //halt($goods);
            $all_idents = $goods;//这里是需要分页的数据
            $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
            $listRow = 20;//每页20行记录
            $showdata = array_slice($all_idents, ($curPage - 1) * $listRow, $listRow, true);// 数组中根据条件取出一段值，并返回
            $goods = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
                'var_page' => 'page',
                'path' => url('admin/Category/index'),//这里根据需要修改url
                'query' => [],
                'fragment' => '',
            ]);
            $goods->appends($_GET);
            $this->assign('listpage', $goods->render());
            return view("goods_index", ["store" => $store, "goods" => $goods, "year" => $year, "role_name" => $role_name]);
        } else {
            $admin_phone = db("admin")->where("id", $admin_id)->value("phone");
            $user_id = db("user")->where("phone_num", $admin_phone)->value("id");
            $store_id = db("store")->where("user_id", $user_id)->value("store_id");
            $goods = db("goods")->order("id desc")->where("store_id", $store_id)->select();
            $goods_year = db("goods")->field("goods_year_id,id")->select();
            $time = date("Y-m-d");
            foreach ($goods_year as $key => $value) {
                $year = db("year")->where("id", $value["goods_year_id"])->value("year");
                $date = date("Y-m-d", strtotime("+$year year"));
                if ($time == $date) {
                    $bool = db("goods")->where("id", $value["id"])->update(["goods_status" => 0, "putaway_status" => null]);
                }
            }


            foreach ($goods as $key => $value) {
                $max[$key] = db("special")->where("goods_id", $goods[$key]['id'])->max("price");//最高价格
                $min[$key] = db("special")->where("goods_id", $goods[$key]['id'])->min("price");//最低价格
                $goods[$key]["goods_repertory"] = db("special")->where("goods_id", $goods[$key]['id'])->sum("stock");//库存
                $goods[$key]["max_price"] = $max[$key];
                $goods[$key]["min_price"] = $min[$key];
                        
            }
            // $goods_money = db("goods")->field("goods_new_money,id")->select();
            // foreach ($goods_money as $k => $val) {
            //     $goods_ratio[] = db("goods_ratio")->where("min_money", "<=", $val["goods_new_money"])->where("max_money", ">=", $val["goods_new_money"])->field("ratio")->find();
            //     $goods_adjusted_money[] = $val["goods_new_money"] + ($val["goods_new_money"] * $goods_ratio[$k]["ratio"]);
            //     db("goods")->where("id", $val["id"])->update(["goods_adjusted_money" => $goods_adjusted_money[$k]]);
            // }
            //调整规格后的价格显示
            $adjusted_price = db("special")->field("price,id")->select();          
            foreach ($adjusted_price as $kw => $vl) {
                $ratio[] = db("goods_ratio")->where("min_money", "<=", $vl["price"])->where("max_money", ">=", $vl["price"])->field("ratio")->find();
                $goods_adjusted_moneys[] = $vl["price"] + ($vl["price"] * $ratio[$kw]["ratio"]);
                db("special")->where("id", $vl["id"])->update(["goods_adjusted_price" => $goods_adjusted_moneys[$kw]]);
            }

            $year = db("year")->select();
            $user_id = Session::get("user_id");
            $role_name = db("admin")->where("id", $user_id)->select();
            $store = db("store")->select();
            //halt($goods);
            $all_idents = $goods;//这里是需要分页的数据
            $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
            $listRow = 20;//每页20行记录
            $showdata = array_slice($all_idents, ($curPage - 1) * $listRow, $listRow, true);// 数组中根据条件取出一段值，并返回
            $goods = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
                'var_page' => 'page',
                'path' => url('admin/Category/index'),//这里根据需要修改url
                'query' => [],
                'fragment' => '',
            ]);
            $goods->appends($_GET);
            $this->assign('listpage', $goods->render());
            return view("goods_index", ["store" => $store, "goods" => $goods, "year" => $year, "role_name" => $role_name]);
        }

    }



    /**
     * 模糊查询
     * 陈绪
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function seach(Request $request)
    {
        $search_keys = $request->param("search_key");
        $search_bts = $request->param("search_bt");

        $search_key = isset($search_keys) ? $search_keys : '%';
        $search_bt = isset($search_bts) ? $search_bts : false;

        if ($search_key) {
            $good = db("goods")->where("goods_name", "like", "%" . $search_key . "%")->paginate(5, false, ['query' => request()->param()]);
        } else {
            $good = db("goods")->paginate(5, false, ['query' => request()->param()]);
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
    public function add(Request $request, $pid = 0)
    {
        $goods_list = [];
        $goods_brand = [];
        if ($pid == 0) {
            $goods_list = getSelectList("goods_type");
            $goods_brand = getSelectList("brand");
        }
        $year = db("year")->select();
        if ($request->isPost()) {
            $car_series = db("car_series")->distinct(true)->field("brand")->select();
            $car_brand = db("car_series")->field("series,brand,year,displacement")->select();
            return ajax_success("获取成功", array("car_series" => $car_series, "car_brand" => $car_brand));
        }
        return view("goods_add", ["year" => $year, "goods_list" => $goods_list, "goods_brand" => $goods_brand]);
    }



    /**
     * [商品添加]
     * 陈绪
     * @param Request $request
     */
    public function save(Request $request)
    {
        if ($request->isPost()) {
            $goods_special = [];
            $goods_data = $request->param();
            if ($goods_data["goods_standard"] == "通用") {
                unset($goods_data["dedicated_vehicle"]);
                unset($goods_data["goods_car_brand"]);
                unset($goods_data["dedicated_property"]);
            }
            if (!empty($goods_data["goods_standard_name"])) {
                $goods_standard_name = implode(",", $goods_data["goods_standard_name"]);
                $goods_standard_value = implode(",", $goods_data["goods_standard_value"]);
                $goods_data["goods_standard_name"] = $goods_standard_name;
                $goods_data["goods_standard_value"] = $goods_standard_value;
            }
            if (!empty($goods_data["goods_delivery"])) {
                $goods_delivery = implode(",", $goods_data["goods_delivery"]);
                $goods_data["goods_delivery"] = $goods_delivery;
            }
            //图片添加
            $show_images = $request->file("goods_show_images");
            $imgs = $request->file("imgs");

            if (!empty($show_images)) {
                $show_image = $show_images->move(ROOT_PATH . 'public' . DS . 'uploads');
                $goods_data["goods_show_images"] = str_replace("\\", "/", $show_image->getSaveName());
            }
            $admin_id = Session::get("user_id");
            $admin_phone = db("admin")->where("id", $admin_id)->value("phone");
            $user_id = db("user")->where("phone_num", $admin_phone)->value("id");
            $store_id = db("store")->where("user_id", $user_id)->value("store_id");
            $goods_data["store_id"] = $store_id;
            $result = implode(",", $goods_data["lv1"]);

            
            foreach ($goods_data as $kt => $vq) {
                if (!(is_array($vq))) {
                    $goods_special[$kt] = $vq;
                }
            }

            $goods_id = db('goods')->insertGetId($goods_special);
            if (!empty($goods_data)) {
                foreach ($goods_data as $kn => $nl) {
                    if (substr($kn, 0, 3) == "sss") {
                        $price[] = $nl["price"];
                        $stock[] = $nl["stock"];
                        $coding[] = $nl["coding"];
                        $cost[] = $nl["cost"];
                        if (isset($nl["status"])) {
                            $status[] = $nl["status"];
                        } else {
                            $status[] = "0";
                        }
                    }
                }

            }

            if (!empty($imgs)) {
                foreach ($imgs as $k => $v) {
                    $shows = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                    $tab = str_replace("\\", "/", $shows->getSaveName());

                    if (is_array($goods_data)) {
                        foreach ($goods_data as $key => $value) {
                            if (substr($key, 0, 3) == "sss") {
                                $str[] = substr($key, 3);
                                $values[$k]["name"] = $str[$k];
                                $values[$k]["price"] = $price[$k];
                                $values[$k]["lv1"] = $result;
                                $values[$k]["stock"] = $stock[$k];
                                $values[$k]["coding"] = $coding[$k];
                                $values[$k]["status"] = $status[$k];
                                $values[$k]["cost"] = $cost[$k];
                                $values[$k]["images"] = $tab;
                                $values[$k]["goods_id"] = $goods_id;

                            }

                        }
                    }

                }

                foreach ($values as $kz => $vw) {
                    $rest = db('special')->insert($vw);

                }
            }
            if ($goods_id) {
                //取出图片在存到数据库
                $goods_images = [];
                $file = request()->file('goods_images');
                if (!empty($file)) {
                    foreach ($file as $key => $value) {
                        $info = $value->move(ROOT_PATH . 'public' . DS . 'uploads');
                        $goods_url = str_replace("\\", "/", $info->getSaveName());
                        $goods_images[] = ["goods_images" => $goods_url, "goods_id" => $goods_id];
                    }
                }
                $booldata = model("goods_images")->saveAll($goods_images);
            }
            
            if ($booldata && $goods_id) {
                $this->success("添加成功", url("admin/Goods/index"));
            } else {
                $this->success("添加失败", url('admin/Goods/add'));
            }
        }
    }


    /**
     * [商品修改]
     * 陈绪
     */
    public function edit(Request $request, $id)
    {
        $goods = db("goods")->where("id", $id)->select();
        $goods_standard = db("special")->where("goods_id", $id)->select();
        foreach ($goods as $key => $value) {
            $goods[$key]["goods_standard_name"] = explode(",", $value["goods_standard_name"]);
            $goods_standard_value = explode(",", $value["goods_standard_value"]);
            $goods_standard_value = array_chunk($goods_standard_value, 8);
            $goods_delivery = explode(",", $value["goods_delivery"]);
            $goods[$key]["goods_delivery"] = $goods_delivery;
            $goods[$key]["goods_standard_value"] = $goods_standard_value;
            $goods[$key]["goods_images"] = db("goods_images")->where("goods_id", $value["id"])->select();

        }
        $goods_standard_name = array();
        foreach ($goods as $k => $val) {
            foreach ($val["goods_standard_name"] as $k_1 => $v_2) {
                $goods_standard_name[$k_1] = array(
                    "goods_standard_name" => $val["goods_standard_name"][$k_1],
                    "goods_standard_value" => $val["goods_standard_value"][$k_1]
                );
            }
        }

        foreach ($goods_standard as $k => $v) {
            $goods_standard[$k]["title"] = explode(',', $v["name"]);
            $res = explode(',', $v["lv1"]);
        }
        $goods_list = getSelectList("goods_type");
        $goods_brand = getSelectList("brand");
        $year = db("year")->select();
        $car_series = db("car_series")->distinct(true)->field("brand")->select();
        if ($request->isPost()) {
            $car_series = db("car_series")->distinct(true)->field("brand")->select();
            $car_brand = db("car_series")->field("series,brand")->select();
            return ajax_success("获取成功", array("car_series" => $car_series, "car_brand" => $car_brand));
        }

        return view("goods_edit", ["car_series" => $car_series, "year" => $year, "goods_brand" => $goods_brand, "goods_standard_name" => $goods_standard_name, "goods" => $goods, "goods_list" => $goods_list, "goods_brand" => $goods_brand,"goods_standard" => $goods_standard,"res" => $res]);
    }


    /**
     * [图片删除]
     * 陈绪
     */
    public function images(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->param();
            if (!empty($id["id"])) {
                $image = db("goods")->where("id", $id["id"])->field("goods_show_images")->find();
                $bool = db("goods")->where("id", $id["id"])->update(["goods_show_images" => null]);
                if ($bool) {
                    if (!empty($image)) {
                        unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $image['goods_show_images']);
                    }
                    return ajax_success("成功");
                }
            } else {
                $images_id = $request->only(["images_id"])["images_id"];
                $goods_images = db("goods_images")->where("id", $images_id)->field("goods_images")->find();
                $bool = db("goods_images")->where("id", $images_id)->delete();
                if ($bool) {
                    if (!empty($goods_images)) {
                        unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $goods_images['goods_images']);
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
    public function del(Request $request)
    {
        $id = $request->only(["id"])["id"];
        $image_url = db("goods_images")->where("goods_id", $id)->field("goods_images,id")->select();
        $bool = db("goods")->where("id", $id)->delete();
        if ($bool) {
            foreach ($image_url as $value) {
                if ($value['goods_images'] != null) {
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $value['goods_images']);
                }
                $bool_data = db("goods_images")->where("id", $value['id'])->delete();
            }
            if ($bool_data) {
                $this->success("删除成功", url("admin/Goods/index"));
            } else {
                $this->success("删除失败", url('admin/Goods/index'));
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

            if ($goods_data["goods_standard"] == "通用") {
                unset($goods_data["dedicated_vehicle"]);
                unset($goods_data["goods_car_brand"]);
                unset($goods_data["dedicated_property"]);
            }
            if (!empty($goods_data["goods_standard_name"])) {
                $goods_standard_name = implode(",", $goods_data["goods_standard_name"]);
                $goods_standard_value = implode(",", $goods_data["goods_standard_value"]);
                $goods_data["goods_standard_name"] = $goods_standard_name;
                $goods_data["goods_standard_value"] = $goods_standard_value;
            }
            if (!empty($goods_data["goods_delivery"])) {
                $goods_delivery = implode(",", $goods_data["goods_delivery"]);
                $goods_data["goods_delivery"] = $goods_delivery;
            }
            //图片添加
            $show_images = $request->file("goods_show_images");

            if (!empty($show_images)) {
                $show_image = $show_images->move(ROOT_PATH . 'public' . DS . 'uploads');
                $goods_data["goods_show_images"] = str_replace("\\", "/", $show_image->getSaveName());
            }
            $admin_id = Session::get("user_id");
            $admin_phone = db("admin")->where("id", $admin_id)->value("phone");
            $user_id = db("user")->where("phone_num", $admin_phone)->value("id");
            $store_id = db("store")->where("user_id", $user_id)->value("store_id");
            $goods_data["store_id"] = $store_id;
            $bool = db("goods")->where("id", $id)->update($goods_data);
            if ($bool) {
                //取出图片在存到数据库
                $goods_images = [];
                $file = request()->file('goods_images');
                if (!empty($file)) {
                    foreach ($file as $key => $value) {
                        $info = $value->move(ROOT_PATH . 'public' . DS . 'uploads');
                        $goods_url = str_replace("\\", "/", $info->getSaveName());
                        $goods_images[] = ["goods_images" => $goods_url, "goods_id" => $id];
                    }
                    $booldata = model("goods_images")->saveAll($goods_images);
                    if ($booldata) {
                        $this->success("更新成功", url("admin/Goods/index"));
                    } else {
                        $this->success("更新失败", url('admin/Goods/add'));
                    }
                } else {
                    $this->success("更新成功", url("admin/Goods/index"));
                }

            }
        }

    }


    /**
     * [商品状态]
     * 陈绪
     */
    public function status(Request $request)
    {

        if ($request->isPost()) {
            $status = $request->only(["status"])["status"];
            if ($status == 0) {
                $id = $request->only(["id"])["id"];
                $admin_id = Session::get("user_id");
                foreach ($id as $value) {
                    if ($admin_id == 2) {
                        $bool = db("goods")->where("id", $value)->update(["goods_status" => 0]);
                    } else {
                        $bool = db("goods")->where("id", $value)->update(["goods_status" => 0]);
                    }
                }
                if ($bool) {
                    return ajax_success("成功");
                } else {
                    return ajax_error("失败");
                }

            }
            if ($status == 1) {
                $id = $request->only(["id"])["id"];
                $admin_id = Session::get("user_id");
                foreach ($id as $val) {
                    $goods = db("goods")->where("id", $val)->field("putaway_status")->find();
                    if ($admin_id == 2 || $goods["putaway_status"] != null) {
                        $bool = db("goods")->where("id", $val)->update(["goods_status" => 1, "putaway_status" => 1]);
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
    public function batches(Request $request)
    {
        if ($request->isPost()) {
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
    public function pay($id)
    {


        return view("goods_pay");

    }




    /**
     * 商品确认付费
     * 陈绪
     */
    public function affirm()
    {

        return view("affirm_pay");

    }




    /**
     * 商品查看
     * 陈绪
     */
    public function look(Request $request, $id)
    {
        $goods = db("goods")->where("id", $id)->select();
        $goods_standard = db("special")->where("goods_id", $id)->select();
        foreach ($goods as $key => $value) {
            $goods[$key]["goods_standard_name"] = explode(",", $value["goods_standard_name"]);
            $goods_standard_value = explode(",", $value["goods_standard_value"]);
            $goods_standard_value = array_chunk($goods_standard_value, 8);
            $goods_delivery = explode(",", $value["goods_delivery"]);
            $goods[$key]["goods_delivery"] = $goods_delivery;
            $goods[$key]["goods_standard_value"] = $goods_standard_value;
            $goods[$key]["goods_images"] = db("goods_images")->where("goods_id", $value["id"])->select();

        }
        $goods_standard_name = array();
        foreach ($goods as $k => $val) {
            foreach ($val["goods_standard_name"] as $k_1 => $v_2) {
                $goods_standard_name[$k_1] = array(
                    "goods_standard_name" => $val["goods_standard_name"][$k_1],
                    "goods_standard_value" => $val["goods_standard_value"][$k_1]
                );
            }
        }

        foreach ($goods_standard as $k => $v) {
            $goods_standard[$k]["title"] = explode(',', $v["name"]);
            $res = explode(',', $v["lv1"]);
        }
        $goods_list = getSelectList("goods_type");
        $goods_brand = getSelectList("brand");
        $year = db("year")->select();
        $car_series = db("car_series")->distinct(true)->field("brand")->select();
        if ($request->isPost()) {
            $car_series = db("car_series")->distinct(true)->field("brand")->select();
            $car_brand = db("car_series")->field("series,brand")->select(); 
            return ajax_success("获取成功", array("car_series" => $car_series, "car_brand" => $car_brand));
        }

        return view("goods_edit", ["car_series" => $car_series, "year" => $year, "goods_brand" => $goods_brand, "goods_standard_name" => $goods_standard_name, "goods" => $goods, "goods_list" => $goods_list, "goods_brand" => $goods_brand,"goods_standard" => $goods_standard,"res" => $res]);
    }



    /**
     * 通用商品规格名添加
     * 陈绪
     */
    public function name(Request $request)
    {

        if ($request->isPost()) {
            $standard_name = $request->only(["goods_name"])["goods_name"];
            $standard = db("goods_standard_name")->where("standard_name", $standard_name)->select();
            if (empty($standard)) {
                $goods_name_bool = db("goods_standard_name")->insert(["standard_name" => $standard_name]);
                if ($goods_name_bool) {
                    $goods_name = db("goods_standard_name")->order("id desc")->select();
                    return ajax_success("成功", $goods_name);
                } else {
                    return 2;
                }

            } else {
                return ajax_error("已存在");
            }
        }

    }





    /**
     * 通用商品规格名显示
     * 陈绪
     */
    public function standard_name(Request $request)
    {

        if ($request->isPost()) {
            $goods_name = db("goods_standard_name")->order("id desc")->select();
            if ($goods_name) {
                return ajax_success("获取成功", $goods_name);
            } else {
                return ajax_error("失败");
            }

        }

    }




    /**
     * 专用商品属性入库
     * 陈绪
     */
    public function property_name(Request $request)
    {

        if ($request->isPost()) {
            $property_name = $request->only(["property_name"])["property_name"];
            $property = db("goods_property_name")->where("property_name", $property_name)->select();
            if (empty($property)) {
                $bool = db("goods_property_name")->insert(["property_name" => $property_name]);
                if ($bool) {
                    $goods_property_name = db("goods_property_name")->order("id desc")->select();
                    return ajax_success("成功", $goods_property_name);
                } else {
                    return 2;
                }
            } else {
                return ajax_error("已存在");
            }
        }

    }


    /**
     * 专用属性名称删除
     * 陈绪
     * @param Request $request
     */
    public function property_name_del(Request $request)
    {

        if ($request->isPost()) {
            $standard_name = $request->only(["goods_name"])["goods_name"];
            $standard_bool = db("goods_property_name")->where("property_name", $standard_name)->delete();
            if ($standard_bool) {
                return ajax_success("删除成功");
            } else {
                return ajax_error("删除失败");
            }
        }

    }





    /**
     * 专用商品属性显示
     * 陈绪
     */
    public function property_show(Request $request)
    {

        if ($request->isPost()) {
            $property_name = db("goods_property_name")->order("id desc")->select();
            if ($property_name) {
                return ajax_success("获取成功", $property_name);
            } else {
                return ajax_error("失败");
            }

        }

    }




    /**
     * 角色检测
     * 陈绪
     */
    public function role_name(Request $request)
    {

        if ($request->isPost()) {
            $user_id = Session::get("user_id");
            $admin = db("admin")->where("id", $user_id)->select();
            return ajax_success("获取成功", array("admin" => $admin));
        }

    }




    /**
     * 商品提交订单
     * 陈绪
     */
    public function alipay(Request $request)
    {

        if ($request->isPost()) {
            include('../extend/AliPay_demo/f2fpay/model/builder/AlipayTradePrecreateContentBuilder.php');
            include('../extend/AliPay_demo/f2fpay/service/AlipayTradeService.php');
            include("../extend/AliPay_demo/f2fpay/config/config.php");

            if (!empty($_POST['WIDbody']) && trim($_POST['WIDtotal_amount']) != "") {
                // (必填) 商户网站订单系统中唯一订单号，64个字符以内，只能包含字母、数字、下划线，
                // 需保证商户系统端不能重复，建议通过数据库sequence生成，
                $outTradeNo = date("YmdHis") . uniqid();

                // (必填) 订单标题，粗略描述用户的支付目的。如“xxx品牌xxx门店当面付扫码消费”
                $store = $_POST['WIDsubject'];

                // (必填) 订单总金额，单位为元，不能超过1亿元
                // 如果同时传入了【打折金额】,【不可打折金额】,【订单总金额】三者,则必须满足如下条件:【订单总金额】=【打折金额】+【不可打折金额】
                $goods_money = $_POST['WIDtotal_amount'];


                // (可选) 订单不可打折金额，可以配合商家平台配置折扣活动，如果酒水不参与打折，则将对应金额填写至此字段
                // 如果该值未传入,但传入了【订单总金额】,【打折金额】,则该值默认为【订单总金额】-【打折金额】
                $undiscountableAmount = "";

                // 卖家支付宝账号ID，用于支持一个签约账号下支持打款到不同的收款账号，(打款到sellerId对应的支付宝账号)
                // 如果该字段为空，则默认为与支付宝签约的商户的PID，也就是appid对应的PID
                //$sellerId = "";

                // 订单描述，可以对交易或商品进行一个详细地描述，比如填写"购买商品2件共15.00元"
                $goods_id = $_POST['WIDbody'];

                //第三方应用授权令牌,商户授权系统商开发模式下使用
                $appAuthToken = "";//根据真实值填写

                // 创建请求builder，设置请求参数
                $qrPayRequestBuilder = new \AlipayTradePrecreateContentBuilder();
                $qrPayRequestBuilder->setOutTradeNo($outTradeNo);
                $qrPayRequestBuilder->setTotalAmount($goods_money);
                $qrPayRequestBuilder->setSubject($store);
                $qrPayRequestBuilder->setBody($goods_id);
                $qrPayRequestBuilder->setUndiscountableAmount($undiscountableAmount);
                $qrPayRequestBuilder->setAppAuthToken($appAuthToken);


                // 调用qrPay方法获取当面付应答
                $qrPay = new \AlipayTradeService($config);
                $qrPayResult = $qrPay->qrPay($qrPayRequestBuilder);

                //	根据状态值进行业务处理
                switch ($qrPayResult->getTradeStatus()) {
                    case "SUCCESS":
                        $response = $qrPayResult->getResponse();
                        $qrcode = $qrPay->create_erweima($response->qr_code);
                        return ajax_success("获取成功", $qrcode);

                        break;
                    case "FAILED":
                        if (!empty($qrPayResult->getResponse())) {
                            return ajax_success("成功", $qrPayResult->getResponse());
                        }
                        break;
                    case "UNKNOWN":
                        if (!empty($qrPayResult->getResponse())) {
                            return ajax_error("失败", $qrPayResult->getResponse());
                        }
                        break;
                    default:
                        echo "不支持的返回状态，创建订单二维码返回异常!!!";
                        break;
                }
            }
        }

    }



    /**
     * 支付宝回到地址
     * 陈绪
     * @param Request $request
     */
    public function pay_code(Request $request)
    {

        include('../extend/AliPay_demo/f2fpay/model/builder/AlipayTradePrecreateContentBuilder.php');
        include('../extend/AliPay_demo/f2fpay/service/AlipayTradeService.php');
        include("../extend/AliPay_demo/f2fpay/config/config.php");
        $qrPayRequestBuilder = new \AlipayTradePrecreateContentBuilder();
        $qrPay = new \AlipayTradeService($config);
        $qrPayResult = $qrPay->qrPay($qrPayRequestBuilder);

        //	根据状态值进行业务处理
        switch ($qrPayResult->getTradeStatus()) {
            case "SUCCESS":
                $response = $qrPayResult->getResponse();
                $qrcode = $qrPay->create_erweima($response->qr_code);
                return ajax_success("获取成功", $qrcode);

                break;
            case "FAILED":
                if (!empty($qrPayResult->getResponse())) {
                    return ajax_success("成功", $qrPayResult->getResponse());
                }
                break;
            case "UNKNOWN":
                if (!empty($qrPayResult->getResponse())) {
                    return ajax_error("失败", $qrPayResult->getResponse());
                }
                break;
            default:
                echo "不支持的返回状态，创建订单二维码返回异常!!!";
                break;
        }
    }




    /**
     * 专用适用车型编辑显示
     * 陈绪
     */
    public function edit_show(Request $request)
    {

        if ($request->isPost()) {
            $id = $request->only(["id"])["id"];
            $goods = db("goods")->where("id", $id)->field("dedicated_vehicle,goods_car_year,goods_car_displacement,goods_car_series")->select();
            foreach ($goods as $key => $value) {

                $goods[$key]["goods_car_year"] = explode(",", $value["goods_car_year"]);
                $goods[$key]["goods_car_displacement"] = explode(",", $value["goods_car_displacement"]);
                $goods[$key]["goods_car_series"] = explode(",", $value["goods_car_series"]);

            }
            if ($goods) {
                return ajax_success("获取成功", $goods);
            } else {
                return ajax_error("获取失败");
            }
        }

    }





    /**
     * 微信支付
     * 陈绪
     */
    public function WeiAlpay(Request $request)
    {

            //店铺名称，必填
        $store = $_POST['WIDsubject'];
            //付款金额，必填
        $goods_money = $_POST['WIDtotal_amount'];

            //商品描述，可空
        $goods_id = $_POST['WIDbody'];


        header("Content-type: text/html; charset=utf-8");
        ini_set('date.timezone', 'Asia/Shanghai');

        include("../extend/WxpayAPI/lib/WxPay.Api.php");
        include('../extend/WxpayAPI/example/WxPay.NativePay.php');
        include('../extend/WxpayAPI/example/log.php');

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
        $input->SetBody($store);
        /**
         * 设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
         */
        $input->SetAttach($goods_id);
        /**
         * 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
         */
        $input->SetOut_trade_no("g" . $goods_id);
        /**
         * 设置订单总金额，只能为整数，详见支付金额
         * @param string $value
         **/
        $input->SetTotal_fee($goods_money * 100);
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
        $input->SetNotify_url("http://automobile.siring.com.cn/saoma_callback");
        /**
         * 设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
         * @param string $value
         **/
        $input->SetTrade_type("NATIVE");
        /**
         * 设置trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
         * @param string $value
         **/
        $input->SetProduct_id($goods_id);
        /**
         * 生成直接支付url，支付url有效期为2小时,模式二
         * @param UnifiedOrderInput $input
         */

        $result = $notify->GetPayUrl($input);
        $url2 = $result["code_url"];
        return view("WeiAlpay_code", ["goods_id" => $goods_id, "url2" => $url2, "goods_money" => $goods_money, "store" => $store]);

    }



    /**
     * 微信上架状态检测
     * 陈绪
     */
    public function get_weixin_status(Request $request)
    {

        if ($request->isPost()) {
            $id = $request->only(["goods_id"])["goods_id"];
            $goods_id = explode("g", $id);
            foreach ($goods_id as $value) {
                $bool = db("goods")->where("id", $value)->where("putaway_status", 1)->select();
            }
            if ($bool) {
                return ajax_success("成功", $bool);
            } else {
                return ajax_error("失败");
            }

        }

    }



    /**
     * 进行二维码扫码
     * 陈绪
     */
    public function qrcode()
    {

        error_reporting(E_ERROR);
        include('../extend/WxpayAPI/example/phpqrcode/phpqrcode.php');
        $url = $_GET["url2"];
        \QRcode::png($url);

    }



    /*
    支付宝支付
     */
    public function alipay_pay()
    {
        header("Content-type:text/html;charset=utf-8");
        include EXTEND_PATH . "/lib/payment/alipay/alipay.class.php";
        $int_order_id = intval($_GET['order_id']);

        $obj_alipay = new \alipay();

        $arr_data = array(
            "return_url" => trim("http://automobile.siring.com.cn/admin.html"),
            "notify_url" => trim("http://automobile.siring.com.cn/"),
            "service" => "create_direct_pay_by_user",
            "payment_type" => 1, //
            "seller_email" => 'bill.nie@hotmail.com',
            "out_trade_no" => 'mall' . $int_order_id,
            "subject" => "支付表单的名称先不管你支付就知道了",
            "total_fee" => number_format('100.12', 2, '.', ''),
        );

        if (isset($arr_order['paymethod']) && isset($arr_order['defaultbank']) && $arr_order['paymethod'] === "bankPay" && $arr_order['defaultbank'] != "") {

            $arr_data['paymethod'] = "bankPay";
            $arr_data['defaultbank'] = $arr_order['defaultbank'];
        }
        $str_pay_html = $obj_alipay->make_form($arr_data, true);
        return view("alipay", ['str_pay_html' => $str_pay_html]);
    }


    /*
    支付宝回调
     */
    public function shopping_notify()
    {

        include EXTEND_PATH . "/lib/payment/alipay/alipay.class.php";
        $obj_alipay = new \alipay();
        if (!$obj_alipay->verify_notify()) {//验证未通过
            //file_put_contents(S_ROOT.'data/failed_'.time().'.txt',var_export($_POST,true));
            echo "fail";
            exit();
        }

        $str_order_id = strtolower(trim($_POST['out_trade_no']));
        $int_order_id = intval(ltrim($str_order_id, 'mall'));
        $str_total_fee = number_format(trim($_POST['total_fee']), 2, '.', '');

        //记录日志
        $arr_log_data = array(
            'order_id' => $int_order_id,
            'pay_type' => 0,
            'is_update_ok' => 0,
            'return_info' => str_addslashes(var_export($_POST, true)),
            'in_date' => $_SGLOBAL['timestamp'],
        );

        if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
            //file_put_contents(S_ROOT.'data/success_order_id_'.time().'.txt','订单ID:'.$int_order_id);
            //1.根据订单号，获取订单信息
            $obj_order = L::loadClass('order', 'index');
            //$arr_order = $obj_order->get_one_main($int_order_id);
            $arr_bat_pay = $obj_order->get_one_bat_pay($int_order_id);
            if ($arr_bat_pay['coupons_id']) {
                $int_main_total_fee = ($arr_bat_pay['pay_price']);
            } else {
                $int_main_total_fee = ($arr_bat_pay['price']);
            }
            $str_main_total_fee = number_format($int_main_total_fee, 2, '.', '');
            if ($str_total_fee != $str_main_total_fee) {
                //file_put_contents(S_ROOT.'data/price_error'.time().'.txt',$str_total_fee.','.$int_main_total_fee);
                echo "success";  ////反馈给支付宝,请不要修改或删除
            } else {
                //第三方交易信息
                $arr_third_pay_data = array(
                    'third_id' => str_addslashes($_POST['trade_no']),
                );
                $arr_res = $obj_order->do_pay_success($int_order_id, $arr_third_pay_data['third_id']);
                //$bool_update = $obj_order->update_main(array('order_id'=>$int_order_id),array('status'=>LEM_order::ORDER_PAY,'third_id'=>$arr_third_pay_data['third_id'],'pay_date'=>$_SGLOBAL['timestamp']));
                //file_put_contents(S_ROOT.'data/update_bool'.time().'.txt',$bool_update);
                if ($arr_res['status'] == 200) {
                    $arr_log_data['is_update_ok'] = 1; //更新成功
                    echo "success";  ////反馈给支付宝,请不要修改或删除
                } else {
                    echo 'fail';
                }
            }
            //include template('template/mall/cart/pay_success');
        } else {
            echo "fail"; //反馈给支付宝,请不要修改或删除
        }
        $obj_alipay->log($arr_log_data);   //记录日志
    }



    /**
     * [汽车商品列表规格图片删除]
     * 郭杨
     */
    public function photos(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->only(["id"])["id"];
            if (!empty($id)) {
                $photo = db("special")->where("id", $id)->update(["images" => null]);
            }
            if ($photo) {
                return ajax_success('更新成功!');
            } else {
                return ajax_error('更新失败');
            }
        }

    }


    /**
     * [汽车商品列表规格值修改]
     * 郭杨
     */
    public function value(Request $request)
    {
        if ($request->isPost()) {

            $id = $request->only(["id"])["id"];
            $value = $request->only(["value"])["value"];
            $key = $request->only(["key"])["key"];
            $valuet = db("special")->where("id", $id)->update([$key => $value]);

            if (!empty($valuet)) {
                return ajax_success('更新成功!');
            } else {
                return ajax_error('更新失败');
            }
        }

    }


    /**
     * [汽车商品列表规格开关]
     * 郭杨
     */
    public function switches(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->only(["id"])["id"];
            $status = $request->only(["status"])["status"];

            if (!empty($id)) {
                $ture = db("special")->where("id", $id)->update(["status" => $status]);
            }
            if ($ture) {
                return ajax_success('更新成功!');
            } else {
                return ajax_error('更新失败');
            }
        }

    }


    /**
     * [汽车商品列表规格图片添加]
     * 郭杨
     */
    public function addphoto(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->only(["id"])["id"];
            $imag = $request->file("file") -> move(ROOT_PATH . 'public' . DS . 'uploads');
            $images = str_replace("\\", "/", $imag->getSaveName());

            if(!empty($id)){
                $bool = db("special")->where("id", $id)->update(["images" => $images]);
            }
             if ($bool) {
                 return ajax_success('添加图片成功!');
             } else {
                 return ajax_error('添加图片失败');
             }
        }

    }


}