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
            $parts_status =Session::get("parts_status"); //订单状态
            $condition = "`user_id` = " . $user_id . " and `store_id` = " . $store_id . " and `parts_order_number` = " . $parts_order_number. " and `status` = " . $parts_status;
            $data = Db::name("order_parts")
                ->where($condition)
                ->select();
            if(!empty($data)){
                Session::set("parts_order_number",null);
                Session::set("store_id",null); //店铺id
                Session::set("parts_status",null); //店铺id
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
             $str=str_replace('filesArr','',$vs);
                $img = $request->file("$vs");
                if(!empty($img)){
                    foreach ($img as $k=>$v) {
                        $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                        $images["$str"][] = str_replace("\\", "/", $info->getSaveName());
                    }
                }
            }
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
              if(!empty( $bool)){
                  Db::name("order_parts")
                      ->where("id",$v)
                      ->update(["status"=>8]);
                  if(!empty($images)){
                      foreach ($images as $ks=>$vs){
                          if( $v == intval($ks)){
                              foreach ($vs as $j=>$i){
                                  //插入评价图片数据库
                                  $insert_data =[
                                      "images"=>$i,
                                      "evaluate_order_id"=>$bool,
                                  ];
                                  Db::name("order_parts_evaluate_images")->insert($insert_data);
                              }
                          }
                      }
                  }
              }
            }
            if($bool){
                //进行消费积分奖励
                $order_id_one =$order_id[0];
                $order_real_pays =Db::name("order_parts")->where("id",$order_id_one)->value("order_real_pay");//消费的钱数
                $recommend_integral =Db::name("recommend_integral")->where("id",1)->find();
                if($order_real_pays >= $recommend_integral["coin"]) {
                    //判断消费是否满足送积分条件
                    $old_integral_wallet = Db::name("user")
                        ->where("id", $user_id)
                        ->value("user_integral_wallet");
                    //推荐人的积分添加
                    $new_integral =$old_integral_wallet + $recommend_integral["consume_integral"];
                    $add_res = Db::name("user")
                        ->where("id", $user_id)
                        ->update(["user_integral_wallet" =>$new_integral ]);
                    if ($add_res) {
                        //余额添加成功(做积分消费记录)
                        //插入积分记录
                        $integral_data = [
                            "user_id" => $user_id,
                            "integral_operation" => $recommend_integral["consume_integral"],//获得积分
                            "integral_balance" => $recommend_integral["consume_integral"] + $old_integral_wallet,//积分余额
                            "integral_type" => 1, //积分类型（1获得，-1消费）
                            "operation_time" => date("Y-m-d H:i:s"), //操作时间
                            "integral_remarks" => "消费满".$recommend_integral["coin"]."送" . $recommend_integral["consume_integral"] . "积分",
                        ];
                        Db::name("integral")->insert($integral_data);
                    }
                }
                return ajax_success("评价成功",$bool);
            }else{
                return ajax_error("评价失败",["status"=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商评价点赞
     **************************************
     */
    public function evaluate_parts_praise(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");
            if(empty($user_id)){
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
            $evaluate_id =$request->only("evaluate_id")["evaluate_id"];
            $is_praise =Db::name("order_parts_praise")
                ->where("parts_evaluate_id",$evaluate_id)
                ->where("user_id",$user_id)
                ->find();
            if(!empty($is_praise)){
                exit(json_encode(array("status" => 0, "info" => "同意用户只能点赞一次")));
            }
            $data =[
                "parts_evaluate_id"=>$evaluate_id,
                "create_time"=>time(),
                "user_id"=>$user_id
            ];
            $res =Db::name("order_parts_praise")->insert($data);
            if($res){
                exit(json_encode(array("status" => 1, "info" => "点赞成功")));
            }
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商订单（评价页面）
     **************************************
     */
    public function service_evaluate_index(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");
            $service_order_number = Session::get("service_order_number"); //订单编号
            $condition = "`user_id` = " . $user_id . " and `service_order_number` = " . $service_order_number;
            $data = Db::name("order_service")
                ->where($condition)
                ->select();
            if(!empty($data)){
                Session::set("service_order_number",null);
                return ajax_success("对应的订单信息返回成功",$data);
            }else{
                return ajax_error("没有对应的订单信息",["status"=>0]);
            }
        }
        return view("service_evaluate_index");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商订单评价添加
     **************************************
     * @param Request $request
     */
    public function evaluate_service_add(Request $request){
        if($request->isPost()){
            $order_id =$request->only("orderId")["orderId"];//订单排序号（数组）
            foreach ($order_id as $k=>$v){
                $filesArr[$k] = "filesArr".$v;
            }
            foreach ($filesArr as $ks=>$vs){
                $str=str_replace('filesArr','',$vs);
                $img = $request->file("$vs");
                if(!empty($img)){
                    foreach ($img as $k=>$v) {
                        $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                        $images["$str"][] = str_replace("\\", "/", $info->getSaveName());
                    }
                }
            }
            $user_id = Session::get("user");//用户id
            $evaluate_content =$request->only("evaluateContent")["evaluateContent"];//评价内容（数组）
            $logistics_stars =$request->only("starArr")["starArr"];//所有的星星（1为1颗星，...5为5颗星）
            $start_length =count($logistics_stars);
            $user_info =Db::name("user")->field("phone_num,user_name,id")->where("id",$user_id)->find();
            $create_time =time();//创建时间
            foreach ($order_id  as $k=>$v){
                //所有的订单信息
                $order_information =  Db::name("order_service")
                    ->field("service_goods_name,service_goods_id,service_order_number,store_id,service_setting_id")
                    ->where("id",$v)
                    ->find();
                $data =[
                    "setting_id"=>$order_information["service_setting_id"],//
                    "evaluate_content"=>$evaluate_content[$k], //评价的内容
                    "goods_id" =>$order_information["service_goods_id"],
                    "store_id" =>$order_information["store_id"],
                    "goods_name"=>$order_information["service_goods_name"],
                    "user_phone_num"=>$user_info["phone_num"],
                    "user_id"=>$user_info["id"],
                    "status"=>0,
                    "order_information_number"=>$order_information["service_order_number"],
                    "order_id"=>$v,
                    "create_time"=>$create_time,
                    "user_name"=> $user_info["user_name"],
                    "evaluate_stars"=>$logistics_stars[$k], //商品描述星星（1为1颗星，...5为5颗星）
                    "service_attitude_stars"=>$logistics_stars[$start_length-2],  //服务态度的星星（1为1颗星，...5为5颗星） //服务星星(先计算长度-2)
                    "logistics_stars"=>$logistics_stars[$start_length-1], //物流服务的星星（1为1颗星，...5为5颗星） //物流星星(先计算长度-1)
                    "is_repay"=>0,
                ];
                $bool =Db::name("order_service_evaluate")->insertGetId($data);
                if(!empty($bool)){
                    Db::name("order_service")
                        ->where("id",$v)
                        ->update(["status"=>6]);
                    if(!empty($images)){
                        foreach ($images as $ks=>$vs){
                            if( $v == intval($ks)){
                                foreach ($vs as $j=>$i){
                                    //插入评价图片数据库
                                    $insert_data =[
                                        "images"=>$i,
                                        "evaluate_order_id"=>$bool,
                                    ];
                                    Db::name("order_service_evaluate_images")->insert($insert_data);
                                }
                            }
                        }
                    }
                    $order_id_one =$order_id[0];
                    $order_real_pays =Db::name("order_service")->where("id",$order_id_one)->value("service_real_pay");//消费的钱数
                    $recommend_integral =Db::name("recommend_integral")->where("id",1)->find();
                    if($order_real_pays >= $recommend_integral["coin"]) {
                        //判断消费是否满足送积分条件
                        $old_integral_wallet = Db::name("user")
                            ->where("id", $user_id)
                            ->value("user_integral_wallet");
                        //推荐人的积分添加
                        $add_res = Db::name("user")
                            ->where("id", $user_id)
                            ->update(["user_integral_wallet" => $old_integral_wallet + $recommend_integral["consume_integral"]]);
                        if ($add_res) {
                            //余额添加成功(做积分消费记录)
                            //插入积分记录
                            $integral_data = [
                                "user_id" => $user_id,
                                "integral_operation" => $recommend_integral["consume_integral"],//获得积分
                                "integral_balance" => $recommend_integral["consume_integral"] + $old_integral_wallet,//积分余额
                                "integral_type" => 1, //积分类型（1获得，-1消费）
                                "operation_time" => date("Y-m-d H:i:s"), //操作时间
                                "integral_remarks" => "消费满".$recommend_integral["coin"]."送" . $recommend_integral["consume_integral"] . "积分",
                            ];
                            Db::name("integral")->insert($integral_data);
                        }
                    }
                    return ajax_success("评价成功",$bool);
                    exit();
                }else{
                    return ajax_error("评价失败",["status"=>0]);
                    exit();
                }
            }

        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:服务商评价点赞
     **************************************
     */
    public function evaluate_service_praise(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");
            if(empty($user_id)){
                exit(json_encode(array("status" => 2, "info" => "请登录")));
            }
            $evaluate_id =$request->only("evaluate_id")["evaluate_id"];
            $is_praise =Db::name("order_service_praise")
                ->where("service_evaluate_id",$evaluate_id)
                ->where("user_id",$user_id)
                ->find();
            if(!empty($is_praise)){
                exit(json_encode(array("status" => 0, "info" => "同意用户只能点赞一次")));
            }
            $data =[
                "service_evaluate_id"=>$evaluate_id,
                "create_time"=>time(),
                "user_id"=>$user_id
            ];
            $res =Db::name("order_service_praise")->insert($data);
            if($res){
                exit(json_encode(array("status" => 1, "info" => "点赞成功")));
            }
        }
    }


}