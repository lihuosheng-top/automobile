<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/12 0012
 * Time: 14:46
 */
namespace app\index\controller;


use think\Controller;
use think\Request;
use think\Session;
use think\Db;

class  SellMy extends Controller{

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家我的页面
     **************************************
     */
    public function sell_my_index(Request $request){
        if($request->isPost()){
            //店铺的id
            $role_name_store_id = Session::get("role_name_store_id");
            if(empty($role_name_store_id)){
                exit(json_encode(array("status" => 2, "info" => "请重新登录","data"=>["status"=>0])));
            }
            //店铺信息
            $store_info = Db::name("store")
                ->field("store_logo_images,store_id,store_name,user_id")
                ->where("store_id",$role_name_store_id)
                ->find();
            if(empty($store_info)){
                exit(json_encode(array("status" => 2, "info" => "请重新登录","data"=>["status"=>0])));
            }
            //今月账单

            //上月账单

        }
        return view("sell_my_index");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家服务订单
     **************************************
     */
    public function sell_service_order(){
        return view("sell_service_order");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家商品订单
     **************************************
     */
    public function sell_parts_order(){
        return view("sell_parts_order");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家服务订单详情
     **************************************
     */
    public function sell_service_order_detail(){
        return view("sell_service_order_detail");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家商品订单详情
     **************************************
     */
    public function sell_parts_order_detail(){
        return view("sell_parts_order_detail");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家服务记录
     **************************************
     * @return \think\response\View
     */
    public function sell_service_record(){
        return view("sell_service_record");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家商品记录
     **************************************
     * @return \think\response\View
     */
    public function sell_parts_record(){
        return view("sell_parts_record");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家账单
     **************************************
     * @return \think\response\View
     */
    public function sell_order_bill(){
        return view("sell_order_bill");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家钱包
     **************************************
     * @return \think\response\View
     */
    public function sell_wallet(){
        return view("sell_wallet");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:卖家提现申请
     **************************************
     * @return \think\response\View
     */
    public function sell_application(){
        return view("sell_application");
    }






}