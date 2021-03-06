<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/23
 * Time: 18:13
 */
namespace app\index\controller;
use think\Controller;
use think\Paginator;
use think\queue\job\Redis;
use think\Request;
use think\Session;
use think\Db;

class LoveCar extends Controller{

    /**
     * 我的爱车
     * 陈绪
     */
    public function love_car(Request $request){

        if($request->isPost()) {
            $brand = db("car_series")->distinct(true)->field("brand")->select();
            $series = db("car_series")->select();
            $car_images = db("car_images")->select();
            foreach ($brand as $key => $value) {
                foreach ($car_images as $val) {
                    if ($value["brand"] == $val["brand"]) {
                        $brand[$key]["images"] = $val["brand_images"];
                    }
                }
            }
            if ($brand) {
                return ajax_success("获取成功", array("brand" => $brand, "series" => $series));
            } else {
                return ajax_error("获取失败");
            }

        }

    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:我的爱车（品牌名）
     **************************************
     * @param Request $request
     */
    public function love_car_ios(Request $request){
        if($request->isPost()) {
            $brand = db("car_series")->distinct(true)->field("brand")->select();
            $car_images = db("car_images")->select();
            foreach ($brand as $key => $value) {
                foreach ($car_images as $val) {
                    if ($value["brand"] == $val["brand"]) {
                        $brand[$key]["images"] = $val["brand_images"];
                    }
                }
            }
            if ($brand) {
                return ajax_success("获取成功", array("brand" => $brand));
            } else {
                return ajax_error("获取失败");
            }

        }

    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:我的爱车（车系列）
     **************************************
     * @param Request $request
     */
    public function  love_car_series(Request $request){
        if($request->isPost()) {
            $brand =$request->only(["brand"])["brand"];
            $brand = db("car_series")->where("brand",$brand)->distinct(true)->field("series")->select();
            if ($brand) {
                return ajax_success("获取成功", array("brand" => $brand));
            } else {
                return ajax_error("获取失败");
            }

        }
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:我的爱车（车排量）
     **************************************
     * @param Request $request
     */
    public function  love_car_displacement(Request $request){
        if($request->isPost()) {
            $series =$request->only(["series"])["series"];
            $brand = db("car_series")->where("series",$series)->distinct(true)->field("displacement")->select();
            if ($brand) {
                return ajax_success("获取成功", array("brand" => $brand));
            } else {
                return ajax_error("获取失败");
            }
        }
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:我的爱车（车年产）
     **************************************
     * @param Request $request
     */
    public function  love_car_year(Request $request){
        if($request->isPost()) {
            $displacement =$request->only(["displacement"])["displacement"];
            $brand = db("car_series")->where("displacement",$displacement)->distinct(true)->field("year")->select();
            if ($brand) {
                return ajax_success("获取成功", array("brand" => $brand));
            } else {
                return ajax_error("获取失败");
            }

        }
    }



    /**
     **************李火生*******************
     * @param Request $request
     * Notes:安卓对接所有爱车列表信息返回
     **************************************
     * @param Request $request
     */
    public function love_car_info(Request $request){
        if($request->isPost()) {
            $id =$request->only(["id"])["id"];
            $series = db("car_series")->where("id",$id)->select();
            if ($series) {
                return ajax_success("获取成功", array("series" => $series));
            } else {
                return ajax_error("获取失败");
            }

        }

    }

    /**
     * 我的爱车入库
     * 陈绪
     */
    public function love_save(Request $request){
        $user_id = Session::get("user");
        if(!empty($user_id)){
            if($request->isPost()){
                $love = $request->param();
                $bool_one = db("user_car")->where("user_id",$user_id)->count();
                if($bool_one>1){
                    return ajax_error("爱车最多只能两辆");
                }else{
                    $bools = db("user_car")->where("user_id",$user_id)->where("status",1)->find();
                    if(!empty($bools)){
                        $love["user_id"] = $user_id;
                        $love["status"] = 0;
                    }else{
                        $love["user_id"] = $user_id;
                        $love["status"] = 1;
                    }
                }
                $bool = db("user_car")->insertGetId($love);
                if($bool){
                    Session::set("user_car_id",$bool);
                    if($love["status"] ==1){
                        Db::name('user_car')
                            ->where("user_id",$user_id)
                            ->where("id","NEQ",$bool)
                            ->update(['status'=>0]);
                 }
                    return ajax_success("添加成功");
                }else{
                    return ajax_error("添加失败");
                }
            }
        }else{
            exit(json_encode(array("status"=>2,"info"=>"请登录"),JSON_UNESCAPED_UNICODE));
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:跳到编辑页面
     **************************************
     */
    public function love_car_go(Request $request){
        if($request->isPost()){
            $id =$request->only(["id"])["id"];
            Session::set("user_car_id",$id);
            if($id>0){
                return ajax_success("可以跳",$id);
            }else{
                return ajax_error("id不正确");
            }
        }
    }


    /**
     * 我的爱车列表
     * 陈绪
     */
    public function love_list(Request $request)
    {
        if($request->isPost()){
            $user_id = Session::get("user");
            $love = db("user_car")->where("user_id",$user_id)->select();
            if(!empty($love)){
                foreach ($love as $key=>$value){
                    $love[$key]["images"] = db("car_images")
                        ->where("brand",$value["brand"])
                        ->find();
                }
                return ajax_success("获取成功",$love);
            }else{
                return ajax_error("暂无爱车信息");
            }

        }
        return view("love_list");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:我的爱车编辑
     **************************************
     * @param Request $request
     * @return \think\response\View|void
     */
    public function love_edit(Request $request)
    {
        if($request->isPost()){
            $user_id = Session::get("user");
            $user_car_id = Session::get("user_car_id");
            $bool = db("user_car")->where("id",$user_car_id)->where("user_id",$user_id)->find();
            if(!empty($bool)){
                $bool["images"] = db("car_images")
                    ->where("brand",$bool["brand"])
                    ->value("brand_images");
                $bool["user_car"] =db("user_car_message")->where("user_car_id",$user_car_id)->find();
                Session::set("user_car_id",null);
                return ajax_success("获取信息成功",$bool);
            }else{
                return ajax_error("没有数据");
            }
        }
        return view("love_edit");
    }



    /**
     * 我的爱车状态修改
     * 陈绪
     */
    public function love_status(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");
            $love = db("user_car")->where("user_id",$user_id)->where("status",1)->field("status")->find();
            if(!empty($love)){
                db("user_car")->where("user_id",$user_id)->where("status",1)->update(["status"=>0]);
            }
            $love_id = $request->only(["id"])["id"];
            $status = $request->only(["status"])["status"];
            $bool = db("user_car")->where("id",$love_id)->update(["status"=>$status]);
            if($bool){
                return ajax_success("设置成功");
            }else{
                return ajax_error("设置失败");
            }
        }
    }





    /**
     * 我的爱车列表删除
     * 陈绪
     */
    public function love_del(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");
            $id = $request->only(["id"])["id"];
            $status =db("user_car")->where("id",$id)->value("status");
            if($status ==1){
                $bool = db("user_car")->where("id",$id)->delete();
                if($bool){
                    //把剩下的那一个爱车变为默认
                    db("user_car")->where("id","NEQ",$id)->where("user_id",$user_id)->update(["status"=>1]);
                    return ajax_success("删除成功");
                }else{
                    return ajax_error("删除失败");
                }
            }else{
                $bool = db("user_car")->where("id",$id)->delete();
                if($bool){
                    return ajax_success("删除成功");
                }else{
                    return ajax_error("删除失败");
                }
            }
        }

    }



    /**
     * 车辆信息添加,修改
     * 陈绪
     */
    public function love_list_save(Request $request){

        if($request->isPost()){
            $user_car_id = $request->only(["id"])["id"];
            $user_car_message = db("user_car_message")->where("user_car_id",$user_car_id)->select();
            if(!empty($user_car_message)){
                $user_car_message_data = $request->param();
                $bool = db("user_car_message")->where("user_car_id",$user_car_id)->update($user_car_message_data);
                if($bool){
                    return ajax_success("更新成功");
                }else{
                    return ajax_error("更新失败");
                }
            }else{
                $user_id =Session::get("user");
                $user_car_data = $request->param();
                $user_car_data["user_car_id"] = $user_car_id;
                $bool = db("user_car_message")->insert($user_car_data);
                if($bool){
                    //判断是否完善资料
                    $data =Db::name("user_car")
                        ->where('user_id',$user_id)
                        ->select();
                    foreach ($data as $k=>$v){
                        $car_is_full_messige =Db::name("user_car_message")
                            ->where("user_car_id",$v["id"])
                            ->value("plate_number");
                        if(!empty($car_is_full_messige)){
                            $car_is_full_messiges =$car_is_full_messige;
                        }
                    }
                    //判断是否完善爱车资料
                    if(!empty($car_is_full_messiges)){
                        $is_perfect = Db::name("user_is_perfect")
                            ->where("user_id",$user_id)
                            ->find();
                        if(empty($is_perfect)){
                            //完善进行积分奖励
                            $send_integral =Db::name("recommend_integral")
                                ->where("id",1)
                                ->value("datum_integral");
                             $old_integral_wallet = Db::name("user")
                                ->where("id",$user_id)
                                ->value("user_integral_wallet");
                            //积分添加
                            $add_res = Db::name("user")
                                ->where("id",$user_id)
                                ->update(["user_integral_wallet"=>$old_integral_wallet+$send_integral]);
                            if($add_res){
//                                //余额添加成功(做积分消费记录)
//                                //插入积分记录
                                $integral_data =[
                                    "user_id"=>$user_id,
                                    "integral_operation"=>$send_integral,//获得积分
                                    "integral_balance"=>$send_integral+$old_integral_wallet,//积分余额
                                    "integral_type"=>1, //积分类型（1获得，-1消费）
                                    "operation_time"=>date("Y-m-d H:i:s"), //操作时间
                                    "integral_remarks"=>"完善资料送".$send_integral."积分",
                                ];
                                Db::name("integral")->insert($integral_data);
                                Db::name("user_is_perfect")->insert(["user_id"=>$user_id]);//记录起来
                            }
                        }
                        }
                    return ajax_success("添加成功");
                    } else{
                    return ajax_error("添加失败");
                }
            }
        }

    }


    /**
     * 车辆详细信息显示
     * 陈绪
     * @param Request $request
     */
    public function love_list_edit(Request $request){
        if($request->isPost()){
            $id = $request->only(["id"])["id"];
            $user_car_message = db("user_car_message")->where("user_car_id",$id)->select();
            $user_car = db("user_car")->where("id",$id)->field("brand")->find();
            foreach ($user_car_message as $key=>$value){
                $user_car_message[$key]["images"] = db("car_images")->where("brand",$user_car["brand"])->value("brand_images");
            };
            if($user_car_message){
                return ajax_success("获取成功",$user_car_message);
            }else{
                return ajax_error("获取失败");
            }
        }
    }





}