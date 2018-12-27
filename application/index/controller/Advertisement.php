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
            $area_data = Db::name("platform")->where('area', $area)->where("status", 1)->order("start_time desc")->select();
            $adress_data = Db::name("platform")->where('department', "platform_business")->where("status", 1)->order("start_time desc")->select();


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

            if ((!empty($reste)) && (!empty($area))) {
                return ajax_success('传输成功', $reste);
            } else {
                return ajax_error("数据为空");

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
 
            if ((!empty($reste)) && (!empty($area))) {
                return ajax_success('传输成功', $reste);
            } else {
                return ajax_error("数据为空");

            }

        }

    }

}