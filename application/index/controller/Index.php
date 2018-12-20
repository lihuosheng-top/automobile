<?php
namespace app\index\controller;

use think\Controller;
use think\Paginator;
use think\Request;
use think\Session;

use think\Db;
class Index extends Controller
{


    /**
     * 首页
     * 陈绪
     */
    public function index(Request $request)
    {
        if($request->isPost()) {
            $user_id = Session::get("user");
            if (!empty($user_id)) {
                $user_car = db("user_car")->where("user_id", $user_id)->where("status", 1)->select();
                if ($user_car) {
                    return ajax_success("获取成功", $user_car);
                } else {
                    return ajax_error("获取失败");
                }

            } else {
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
        }

        return view("index");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:首页目的不让直接进入前台页面
     **************************************
     * @return \think\response\View
     */
    public function home()
    {
        return view("home");
    }


    /**
     * 微信回调
     * 陈绪
     */
    public function saoma_callback(Request $request)
    {
        //扫码支付，接收微信请求;
        if($request->isPost()){
            $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
            $xml_data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            $val = json_decode(json_encode($xml_data), true);
            if($val["result_code"] == "SUCCESS" ){
                $goods = strstr($val["out_trade_no"],"g");
                $goods_id = substr($goods,1);
                $ids = explode("g",$goods_id);
                foreach ($ids as $value){
                    $bool = db("goods")->where("id",$value)->update(["putaway_status"=>1,"goods_status"=>1]);
                }

                if($bool){
                    return ajax_success("成功",$bool);
                }else{
                    return ajax_error("失败");
                }

            }

        }
    }




    /**
     * 热门配件店铺
     * 陈绪
     */
    public function shop(Request $request){

        if($request->isPost()){
            $where ="`is_hot_store` = '1' and `store_is_button` = '1' and `del_status` = '1' and `operation_status` = '1' ";
            $shop = db("store")->where($where)->select();
            $shop_data  = [];
            foreach ($shop as $key=>$value){
                $shop_data[$key]["shop_address"] = $value["store_city_address"].",".$value["store_street_address"];
                $shop_data[$key]["shop_images"] = $value["verifying_physical_storefront_one"];
                $shop_data[$key]["id"] = $value["store_id"];
                $shop_data[$key]["shop_time"] = $value["store_do_bussiness_time"];
                $shop_data[$key]["shop_name"] = $value["store_name"];
            }
            return ajax_success("获取成功",$shop_data);
        }

    }




    /**
     * 热门配件店铺中的商品
     * 陈绪
     */
    public function shop_goods(Request $request){

        if($request->isPost()) {
            $shop_id = $request->only(["id"])["id"];
            $goods = db("goods")->where("store_id", $shop_id)->where("goods_status", 1)->select();
            $serve_goods = db("serve_goods")->where("store_id", $shop_id)->where("status", 1)->select();
            $store = db("store")->where("store_id",$shop_id)->select();
            $service_setting = db("service_setting")->select();
            $serve_data = [];
            foreach ($service_setting as $key => $value) {
                foreach ($serve_goods as $val) {
                    if ($value["service_setting_id"] == $val["service_setting_id"]) {
                        $serve_data[$key]["serve_name"] = $value["service_setting_name"];
                        $serve_data[$key]["serve_goods"] = db("serve_goods")->where("service_setting_id", $val["service_setting_id"])->where("store_id", $shop_id)->select();
                    }
                }
            }
            if ($goods) {
                return ajax_success("获取成功", array("store"=>$store,"goods" => $goods, "serve_data" => $serve_data));
            } else {
                return ajax_error("获取失败");
            }
        }

    }




    /**
     * 热门服务店铺
     * 陈绪
     */
    public function serve_shop(Request $request){

        if($request->isPost()){

        }

    }

}
