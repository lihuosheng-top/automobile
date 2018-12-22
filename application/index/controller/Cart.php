<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Db;

class Cart extends Controller
{


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:购物车页面跟数据
     **************************************
     * @param Request $request
     * @return \think\response\View|void
     */
    public function cart_index(Request $request)
    {
        if($request->isPost()){
            $user_id = Session::get("user");
            if(empty($user_id)){
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
            $shopping_data = db("shopping")->where("user_id",$user_id)->select();
            if(!empty($shopping_data)){
                exit(json_encode(array("status" => 1, "info" => "购物车数据返回成功","data"=>$shopping_data)));
            }else{
                exit(json_encode(array("status" => 0, "info" => "购物车未添加商品")));
            }
        }
        return view("cart_index");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:获取商品id 存入购物车
     **************************************
     * @param Request $request
     */
    public function get_goods_id_to_cart(Request $request){
        if ($request->isPost()){
            $user_id = Session::get("user");
            if(empty($user_id)){
                return ajax_error("请登录",["status"=>0]);
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
                //存入购物车
                $goods_id = $request->only(['goods_id'])['goods_id'];//商品id
                $store_id = $request->only(['store_id'])['store_id'];//店铺id
                $goods_unit = $request->only(['goods_unit'])['goods_unit'];//商品数量
                $goods_standard_id = $request->only(['goods_standard_id'])['goods_standard_id'];//商品通用专用规格id
                $goods_id = intval($goods_id);
                $goods = db("goods")->where("id",$goods_id)->find();

                $store_name =Db::name("store")->field("store_name")->where("store_id",$store_id)->find();
                $shopping_data = db("shopping")
                    ->where("user_id",$user_id)
                    ->where("goods_id", $goods_id)
                    ->where("store_id",$store_id)
                    ->select();
                foreach ($shopping_data as $key=>$value) {
                    if (in_array($goods_id,$value)) {
                        $goods_end_money =Db::name("special")
                            ->field("id",$goods_standard_id)
                            ->where("goods_id",$goods_id)
                            ->find();
                        $money = array($value['money'], $goods_end_money["price"]);
                        $shopping[$key]['money'] = array_sum($money);
                        $shopping[$key]['goods_unit'] = $value['goods_unit'] + 1;
                        unset($shopping[$key]['id']);
                        $bool = db("shopping")->where("goods_id", $goods_id)->where("user_id",$user_id)->update($shopping[0]);
                        return ajax_success("成功", $bool);
                    }
                }
            
                $data['goods_name'] = $goods['goods_name'];
                $data['goods_images'] = $goods['goods_show_images'];
                $goods_end_money =Db::name("special")
                    ->field("id",$goods_standard_id)
                    ->where("goods_id",$goods_id)
                    ->find();
                $data['money'] =  $goods_end_money["price"];
                $data['goods_unit'] = 1;
                $data['user_id'] = $user_id;
                $data['goods_id'] = $goods['id'];
                $data['store_id'] = $goods['store_id'];
                $data['store_name'] = $store_name["store_name"];
                $bool = db("shopping")->insert($data);
                 exit(json_encode(array("status" => 1, "info" => "加入购物车成功" ,"data"=>$bool)));
            }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:购物车存储到shopping_shop表（ajax）
     **************************************
     * @param Request $request
     */
    public function place_an_order_by_cart(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");//用户id
            if(empty($user_id)){
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
            if(!empty($user_id)){
                $id = $request->only(['id'])['id'];//shopping表的主键id
                $goods_unit = $request->only(['goods_unit'])['goods_unit'];//商品数量
                $money = $request->only(['money'])['money'];//商品的总价
                foreach ($id as $key => $val) {
                    db("shopping")->where("id", $val)->update(['goods_unit' => $goods_unit[$key]]);
                }
                //存储到购物车订单表中
                $data['money'] = $money;
                $data['shopping_id'] = implode(",", $id);
                $data['user_id'] = $user_id;
                db("shopping_shop")->insert($data);
                $shopping_id['id'] = db("shopping_shop")->getLastInsID();
                Session("shopping", $shopping_id);
                return ajax_success("获取成功", $shopping_id);
            }
        }
    }


}
