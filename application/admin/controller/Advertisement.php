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
use think\Session;

class  Advertisement extends  Controller{


    /**
     * [汽车配件商广告显示]
     * 郭杨
     */

    public function accessories_business_advertising(Request $request){
        $user_phone = Session::get("user_info");
         dump($user_phone[0]["phone"]);
        // $platform = Db::table('tb_accessories')
        // ->field("tb_accessories.*,tb_store.store_name")
        // ->join("tb_user","tb_accessories.id = tb_user.id",'left')
        // ->join("tb_store","tb_store.user_id = tb_user.id","left")
        // ->where('tb_user.phone_num','$user_phone[0]["phone"]')
        // ->select();
        $user = db("user")->where("phone_num",$user_phone[0]["phone"])->value("id");
        $store_name = db("store")->where("user_id",$user)->value("store_name");
        halt($store_name);
        exit();
        return view('accessories_business_advertising',['platform'=>$platform]);
    }


    /**
     * [汽车配件商广告添加]
     * 郭杨
     */
    public function accessories_business_add(){
        return view('accessories_business_add');
    }



    /**
     * [汽车配件商广告编辑]
     * 郭杨
     */
    public function accessories_business_edit($id){

        $plat = db("accessories")->where("id",$id)->select();
        return view('accessories_business_edit',['plat'=>$plat]);
    }



    /**
     * [汽车配件商广告入库]
     * 郭杨
     */
    public function accessories_business_save(Request $request){
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
            $bool = db("accessories")->insert($data);
            $boole = db("platform")->insert($data);
            if ($bool && $boole) {
                $this->success("添加成功", url("admin/Advertisement/accessories_business_advertising"));
            } else {
                $this->error("添加失败", url("admin/Advertisement/accessories_business_add"));
            }
        }
    }


    /**
     * [汽车平台广告更新]
     * 郭杨
     * @param Request $request
     * 
     */
    public function accessories_business_updata(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->param();
            $bool = db("accessories")->where('id', $request->only(["id"])["id"])->update($data);

            if ($bool) {
                $this->success("编辑成功", url("admin/Advertisement/accessories_business_advertising"));
            } else {
                $this->error("编辑失败", url("admin/Advertisement/accessories_business_edit"));
            }
        }
    }



    /**
     * 汽车平台广告删除
     * 郭杨
     */
    public function accessories_business_del($id){

        $bool = db("accessories")->where("id", $id)->delete();
        if ($bool) {
            $this->success("删除成功", url("admin/Advertisement/accessories_business_advertising"));
        } else {
            $this->error("删除失败", url("admin/Advertisement/accessories_business_advertising"));
        }

    }


    /**
     * 汽车平台广告模糊搜索
     * 郭杨
     */
    public function accessories_business_search(){
        $ppd = input('key');          //广告名称
        $interest = input('keys');    //广告位置

        if ((!empty($ppd)) || (!empty($interest))) {
            $activ = db("accessories")->where("name", "like", "%" . $ppd . "%")->where("location", "like", "%" . $interest . "%")->paginate(2);    
        }else{
            $activ = db("accessories")->paginate(2);
        }
        if(!empty($activ)){
            return view('accessories_business_advertising',['platform'=>$activ]);
        }
    }

}