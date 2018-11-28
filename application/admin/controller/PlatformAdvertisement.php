<?php
/**
 * Created by PhpStorm.
 * User: GY
 * Date: 2018/10/26
 * Time: 19:17
 */
namespace  app\admin\controller;

use think\console\Input;
use think\Controller;
use think\Db;
use think\Request;
use think\Image;

class  PlatformAdvertisement extends  Controller{

    /**
     * [汽车平台广告显示]
     * 郭杨
     */
    public function platform_business_index(){
        $platform = db("platform")->paginate(2);
        //halt($platform);
        return view('platform_business_index',['platform'=>$platform]);
    }



    public function platform_business_add(){
        return view('platform_business_add');
    }




    public function platform_business_edit($id){
        
        $plat = db("platform")->where("id",$id)->select();
        return view('platform_business_edit',['plat'=>$plat]);
    }


    /**
     * [汽车平台广告入库]
     * 郭杨
     */
    public function platform_business_save(Request $request){
        if ($request->isPost()) {
            $data = $request->param();
            $show_images = $request->file("advert_picture");

            if ($show_images) {
                $show_images = $request->file("advert_picture")->move(ROOT_PATH . 'public' . DS . 'uploads');
                $data["advert_picture"] = str_replace("\\", "/", $show_images->getSaveName());
                $str = explode('.',$data["advert_picture"]);
                $data["forms"] = $str[1];
            }

            $bool = db("platform")->insert($data);
            if ($bool) {
                $this->success("添加成功", url("admin/platform_advertisement/platform_business_index"));
            } else {
                $this->error("添加失败", url("admin/platform_advertisement/platform_business_add"));
            }
        }
    }


    /**
     * 平台广告删除
     * 陈绪
     */
    public function del(){



    }

}