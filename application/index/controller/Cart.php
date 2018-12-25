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
                    foreach ($shopping_data as $key=>$val){
                        $store_all_id[] =$val["store_id"];
                    }
                $da_store_id = array_unique($store_all_id); //去重之后的商户
                //购物车信息
                foreach ($da_store_id as $keys=>$vals){
//                    $da_store_ids[] =$vals; //去重之后的id数组
                    $shopping_datas[] =Db::table('tb_shopping')
                        ->field("tb_shopping.*,tb_special.name special_name,tb_special.goods_adjusted_price goods_prices")
                        ->join("tb_special","tb_shopping.goods_standard_id=tb_special.id",'left')
                        ->where('store_id', $vals)
                        ->where('user_id',  $user_id)
                        ->select();
                }
                //店铺id和店铺名称
                if(!empty($shopping_datas)){
                    foreach ($shopping_datas as $k=>$v){
                        $shopping_informations[$k]["info"] =$v;
                            foreach ($v as  $ks=>$vs){
                                $store_ids[$k] =$vs["store_id"];
                                $store_names[$k] =$vs["store_name"];
                            }
                    }
                }
                if(!empty($store_ids) && (!empty($store_names))){
                    //店铺id
                    foreach ($store_ids as $i => $j) {
                        if(!empty($j)){
                            $shopping_informations[$i]["store_id"] =$j;
                        }
                    }
                    //店铺名称
                    foreach ($store_names as $i => $j) {
                        if(!empty($j)){
                            $shopping_informations[$i]["store_name"] =$j;
                        }
                    }
                }
                if(!empty($shopping_informations)){
                    exit(json_encode(array("status" => 1, "info" => "购物车数据返回成功","data"=>$shopping_informations)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "购物车未添加商品")));
                }

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
                $goods_delivery = $request->only(['goods_delivery'])['goods_delivery'];//配送与安装服务
                $goods_id = intval($goods_id);
                $goods = db("goods")->where("id",$goods_id)->find();
                $store_name =Db::name("store")->field("store_name")->where("store_id",$store_id)->find();

                $shopping_data = db("shopping")
                    ->where("user_id",$user_id)
                    ->where("goods_id", $goods_id)
                    ->where("store_id",$store_id)
                    ->select();

                foreach ($shopping_data as $key=>$value) {
                    if (in_array($goods_standard_id,$value) && in_array($goods_delivery,$value)) {
//                        $goods_end_money =Db::name("special")
//                            ->field("price")
//                            ->where("id",$goods_standard_id)
//                            ->where("goods_id",$goods_id)
//                            ->find();
//                        dump($value);
//                        dump($goods_end_money);
//                        $money = array($value['money'],$goods_end_money["price"]);
//                        $shopping[$key]['money'] = array_sum($money);
                        $shopping_num = $value['goods_unit'] + $goods_unit;
                        $shopping_id =$value["id"];
                    }
                }
                if(!empty($shopping_num)){
                    $bool = Db::name("shopping")
                        ->where("id",$shopping_id)
                        ->where("goods_id", $goods_id)
                        ->where("user_id",$user_id)
                        ->where("goods_standard_id",$goods_standard_id)
                        ->update(["goods_unit"=>$shopping_num]);
                    if($bool){
                        return ajax_success("成功", $bool);
                    }else{
                        return ajax_error("失败",["status"=>0]);
                    }
                }
                $data['goods_name'] = $goods['goods_name'];
                $goods_end_money =Db::name("special")
                    ->field("price,name,goods_adjusted_price,images")
                    ->where("id",$goods_standard_id)
                    ->where("goods_id",$goods_id)
                    ->find();
                $data['money'] =  $goods_end_money["goods_adjusted_price"];
                $data['goods_images'] =$goods_end_money['images'];//商品图片
                $data['goods_unit'] = $goods_unit;
                $data['user_id'] = $user_id;
                $data['goods_id'] = $goods['id'];
                $data['store_id'] = $goods['store_id'];
                $data['store_name'] = $store_name["store_name"];
                $data['goods_standard_id'] =$goods_standard_id;
                $data["special_name"] =$goods_end_money["name"];
                $data['goods_delivery'] =$goods_delivery;
                $bool = db("shopping")->insert($data);
                 exit(json_encode(array("status" => 1, "info" => "加入购物车成功" ,"data"=>$bool)));
            }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:购物车添加商品数量
     **************************************
     * @param Request $request
     */
    public function cart_information_add(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");
            if(empty($user_id)){
                return ajax_error("请登录",["status"=>0]);
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
            $goods_unit = $request->only(['goods_unit'])['goods_unit'];//商品数量
            $shopping_id = $request->only(['shopping_id'])['shopping_id'];//shopping表中的id
            if(!empty($goods_unit)){
                $shopping_data = Db::name("shopping")
                    ->where("id",$shopping_id)
                    ->where("user_id",$user_id)
                    ->find();
                $goods_units =$goods_unit+$shopping_data["goods_unit"];
                $bool = Db::name("shopping")
                    ->where("id",$shopping_id)
                    ->where("user_id",$user_id)
                    ->update(["goods_unit"=>$goods_units]);
                if($bool){
                    exit(json_encode(array("status" => 1, "info" => "添加成功","data"=>$bool)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "添加失败","data"=>["status"=>0])));
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:购物车减少商品数量
     **************************************
     * @param Request $request
     */
    public function cart_information_del(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");
            if(empty($user_id)){
                return ajax_error("请登录",["status"=>0]);
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
            $goods_unit = $request->only(['goods_unit'])['goods_unit'];//商品数量
            $shopping_id = $request->only(['shopping_id'])['shopping_id'];//shopping表中的id
            if(!empty($goods_unit)){
                $shopping_data = Db::name("shopping")
                    ->where("id",$shopping_id)
                    ->where("user_id",$user_id)
                    ->find();
                $goods_units =$shopping_data["goods_unit"]-$goods_unit;
                $bool = Db::name("shopping")
                    ->where("id",$shopping_id)
                    ->where("user_id",$user_id)
                    ->update(["goods_unit"=>$goods_units]);
                if($bool){
                    exit(json_encode(array("status" => 1, "info" => "删除成功","data"=>$bool)));
                }else{
                    exit(json_encode(array("status" => 0, "info" => "删除失败","data"=>["status"=>0])));
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:购物车删除
     **************************************
     * @param Request $request
     */
    public function  carts_del(Request $request){
        if($request->isPost()){
            $id =$request->only("id")["id"];
            if(is_array($id)){
                $where ='id in('.implode(',',$id).')';
            }else{
                $where ='id='.$id;
            }
            $list =  Db::name('shopping')->where($where)->delete();
            if($list!==false)
            {
                exit(json_encode(array("status" => 1, "info" => "成功删除","data"=>$list)));
            }else{
                exit(json_encode(array("status" => 0, "info" => "删除失败","data"=>["status"=>0])));

            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:保存购物车到确认订单页面返回
     **************************************
     * @param Request $request
     */
    public function save_shopping_id(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");//用户id
            if(empty($user_id)){
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
            $id = $request->only(['id'])['id'];//shopping表的主键id
            $total_price = $request->only(['money'])['money'];//总价
            Session::set("shopping_ids",$id);
            Session::set("total_price",$total_price);
            Session::set('part_goods_info',null); //清空立即购买过来的数据
            exit(json_encode(array("status" => 1, "info" => "保存id成功")));
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:多店铺取消付款
     **************************************
     * @param Request $request
     */
    public function cart_store_more_cancel(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");
            $store_id =$request->only("store_id")["store_id"];//店铺id（数组）
            $parts_order_number =$request->only("parts_order_number")["parts_order_number"];//配件商订单编号
            $time=date("Y-m-d",time());
            $v=explode('-',$time);
            $time_second=date("H:i:s",time());
            $vs=explode(':',$time_second);
            $mun =count($store_id);//长度
            if($mun>1){
                foreach ($store_id as $key=>$val){
                    $parts_order_number_end =$v[0].$v[1].$v[2].$vs[0].$vs[1].$vs[2].rand(10,99).$val.$user_id; //订单编号
                    $number_order = $parts_order_number_end;
                    $bool = Db::name('order_parts')
                        ->where('store_id', $val)
                        ->where('user_id', $user_id)
                        ->where('parts_order_number',$parts_order_number)
                        ->update(["parts_order_number"=>$number_order]);
                }
                if($bool){
                    return ajax_success("成功",["status"=>1]);
                }else{
                    return ajax_error("失败",["status"=>0]);
                }
            }else if($mun==1){
                $parts_order_number_end =$v[0].$v[1].$v[2].$vs[0].$vs[1].$vs[2].rand(1000,9999).$user_id; //订单编号
                $bool= Db::name('order_parts')
                    ->where('store_id', $store_id[0])
                    ->where('user_id', $user_id)
                    ->where('parts_order_number',$parts_order_number)
                    ->update(["parts_order_number"=>$parts_order_number_end]);
                if($bool){
                    return ajax_success("成功",["status"=>1]);
                }else{
                    return ajax_error("失败",["status"=>0]);
                }
            }


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
