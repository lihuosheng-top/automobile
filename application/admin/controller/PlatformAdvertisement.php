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



    /**
     * [汽车平台广告编辑]
     * 郭杨
     */
    public function platform_business_edit($id){

        $plat = db("platform")->where("id",$id)->select();
        //dump($plat);

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

            $data["start_time"] = strtotime($data["start_time"]);
            $data["end_time"] = strtotime($data["end_time"]);
            $bool = db("platform")->insert($data);
            if ($bool) {
                $this->success("添加成功", url("admin/platform_advertisement/platform_business_index"));
            } else {
                $this->error("添加失败", url("admin/platform_advertisement/platform_business_add"));
            }
        }
    }


    /**
     * [汽车平台广告更新]
     * 郭杨
     * @param Request $request
     * @param $id
     */
    public function platform_business_updata(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->param();

            $bool = db("platform")->where('id', $request->only(["id"])["id"])->update($data);

            if ($bool) {
                $this->success("编辑成功", url("admin/platform_advertisement/platform_business_index"));
            } else {
                $this->error("编辑失败", url("admin/platform_advertisement/platform_business_edit"));
            }
        }
    }
  
    
    /**
     * 平台广告删除
     * 郭杨
     */
    public function platform_business_del($id){

        $bool = db("platform")->where("id", $id)->delete();
        if ($bool) {
            $this->success("删除成功", url("admin/platform_advertisement/platform_business_index"));
        } else {
            $this->error("删除失败", url("admin/platform_advertisement/platform_business_index"));
        }

    }


    /**
     * 平台广告模糊搜索
     * 郭杨
     */
    public function platform_business_search(){
        $ppd = input('key');          //广告名称
        $interest = input('keys');    //广告位置

        if ((!empty($ppd)) || (!empty($interest))) {
            $activ = db("platform")->where("name", "like", "%" . $ppd . "%")->where("location", "like", "%" . $interest . "%")->paginate(2);    
        }else{
            $activ = db("platform")->paginate(2);
        }
        if(!empty($activ)){
            return view('platform_business_index',['platform'=>$activ]);
        }
    }

}