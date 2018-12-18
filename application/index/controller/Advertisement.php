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
        if ($request->isPost()){

            $tea = Db::name("goods_type")->field('name,icon_image,color,id')->where('pid', 0)->where("status", 1)->select();
            foreach($tea as $key => $value){
                $res = db("goods_type")->where("pid",$value['id'])->field("name,id")->find();
                $tea[$key]["tid"] = $res["id"];
                $tea[$key]["activity_name"] = $res["name"];
               
            }
       }

    }


}