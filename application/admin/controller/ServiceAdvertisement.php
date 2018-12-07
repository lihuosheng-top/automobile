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

class  ServiceAdvertisement extends  Controller
{


    /**
     * [汽车服务商广告显示]
     * 郭杨
     */

    public function service_business_advertising(Request $request){
        $user_phone = Session::get("user_info");
        $user = db("user")->where("phone_num",$user_phone[0]["phone"])->value("id");
        $store_name = db("store")->where("user_id",$user)->value("store_name");

        $platform = db("facilitator") -> select();
        foreach ($platform as $key => $value) {
            if ($value["id"]) {
                $platform[$key]["shop_name"] = $store_name;
                $platform[$key]["pid"] = $value["id"];
            }
        }

        $all_idents = $platform;//这里是需要分页的数据
        $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
        $listRow = 20;//每页20行记录
        $showdata = array_slice($all_idents, ($curPage - 1) * $listRow, $listRow, true);// 数组中根据条件取出一段值，并返回
        $platform = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path' => url('admin/service_advertisement/service_business_advertising'),//这里根据需要修改url
            'query' => [],
            'fragment' => '',
        ]);
        $platform->appends($_GET);
        $this->assign('platforms', $platform->render());
        return view('service_business_advertising',['platform'=>$platform]);
    }


    /**
     * [汽车服务商广告添加]
     * 郭杨
     */
    public function service_business_add(){
        return view('service_business_add');
    }



    /**
     * [汽车服务商广告编辑]
     * 郭杨
     */
    public function service_business_edit($id){

        $plat = db("facilitator")->where("id",$id)->select();
        return view('service_business_edit',['plat'=>$plat]);
    }



    /**
     * [汽车服务商广告入库]
     * 郭杨
     */
    public function service_business_save(Request $request){
        if ($request->isPost()) {
            $data = $request->param();
            $show_images = $request->file("advert_picture");

            $user_phone = Session::get("user_info");
            $user = db("user")->where("phone_num",$user_phone[0]["phone"])->value("id");
            $store_name = db("store")->where("user_id",$user)->value("store_name");
               

            if ($show_images) {
                $show_images = $request->file("advert_picture")->move(ROOT_PATH . 'public' . DS . 'uploads');
                $data["advert_picture"] = str_replace("\\", "/", $show_images->getSaveName());
                $str = explode('.',$data["advert_picture"]);
                $data["forms"] = $str[1];
            }

            $data["start_time"] = strtotime($data["start_time"]);
            $data["end_time"] = strtotime($data["end_time"]);

            $userId = db('facilitator')->insertGetId($data);
            $data["pid"] = $userId;
            $boole = db("platform")->insert($data);
            if ($userId && $boole) {
                $this->success("添加成功", url("admin/service_advertisement/service_business_advertising"));
            } else {
                $this->error("添加失败", url("admin/service_advertisement/service_add"));
            }
        }
    }


    /**
     * [汽车服务商广告更新]
     * 郭杨
     * @param Request $request
     * 
     */
    public function service_business_updata(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->param();

            $data["start_time"] = strtotime($data["start_time"]);
            $data["end_time"] = strtotime($data["end_time"]);
            
            $bool = db("facilitator")->where('id', $request->only(["id"])["id"])->update($data);
            unset($data["id"]);
            $boole = db("platform")->where('pid', $request->only(["id"])["id"])->update($data);

            if ($bool && $boole) {
                $this->success("编辑成功", url("admin/service_advertisement/service_business_advertising"));
            } else {
                $this->error("编辑失败", url("admin/service_advertisement/service_business_edit"));
            }
        }
    }



    /**
     * 汽车服务商广告删除
     * 郭杨
     */
    public function service_business_del($id){

        $bool = db("facilitator")->where("id", $id)->delete();
        $boole = db("platform")->where("pid", $id)->delete();
        if ($bool && $boole) {
            $this->success("删除成功", url("admin/service_advertisement/service_business_advertising"));
        } else {
            $this->error("删除失败", url("admin/service_advertisement/service_business_advertising"));
        }

    }


    /**
     * 汽车服务商广告模糊搜索
     * 郭杨
     */
    public function service_business_search(){
        $ppd = input('key');          //广告名称
        $interest = input('keys');    //广告位置

        if ((!empty($ppd)) || (!empty($interest))) {
            $activ = db("facilitator")->where("name", "like", "%" . $ppd . "%")->where("location", "like", "%" . $interest . "%")->paginate(2);    
        }else{
            $activ = db("facilitator")->paginate(2);
        }
        if(!empty($activ)){
            return view('service_business_advertising',['platform'=>$activ]);
        }
    }



}