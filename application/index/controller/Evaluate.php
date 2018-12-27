<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/13 0013
 * Time: 10:01
 */
namespace  app\index\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Db;

class Evaluate extends  Controller{
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:评价页面
     **************************************
     */
    public function evaluate_index(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");
            $store_id = Session::get("store_id"); //店铺id
            $parts_order_number = Session::get("parts_order_number"); //订单编号
            $condition = "`user_id` = " . $user_id . " and `store_id` = " . $store_id . " and `parts_order_number` = " . $parts_order_number;
            $data = Db::name("order_parts")
                ->where($condition)
                ->select();
            if(!empty($data)){
                Session::set("parts_order_number",null);
                Session::set("store_id",null); //店铺id
                return ajax_success("对应的订单信息返回成功",$data);
            }else{
                return ajax_error("没有对应的订单信息",["status"=>0]);
            }
        }
        return view("evaluate_index");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单评价信息的添加
     **************************************
     */
    public function evaluate_parts_add(Request $request){
        if($request->isPost()){
            $order_id =$request->only("orderId")["orderId"];//订单排序号（数组）
            foreach ($order_id as $k=>$v){
                $filesArr[$k] = "filesArr".$v;
            }

            foreach ($filesArr as $ks=>$vs){
                $img = $request->file("$v");
                dump($img);
                if(!empty($img)){
                    $images =[];
                    foreach ($img as $k=>$v) {
                        $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                        $images[] = str_replace("\\", "/", $info->getSaveName());
                    }
                    dump($images);
                }
            }
            exit;
            /*if(!empty($img)){
                return ajax_success("有数据",$images);
            }else{
                return ajax_success("没有有数据",$images);
            }*/
            $user_id = Session::get("user");//用户id
            $evaluate_content =$request->only("evaluateContent")["evaluateContent"];//评价内容（数组）
            $is_on_time =$request->only("isOnTime")["isOnTime"];//是否准时（是否准时，1为准时,-1为不准时）
            $logistics_stars =$request->only("starArr")["starArr"];//所有的星星（1为1颗星，...5为5颗星）
            $start_length =count($logistics_stars);
            $user_info =Db::name("user")->field("phone_num,user_name,id")->where("id",$user_id)->find();
            $create_time =time();//创建时间
            foreach ($order_id  as $k=>$v){
                //所有的订单信息
              $order_information =  Db::name("order_parts")
                    ->field("parts_goods_name,goods_id,parts_order_number,store_id")
                    ->where("id",$v)
                    ->find();
              $data =[
                  "evaluate_content"=>$evaluate_content[$k], //评价的内容
                  "goods_id" =>$order_information["goods_id"],
                  "store_id" =>$order_information["store_id"],
                  "goods_name"=>$order_information["parts_goods_name"],
                  "user_phone_num"=>$user_info["phone_num"],
                  "user_id"=>$user_info["id"],
                  "status"=>0,
                  "order_information_number"=>$order_information["parts_order_number"],
                  "order_id"=>$v,
                  "create_time"=>$create_time,
                  "user_name"=> $user_info["user_name"],
                  "evaluate_stars"=>$logistics_stars[$k], //商品描述星星（1为1颗星，...5为5颗星）
                  "service_attitude_stars"=>$logistics_stars[$start_length-2],  //服务态度的星星（1为1颗星，...5为5颗星） //服务星星(先计算长度-2)
                  "logistics_stars"=>$logistics_stars[$start_length-1], //物流服务的星星（1为1颗星，...5为5颗星） //物流星星(先计算长度-1)
                  "is_repay"=>0,
                  "is_on_time"=>$is_on_time,

              ];
              $bool =Db::name("order_parts_evaluate")->insertGetId($data);
            }
            if($bool){
                return ajax_success("评价成功",$bool);
            }else{
                return ajax_error("评价失败",["status"=>0]);
            }
        }
    }



}