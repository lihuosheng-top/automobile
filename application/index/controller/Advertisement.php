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

class  Advertisement extends  Controller{

    /**
     * [汽车广告显示]
     * 郭杨
     */
    public function advertisement_index(Request $request)
    {
        if ($request->isPost())
        {
            $area = $request->only(["area"])["area"];
            Session::set("area",$area);    
            $area_data = Db::name("platform")->where('area',$area)->where("status", 1)->order("start_time desc")->select();
            $adress_data = Db::name("platform")->where('department',"platform_business")->where("status", 1)->order("start_time desc")->select();              
            foreach($area_data as $key => $value)
            {

            if( $value['pid'] == 18) //首页轮播
            { 
                $home[] = $value;          
            }
            if( $value['pid'] == 19) //首页固定
            {
                $fixed[] = $value;         
            }
            if( $value['pid'] == 20) //热门推荐
            {
                $hot[] = $value;          
            }
            if( $value['pid'] == 21) //配件商城
            {
                $machine[] = $value;          
            }   
            }
      
            $home_length = count($home);
            $fixed_length = count($fixed);
            $hot_length = count($hot);
            $machine_length = count($machine); 
            
            foreach($adress_data as $k => $v)
            {

            if( $v['pid'] == 18) //首页轮播
            { 
                $homes[] = $v;          
            }
            if( $v['pid'] == 19) //首页固定
            {
                $fixeds[] = $v;         
            }
            if( $v['pid'] == 20) //热门推荐
            {
                $hots[] = $v;          
            }
            if( $v['pid'] == 21) //配件商城
            {
                $machines[] = $v;          
            }   
            }
      

            foreach($homes as $a => $b)
            {
                $home[ $home_length + $a ] = $b;
            }
            foreach($fixeds as $c => $d)
            {
                $fixed[ $fixed_length + $c ] = $d;
            }
            foreach($hots as $f => $g)
            {
                $hot[ $hot_length + $f ] = $g;
            }
            foreach($machines as $h => $i)
            {
                $machine[ $machine_length + $h ] = $i;
            }


            $reste['home'] = $home;
            $reste["fixed"] = $fixed;
            $reste["hot"] = $hot;
            $reste["machine"] = $machine;

            if ( (!empty($reste)) && (!empty($area)) ) {
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
        if ($request->isPost())
        {
            $area = Session::get("area");
            $area_data = Db::name("platform")->where('area',$area)->where("status", 1)->order("start_time desc")->select();
            $adress_data = Db::name("platform")->where('department',"platform_business")->where("status", 1)->order("start_time desc")->select();
                       
            foreach($area_data as $key => $value)
            {

            if( $value['pid'] == 21) //配件商城
            {
                $machine[] = $value;          
            }
            if( $value['pid'] == 27) //配件商城优惠推荐
            {
                $discount[] = $value;          
            }   
            }
       
            $machine_length = count($machine); 
            $discount_length = count($discount); 
            
            foreach($adress_data as $k => $v)
            {

            if( $v['pid'] == 21) //平台配件商城
            {
                $machines[] = $v;          
            }
            if( $v['pid'] == 27) //平台配件商城优惠推荐
            {
                $discounts[] = $v;          
            }   
            }
      

            foreach($machines as $h => $i)
            {
                $machine[ $machine_length + $h ] = $i;
            }
            foreach($discounts as $b => $n)
            {
                $discount[ $discount_length + $b ] = $n;
            }
            $reste["machine"] = $machine; 
            $reste["discounts"] = $discount; 
 
            if ( (!empty($reste)) && (!empty($area)) ) {
                return ajax_success('传输成功', $reste);
            } else {
                return ajax_error("数据为空");

            }

        }

    }


}