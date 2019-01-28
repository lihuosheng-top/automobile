<?php
/**
 * Created by PhpStorm.
 * User: GY
 * Date: 2018/12/18 
 * Time: 11:20
 */
namespace app\index\controller;
use think\console\Input;
use think\Controller;
use think\Db;
use think\Request;
use think\Image;
use think\Session;
class Advertisement extends Controller
{
    /**
     * [汽车广告显示]
     * 郭杨
     */
    public function advertisement_index(Request $request)
    {
        if ($request->isPost()) {
            $area = $request->only(["area"])["area"];
            Session::set("area", $area);
            $t= date('Y-m-d H:i:s');
            $time  = strtotime($t);
            $end_time =  "end_time < {$time}";
            $status = Db::name("platform")->where($end_time)->update(["status"=>3]);
            $area = Db::name("platform")->where('area', $area)->where("status", 1)->order("start_time desc")->select();
            $adress = Db::name("platform")->where('department', "platform_business")->where("status", 1)->order("start_time desc")->select();
            $area_data = rest($area);
            $adress_data = rest($adress);

            
            //平台
            foreach ($adress_data as $k => $v) {
                if ($v['pid'] == 18) //首页轮播
                {
                    if (isset($v)) {
                        $homes[] = $v;
                        $home_length = count($homes);
                    }
                }
                if ($v['pid'] == 19) //首页固定
                {
                    if (isset($v)) {
                        $fixeds[] = $v;
                        $fixed_length = count($fixeds);
                    }
                }
                if ($v['pid'] == 20) //热门推荐
                {
                    if (isset($v)) {
                        $hots[] = $v;
                        $hot_length = count($hots);
                    }
                }
                if ($v['pid'] == 21) //配件商城
                {
                    if (isset($v)) {
                        $machines[] = $v;
                        $machine_length = count($machines);
                    }
                }
            }
            //配件商and服务商
            foreach ($area_data as $k => $v) {
                if ($v['pid'] == 18) //首页轮播
                {
                    $home[] = $v;
                }
                if ($v['pid'] == 19) //首页固定
                {
                    $fixed[] = $v;
                }
                if ($v['pid'] == 20) //热门推荐
                {
                    $hot[] = $v;
                }
                if ($v['pid'] == 21) //配件商城
                {
                    $machine[] = $v;
                }
            }
            //首页轮播
            if ((!isset($homes)) && (!isset($home))) {
                $reste["home"] = null;
            }
            if ((!isset($homes)) && (isset($home))) {
                $reste["home"] = $home;
            }
            if ((isset($homes)) && (!isset($home))) {
                $reste["home"] = $homes;
            }
            if ((isset($homes)) && (isset($home))) {
                foreach ($home as $a => $b) {
                    $homes[$home_length + $a] = $b;
                    $reste["home"] = $homes;
                }
            }
            if(count($reste["home"])>5){
                $reste["home"] = array_slice($reste["home"],0,5);
                }

            //首页固定
            if ((!isset($fixeds)) && (!isset($fixed))) {
                $reste["fixed"] = null;
            }
            if ((!isset($fixeds)) && (isset($fixed))) {
                $reste["fixed"] = $fixed;
            }
            if ((isset($fixeds)) && (!isset($fixed))) {
                $reste["fixed"] = $fixeds;
            }
            if ((isset($fixeds)) && (isset($fixed))) {
                foreach ($fixed as $c => $d) {
                    $fixeds[$fixed_length + $c] = $d;
                    $reste["fixed"] = $fixeds;
                }
            }
            if(count($reste["fixed"])>1){
            $reste["fixed"] = array_slice($reste["fixed"],0,1);
            }
            //热门推荐
            if ((!isset($hots)) && (!isset($hot))) {
                $reste["hot"] = null;
            }
            if ((!isset($hots)) && (isset($hot))) {
                $reste["hot"] = $hot;
            }
            if ((isset($hots)) && (!isset($hot))) {
                $reste["hot"] = $hots;
            }
            if ((isset($hots)) && (isset($hot))) {
                foreach ($hot as $e => $f) {
                    $hots[$hot_length + $e] = $f;
                    $reste["hot"] = $hots;
                }
            }
            //配件商城
            if ((!isset($machines)) && (!isset($machine))) {
                $reste["machine"] = null;
            }
            if ((!isset($machines)) && (isset($machine))) {
                $reste["machine"] = $machine;
            }
            if ((isset($machines)) && (!isset($machine))) {
                $reste["machine"] = $machines;
            }
            if ((isset($machines)) && (isset($machine))) {
                foreach ($machine as $h => $i) {
                    $machines[$machine_length + $h] = $i;
                    $reste["machine"] = $machines;
                }
            }

            if(count($reste["machine"])>3){
                $reste["machine"] = array_slice($reste["machine"],0,3);
                }
           
          $test = $reste["hot"];
          if(!empty($test)){
           foreach($test as $m => $p)
           {
            if($test[$m]["postid"]==5){
                $hot_ones[] = $p;
                if(count($hot_ones) > 0){
                $hot_one = array_slice($hot_ones,0,1);
                }
            }
            if($test[$m]["postid"]==6){
                $hot_twos[] = $p;
            }
            if($test[$m]["postid"]==7){
                $hot_threes[] = $p;
            }
            if($test[$m]["postid"]==8){
                $hot_fours[] = $p;
            }
            if($test[$m]["postid"]==9){
                $hot_fives[] = $p;

            }
            if($test[$m]["postid"]==10){
                $hot_sixs[] = $p;
            }
               
           }
        }   
            if(isset($hot_one)){
            $reste["hot_one"] = $hot_one;
            }else{
                $reste["hot_one"] = NULL;
            }
            if(isset($hot_two)){
                $reste["hot_two"] = $hot_two;
                }else{
                    $reste["hot_two"] = NULL;
                }
                if(isset($hot_three)){
                    $reste["hot_three"] = $hot_three;
                    }else{
                        $reste["hot_three"] = NULL;
                    }
                    if(isset($hot_four)){
                        $reste["hot_four"] = $hot_four;
                        }else{
                            $reste["hot_four"] = NULL;
                        }
                        if(isset($hot_five)){
                            $reste["hot_five"] = $hot_five;
                            }else{
                                $reste["hot_five"] = NULL;
                            }
                            if(isset($hot_six)){
                                $reste["hot_six"] = $hot_six;
                                }else{
                                    $reste["hot_six"] = NULL;
                                }                               
                                     
            if ((!empty($reste)) && (!empty($area))) {
                return ajax_success('获取成功', $reste);
            } 
            if ((!empty($reste)) && (empty($area))) {
                return ajax_success('获取位置失败', $reste);
            }else {
                return ajax_error("暂无广告显示");
            }
        }
    }
    /**
     * [汽车广告配件商城显示]
     * 郭杨
     */
    public function machine_index(Request $request)
    {
        if ($request->isPost()) {
            $area = Session::get("area");
            $area_data = Db::name("platform")->where('area', $area)->where("status", 1)->order("start_time desc")->select();
            $adress_data = Db::name("platform")->where('department', "platform_business")->where("status", 1)->order("start_time desc")->select();
            //平台
            foreach ($adress_data as $k => $v) {
                if ($v['pid'] == 21) //配件商城
                {
                    if (isset($v)) {
                        $machines[] = $v;
                        $machine_length = count($machines);
                    }
                }
                if ($v['pid'] == 27) //配件商城优惠推荐
                {
                    if (isset($v)) {
                        $discounts[] = $v;
                        $discount_length = count($discounts);
                    }
                }
            }
            
            //配件商and服务商
            foreach ($area_data as $k => $v) {
                if ($v['pid'] == 21) //配件商城
                {
                    $machine[] = $v;
                }
                if ($v['pid'] == 27) //配件商城优惠推荐
                {
                    $discount[] = $v;
                }
            }
            //配件商城
            if ((!isset($machines)) && (!isset($machine))) {
                $reste["machine"] = null;
            }
            if ((!isset($machines)) && (isset($machine))) {
                $reste["machine"] = $machine;
            }
            if ((isset($machines)) && (!isset($machine))) {
                $reste["machine"] = $machines;
            }
            if ((isset($machines)) && (isset($machine))) {
                foreach ($machine as $h => $i) {
                    $machines[$machine_length + $h] = $i;
                    $reste["machine"] = $machines;
                }
            }

            if(count($reste["machine"])>3){
                $reste["machine"] = array_slice($reste["machine"],0,3);
                }

            //配件商城优惠推荐
            if ((!isset($discounts)) && (!isset($discount))) {
                $reste["discount"] = null;
            }
            if ((!isset($discounts)) && (isset($discount))) {
                $reste["discount"] = $discount;
            }
            if ((isset($discounts)) && (!isset($discount))) {
                $reste["discount"] = $discounts;
            }
            if ((isset($discounts)) && (isset($discount))) {
                foreach ($discount as $M => $W) {
                    $discounts[$discount_length + $M] = $W;
                    $reste["discount"] = $discounts;
                }
            }

            if(count($reste["discount"])>3){
                $reste["discount"] = array_slice($reste["discount"],0,3);
                }
                
                if ((!empty($reste)) && (!empty($area))) {
                    return ajax_success('获取成功', $reste);
                } 
                if ((!empty($reste)) && (empty($area))) {
                    return ajax_success('获取位置失败', $reste);
                }else {
                    return ajax_error("暂无广告显示");
                }
            }
        }


    /**
     * [汽车广告热门店铺显示]
     * 郭杨
     */
    public function hot_index()
    {
        $hot_data = Db::name("platform")->where("status", 1)->where("pid",20)->order("start_time desc")->select();

        if(!empty($hot_data)){
            return ajax_success("获取广告成功",$hot_data);
        } else {
            return ajax_error("暂无广告显示");
        }
              
    }


    public function returnSquarePoint()
    {

        $myLat = 22.541355 ;//接收到的当前位置的纬度
        $myLng = 114.047953;//接收到的当前位置的经度
        
        $range = 180 / pi() * 5 / 6372.797;     //里面的 1 就代表搜索 1km 之内，单位km
        $lngR = $range / cos($myLat * pi() / 180);
        $maxLat = $myLat + $range;//最大纬度
        $minLat = $myLat - $range;//最小纬度
        $maxLng = $myLng + $lngR;//最大经度
        $minLng = $myLng - $lngR;//最小经度
        $sql = "SELECT * FROM `表名` WHERE LastGpsWei<>0 AND latitude>$right_bottom_lat AND latitude<$left_top_lat AND longitude>$left_top_lng AND longitude<$right_bottom_lng";
        halt($maxLat);
    }
    





}