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

        if($request->isPost()) {
            include('../extend/AliPay_demo/f2fpay/model/builder/AlipayTradePrecreateContentBuilder.php');
            include('../extend/AliPay_demo/f2fpay/service/AlipayTradeService.php');
            include("../extend/AliPay_demo/f2fpay/config/config.php");

            if (!empty($_POST['WIDbody']) && trim($_POST['WIDtotal_amount']) != "") {
                // (必填) 商户网站订单系统中唯一订单号，64个字符以内，只能包含字母、数字、下划线，
                // 需保证商户系统端不能重复，建议通过数据库sequence生成，
                $outTradeNo = date("YmdHis").uniqid();

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
                $response = $qrPayResult->getResponse();
                $qrcode = $qrPay->create_erweima($response->qr_code);
                return ajax_success("获取成功", $qrcode);
                //	根据状态值进行业务处理
                switch ($qrPayResult->getTradeStatus()) {
                    case "SUCCESS":



                        break;
                    case "FAILED":
                        if (!empty($qrPayResult->getResponse())) {
                            return ajax_success("成功",$qrPayResult->getResponse());
                        }
                        break;
                    case "UNKNOWN":
                        if (!empty($qrPayResult->getResponse())) {
                            return ajax_error("失败",$qrPayResult->getResponse());
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
    public function pay_code(Request $request){

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
                    return ajax_success("成功",$qrPayResult->getResponse());
                }
                break;
            case "UNKNOWN":
                if (!empty($qrPayResult->getResponse())) {
                    return ajax_error("失败",$qrPayResult->getResponse());
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

        if($request->isPost()) {

            //店铺名称，必填
            $store = $_POST['WIDsubject'];
            //付款金额，必填
            $goods_money = $_POST['WIDtotal_amount'] * 100;

            //商品描述，可空
            $goods_id = $_POST['WIDbody'];


            header("Content-type: text/html; charset=utf-8");
            ini_set('date.timezone', 'Asia/Shanghai');

            include("../extend/WxpayAPI/lib/WxPay.Api.php");
            include("../extend/WxpayAPI/example/WxPay.NativePay.php");
            include("../extend/WxpayAPI/example/log.php");

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
            $input->SetAttach("ceshidingdan1");
            /**
             * 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
             */
            $input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
            /**
             * 设置订单总金额，只能为整数，详见支付金额
             * @param string $value
             **/
            $input->SetTotal_fee($goods_money);
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
            $input->SetNotify_url("http://automobile.siring.com.cn/admin/goods_qrcode");
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

            return ajax_success("获取成功", urlencode($url2));
            //return view("WeiAlpay_code",["url2"=>urlencode(url2)]);
        }
        return view("WeiAlpay_code");


    }



    /**
     * 进行二维码扫码
     * 陈绪
     */
    public function qrcode(){

        error_reporting(E_ERROR);
        include ('../extend/WxpayAPI/example/phpqrcode/phpqrcode.php');
        $url = urldecode($_GET["url2"]);
        $errorCorrectionLevel = 'H';
        $matrixPointSize = 10;
        \QRcode::png($url,false,$errorCorrectionLevel, $matrixPointSize,3);

    }



    /**
     * 微信回调
     * 陈绪
     */
    public function wx_notify(Request $request){
        if($request->isPost()) {
            ini_set('date.timezone', 'Asia/Shanghai');

            error_reporting(E_ERROR);

            include("../extend/WxpayAPI/example/notify.php");

            $notify = new \PayNotifyCallBack();

            $notify->Handle(false);

            $is_success = $notify->IsSuccess();

            $bdata = $is_success['data'];               //获取微信回调数据
            return ajax_success("成功", $bdata);

            if ($is_success['code'] == 1) {

                echo 1;
                exit();

                //验证成功，获取数据

                $total_fee = $bdata['total_fee'] / 100;     //支付金额

                $trade_no = $bdata['transaction_id'];     //微信订单号

                $out_trade_no = $bdata['out_trade_no'];           //系统订单号

                $openid = $bdata['openid'];           //用户在商户appid下的唯一标识


                // 其他coding ……

            }
        }

    }



}