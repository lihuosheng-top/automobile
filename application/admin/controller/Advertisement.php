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
use think\paginator\driver\Bootstrap;

class  Advertisement extends  Controller{


    /**
     * [汽车配件商广告显示]
     * 郭杨
     */

    public function accessories_business_advertising(Request $request){
        $user_phone = Session::get("user_info");
        $user = db("user")->where("phone_num",$user_phone[0]["phone"])->value("id");
        $store_name = db("store")->where("user_id",$user)->value("store_name");

        $platform = db("accessories")->where("pgone",$user_phone[0]["id"]) -> select();
        foreach ($platform as $key => $value) {
            if ($value["id"]) {
                $platform[$key]["shop_name"] = $store_name;
            }
        }
    
        $all_idents = $platform;//这里是需要分页的数据
        $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
        $listRow = 20;//每页20行记录
        $showdata = array_slice($all_idents, ($curPage - 1) * $listRow, $listRow, true);// 数组中根据条件取出一段值，并返回
        $platform = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path' => url('admin/Advertisement/accessories_business_advertising'),//这里根据需要修改url
            'query' => [],
            'fragment' => '',
        ]);
        $platform->appends($_GET);
        $this->assign('platform', $platform->render());
        return view('accessories_business_advertising',['platform'=>$platform]);
    }


    /**
     * [汽车配件商广告添加]
     * 郭杨
     */
    public function accessories_business_add($pid = 0)
    {
        $goods_liste = [];
        if ($pid == 0) {
            $goods_liste = selectList("position");
        }
        return view('accessories_business_add', ["goods_liste" => $goods_liste]);
    }



    /**
     * [汽车配件商广告编辑]
     * 郭杨
     */
    public function accessories_business_edit($id,$pid=0)
    {
        
        $goods_liste = [];
        if ($pid == 0) {
            $goods_liste = selectList("position");
        }
        $plat = db("accessories")->where("id",$id)->select();       
        return view('accessories_business_edit',['plat'=>$plat,"goods_liste" => $goods_liste]);
    }



    /**
     * [汽车配件商广告入库]
     * 郭杨
     */
    public function accessories_business_save(Request $request){
        if ($request->isPost()) {
            $data = $request->param();
            $show_images = $request->file("advert_picture");

            $user_phone = Session::get("user_info");
            $id = $user_phone[0]["id"];
            $user = db("user")->where("phone_num",$user_phone[0]["phone"])->value("id");
            $store_name = db("store")->where("user_id",$user)->value("store_name");
            $area = db("store")->where("user_id",$user)->value("store_city_address");
            $position = db("position") -> where("id",$data["pid"])->value("name");

            //插入配件商表
            if ($show_images) {
                $show_images = $request->file("advert_picture")->move(ROOT_PATH . 'public' . DS . 'uploads');
                $data["advert_picture"] = str_replace("\\", "/", $show_images->getSaveName());
                $str = explode('.',$data["advert_picture"]);
                $data["forms"] = $str[1];
            }

            $data["start_time"] = strtotime($data["start_time"]);
            $data["end_time"] = strtotime($data["end_time"]);
            $data["pgone"] = $id;
            $data["area"] = $area;
            $data["location"] = $position;
            $userId = db('accessories')->insertGetId($data);

            //插入平台列表
            $data["postid"] = $data["pid"];
            $data["pid"] = db("position") -> where("id",$data["pid"])->value("pid"); //找到广告位置pid
            $data["pgd"] = $userId;
            $data["department"] = $user_phone[0]["department"];
            $data["shop_name"] = $store_name;
            unset($data["pgone"]);
            

            $boole = db("platform")->insert($data);
            if ($userId && $boole) {
                $this->success("添加成功", url("admin/Advertisement/accessories_business_advertising"));
            } else {
                $this->error("添加失败", url("admin/Advertisement/accessories_business_add"));
            }
        }
    }


    /**
     * [汽车配件商广告更新]
     * 郭杨
     * @param Request $request
     * 
     */
    public function accessories_business_updata(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->param();
            $position = db("position") -> where("id",$data["pid"])->value("name");
            $data["location"] = $position;
            $data["start_time"] = strtotime($data["start_time"]);
            $data["end_time"] = strtotime($data["end_time"]);
            $bool = db("accessories")->where('id', $request->only(["id"])["id"])->update($data);

            unset($data["id"]);
            unset($data["pid"]);
            $boole = db("platform")->where('pgd', $request->only(["id"])["id"])->update($data);

            if ($bool && $boole) {
                $this->success("编辑成功", url("admin/Advertisement/accessories_business_advertising"));
            } else {
                $this->error("编辑失败", url("admin/Advertisement/accessories_business_edit"));
            }
        }
    }



    /**
     * 汽车配件商广告删除
     * 郭杨
     */
    public function accessories_business_del($id){

        $bool = db("accessories")->where("id", $id)->delete();
        $boole = db("platform")->where("pgd", $id)->delete();
        if ($bool && $boole) {
            $this->success("删除成功", url("admin/Advertisement/accessories_business_advertising"));
        } else {
            $this->error("删除失败", url("admin/Advertisement/accessories_business_advertising"));
        }

    }


    /**
     * 汽车配件商广告模糊搜索
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