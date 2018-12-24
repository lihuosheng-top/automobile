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
            $img = $request->file("filesArr");
            foreach ($img as $k=>$v){
                $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                $evaluation_images[] = str_replace("\\","/",$info->getSaveName());
            }
            if(!empty($img)){
                return ajax_success("有数据",$evaluation_images);
            }else{
                return ajax_success("没有有数据",$evaluation_images);
            }

//            $user_id = Session::get("user");//用户id
//            $order_id =$request->only("order_id")["order_id"];//订单排序号（数组）
////            dump($order_id);
//            $is_on_time =$request->only("is_on_time")["is_on_time"];//是否准时（是否准时，1为准时,-1为不准时）
//            $service_attitude_stars =$request->only("service_attitude_stars")["service_attitude_stars"];//服务态度的星星（1为1颗星，...5为5颗星）
//            $logistics_stars =$request->only("logistics_stars")["logistics_stars"];//物流服务的星星（1为1颗星，...5为5颗星）
//            $user_info =Db::name("user")->field("user_phone_num,user_name")->where("id",$user_id)->find();
//            $create_time =time();//创建时间
//            foreach ($order_id  as $k=>$v){
//                Db::name("order_parts")->field("parts_goods_name,goods_id,order_information_number")->where("id",$v)->find();
//            }
//            $data =[
//                "evaluate_content"=>1
//
//            ];
//            Db::startTrans();
//            try{
//                $a=Db::name("user")->where("id",$user_id)->find();
//                if(!$a){
//                    throw  new \Exception("没有查询成功");
//                }
//                Db::commit();
//            }catch(\Exception $e){
//            Db::rollback();
//            dump($e->getMessage());
//            }

        }
    }



}