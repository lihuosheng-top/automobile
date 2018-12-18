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
            $area = $request->only(['area'])['area'];
           // $resdata = Db::name("goods_type")->field('name,icon_image,color,id')->where('pid', $id)->where("status", 1)->select();
            
            if (!empty($area)) {
                return ajax_success('传输成功', $area);
            } else {
                return ajax_error("数据为空");

            }


        }

    }


}