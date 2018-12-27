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

class  PlatformAdvertisement extends  Controller{

    /**
     * [汽车平台广告显示]
     * 郭杨
     */
    public function platform_business_index(Request $request)
    {
        $platform = db("platform") -> order("status",0)->order("start_time desc")->select();
        $all_idents = $platform;//这里是需要分页的数据
        $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
        $listRow = 20;//每页20行记录
        $showdata = array_slice($all_idents, ($curPage - 1) * $listRow, $listRow, true);// 数组中根据条件取出一段值，并返回
        $platform = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path' => url('admin/platform_advertisement/platform_business_index'),//这里根据需要修改url
            'query' => [],
            'fragment' => '',
        ]);
        $platform->appends($_GET);
        $this->assign('platforme', $platform->render());
        return view('platform_business_index',['platform'=>$platform]);
    }



    public function platform_business_add($pid = 0)
    {      
        $goods_liste = [];
        if ($pid == 0) {
            $goods_liste = selectList("position");
        }
        
        return view('platform_business_add', ["goods_liste" => $goods_liste]);
    }



    /**
     * [汽车平台广告编辑]
     * 郭杨
     */
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

            $user_phone = Session::get("user_info");
            $id = $user_phone[0]["id"];
            $user = db("user")->where("phone_num",$user_phone[0]["phone"])->value("id");
            $store_name = db("store")->where("user_id",$user)->value("store_name");
            $area = db("store")->where("user_id",$user)->value("store_city_address");
            $position = db("position") -> where("id",$data["pid"])->value("name");

            if ($show_images) {
                $show_images = $request->file("advert_picture")->move(ROOT_PATH . 'public' . DS . 'uploads');
                $data["advert_picture"] = str_replace("\\", "/", $show_images->getSaveName());
                $str = explode('.',$data["advert_picture"]);
                $data["forms"] = $str[1];
            }

            $data["start_time"] = strtotime($data["start_time"]);
            $data["end_time"] = strtotime($data["end_time"]);           
            $data["location"] = $position;
            $data["pid"] = db("position") -> where("id",$data["pid"])->value("pid");
            $data["department"] = "platform_business";

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
            $find = db("platform")->where('id', $request->only(["id"])["id"])->find();

            if(isset($find['pgd']))
            {
               $bools = db("accessories")->where('id', $find['pgd'])->update(['status'=>$data["status"],'remarks'=>$data["remarks"]]);
            }
            if(isset($find['pfd']))
            {
                $boolse = db("facilitator")->where('id', $find['pfd'])->update(['status'=>$data["status"],'remarks'=>$data["remarks"]]);
            }

            
            if(empty($find['shop_name']))
            {
              $data["start_time"] = strtotime($data["start_time"]);
              $data["end_time"] = strtotime($data["end_time"]);  
            }     
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
            $activ = db("platform")->paginate(20);
        }
        if(!empty($activ)){
            return view('platform_business_index',['platform'=>$activ]);
        }
    }

}