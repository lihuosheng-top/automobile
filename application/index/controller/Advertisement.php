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
            $area = Session::get("area");    
            $area_data = Db::name("platform")->where('area',$area)->where("status", 1)->select();
            $data = Db::name("position") -> where("pid",0) ->field("name,id")->select();           
              
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
            $area_data = Db::name("platform")->where('area',$area)->where("status", 1)->select();
            $data = Db::name("position") -> where("pid",0) ->field("name,id")->select();           
              
            foreach($area_data as $key => $value)
            {

            if( $value['pid'] == 21) //配件商城
            {
                $machine[] = $value;          
            }   
            }
       
            $reste["machine"] = $machine; 
            
            if ( (!empty($reste)) && (!empty($area)) ) {
                return ajax_success('传输成功', $reste);
            } else {
                return ajax_error("数据为空");

            }

        }

    }


}