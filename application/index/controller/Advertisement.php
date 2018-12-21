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

class  Advertisement extends  Controller{

    /**
     * [汽车广告显示]
     * 郭杨
     */
    public function advertisement_index(Request $request)
    {
        if ($request->isGet()){
            //$area = $request->only("area")["area"];
            $area = "广东省,深圳市,福田区";
            $area_data = Db::name("platform")->where('area',$area)->where("status", 1)->select();
            $data = Db::name("position") -> where("pid",0) ->field("name")->select();
             
 
            $res = [];
            $rest = array();

            dump($area_data);

            foreach($area_data as $key => $value)
            {
    
                $rest[$value['location']][$key] = $value; 
                                
            }

            halt($rest);
            foreach($data as $k => $v){
                $res[$v['name']] = $v;
                
            }
        
             dump($res);



        
           // halt($res);
            if (!empty($area_data)) {
                return ajax_success('传输成功', $area_data);
            } else {
                return ajax_error("数据为空");

            }


        }

    }


}