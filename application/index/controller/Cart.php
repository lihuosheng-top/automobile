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
    public function get_goods_id_save(Request $request){
        if ($request->isPost()){
            $user_id = Session::get("user");
            if(empty($user_id)){
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
                //存入购物车
                $goods_id = $request->only(['goods_id'])['goods_id'];//商品id
                $store_id = $request->only(['store_id'])['store_id'];//店铺id
                $goods_id = intval($goods_id);
                $goods = db("goods")->where("id",$goods_id)->find();
                $shopping_data = db("shopping")
                    ->where("user_id",$user_id)
                    ->where("goods_id", $goods_id)
                    ->where("store_id",$store_id)
                    ->select();
                foreach ($shopping_data as $key=>$value) {
                    if (in_array($goods_id,$value)) {
                        $money = array($value['money'], $goods['goods_bottom_money']);
                        $shopping[$key]['money'] = array_sum($money);
                        $shopping[$key]['goods_unit'] = $value['goods_unit'] + 1;
                        unset($shopping[$key]['id']);
                        $bool = db("shopping")->where("goods_id", $goods_id)->where("user_id",$user_id['id'])->update($shopping[0]);
                        return ajax_success("成功", $bool);
                    }
                }
                $data['goods_name'] = $goods['goods_name'];
                $data['goods_images'] = $goods['goods_show_images'];
                $data['money'] = $goods['goods_bottom_money'];
                $data['goods_unit'] = 1;
                $data['user_id'] = $user_id['id'];
                $data['goods_id'] = $goods['id'];
                $bool = db("shopping")->insert($data);
                return ajax_success("获取成功", $bool);
            }
    }

}
