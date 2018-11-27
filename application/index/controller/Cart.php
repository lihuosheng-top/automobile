<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Db;

class Cart extends Controller
{


    /**
     * 购物车
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function Cart_index()
    {
        return view("cart_index");
    }




    /**
     * 获取商品id 存入购物车
     * 陈绪
     */
    public function ajax_id(Request $request){
        if ($request->isPost()){
            $data = Session::get("member");
            $user_id =Db::name('user')->field('id')->where('phone_num',$data['phone_num'])->find();

            unset($data);
            if(empty($user_id['id'])){
                return ajax_error("请登录");
            }
            if(!empty($user_id['id'])) {
                //存入购物车
                $goods_id = $request->only(['id'])['id'];
                $goods_id = intval($goods_id);
                $goods = db("goods")->where("id",$goods_id)->find();
                $shopping = db("shopping")->where("user_id",$user_id['id'])->where("goods_id", $goods_id)->select();
                foreach ($shopping as $key=>$value) {
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

}
