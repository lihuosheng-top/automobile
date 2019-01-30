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
        $t= date('Y-m-d H:i:s');
        $time  = strtotime($t);
        $end_time =  "end_time < {$time}";
        $status = Db::name("platform")->where($end_time)->update(["status"=>3]); //到时间自动下架
        $data = db("platform")->select();
        $platform = foreach_pid($data);
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

    /**
     * [汽车平台广告添加]
     * 郭杨
     */

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
    public function platform_business_edit($pid=0,$id){
        $goods_liste = [];
        if ($pid == 0) {
            $goods_liste = selectList("position");
        }
        $plat = db("platform")->where("id",$id)->select();
        foreach($plat as $key => $value)
        {
            $plat[$key]['store_name'] = db('store')->where("store_id",$plat[$key]['store_id'])->value('store_name');
        }
        return view('platform_business_edit',['plat'=>$plat,"goods_liste" => $goods_liste]);
    }


    /**
     * [汽车平台广告入库]
     * 郭杨
     */
    public function platform_business_save(Request $request){
        if ($request->isPost()) {
            $data = $request->param();
            $show_images = $request->file("advert_picture");
            $store_data = db("store")->where("store_name",$data["url"])->find();            
            $position = db("position") -> where("id",$data["pid"])->value("name");

            //http://127.0.0.1/automobile/public/store_index?storeId=58
            if(!empty($store_data)){
                $data["url"] = config('domain_url.address')."store_index?storeId=".$store_data['store_id'];
                $data["longitude"] = $store_data['longitude']; //坐标经度
                $data["latitude"] = $store_data['latitude'];   //坐标纬度
                $data["store_id"] = $store_data['store_id'];
            } else if(empty($store_data['store_id'])){
                $data["url"] = null;
                $data['store_id'] = null;
            }

            if ($show_images) {
                $show_images = $request->file("advert_picture")->move(ROOT_PATH . 'public' . DS . 'uploads');
                $data["advert_picture"] = str_replace("\\", "/", $show_images->getSaveName());
                $str = explode('.',$data["advert_picture"]);
                $data["forms"] = $str[1];
            }

            $data["start_time"] = strtotime($data["start_time"]);
            $data_times = strtotime($data["end_time"]);   
            $t= date('Y-m-d H:i:s',$data_times+1*24*60*60);
            $data["end_time"]  = strtotime($t);  
                   
            $data["location"] = $position;
            $data["postid"] = $data["pid"];
            $data["pid"] = db("position") -> where("id",$data["pid"])->value("pid");
            $data["department"] = "platform_business";
            $data["shop_name"] = '平台广告';
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


            if($find["department"] == "platform_business")
            {
                $data["postid"] = $data["pid"];
                $test_id = db("position") -> where("id",$data["pid"])->value("pid");
                $data["pid"] = $test_id; 
                $data["start_time"] = strtotime($data["start_time"]);
                $data["end_time"]  = strtotime($data["end_time"]); 
                $store_data = db("store")->where("store_name",$data["url"])->find();
                //http://127.0.0.1/automobile/public/store_index?storeId=58
                if(!empty($store_data)){
                    $data['store_id'] = $store_data['store_id']; //店铺id
                    $data["url"] = config('domain_url.address')."store_index?storeId=".$store_data['store_id'];
                    $data["longitude"] = $store_data['longitude']; //坐标经度
                    $data["latitude"] = $store_data['latitude'];   //坐标纬度
                } else {
                    $data["url"] = null;
                    $data['store_id'] = null;
                } 
            }

            if(!empty($find['pgd']))
            {   
               $bools = db("accessories")->where('id', $find['pgd'])->update(['status'=>$data["status"],'remarks'=>$data["remarks"]]);
            }

            $show_images = $request->file("advert_picture");
            if ($show_images) {
                $show_images = $request->file("advert_picture")->move(ROOT_PATH . 'public' . DS . 'uploads');
                $data["advert_picture"] = str_replace("\\", "/", $show_images->getSaveName());
            }

            $bool = db("platform")->where('id', $request->only(["id"])["id"])->update($data);
           
            if ($bool || $bools ) {
                $this->success("编辑成功", url("admin/platform_advertisement/platform_business_index"));
            } else {
                $this->error("编辑失败", url("admin/platform_advertisement/platform_business_index"));
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
     * 平台广告图片删除
     * 郭杨
     */
    public function platform_picture_del(Request $request){

        if ($request->isPost()) {
            $id = $request->only(['id'])['id'];
            $image_url = db("platform")->where("id", $id)->field("advert_picture")->find();
            if ($image_url['advert_picture'] != null) {
                unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $image_url['advert_picture']);
            }
            $bool = db("platform")->where("id", $id)->field("advert_picture")->update(["advert_picture" => null]);
            if ($bool) {
                return ajax_success("删除成功");
            } else {
                return ajax_error("删除失败");
            }
        }

    }


    /**
     * 平台广告模糊搜索
     * 郭杨
     */
    public function platform_business_search(){
        $shop_name = input('platform');    //店铺名称
        $name = input('name');           //广告名称
        $pid = input('keys');      //广告位置
        $status = input('status');     //广告状态


        if ((!empty($shop_name)) && (!empty($name)) && (!empty($pid)) && (!empty($status)) ) {
            $data = db("platform")
                    ->where("name", "like", "%" . $name . "%")
                    ->where("shop_name", "like", "%" . $shop_name . "%")
                    ->where("pid", "like", "%" . $pid . "%")
                    ->where("status", "like", "%" . $status . "%")
                    ->select(); 
                    $platform = foreach_pid($data);
        }else if ((!empty($shop_name)) && (empty($name)) && (!empty($pid)) && (!empty($status))){
            $data = db("platform")
                    ->where("shop_name", "like", "%" . $shop_name . "%")
                    ->where("pid", "like", "%" . $pid . "%")
                    ->where("status", "like", "%" . $status . "%")
                    ->select();
                    $platform = foreach_pid($data);
        }else if ((!empty($shop_name)) && (!empty($name)) && (empty($pid)) && (!empty($status))){
            $data = db("platform")
                    ->where("name", "like", "%" . $name . "%")
                    ->where("shop_name", "like", "%" . $shop_name . "%")
                    ->where("status", "like", "%" . $status . "%")
                    ->select();
                    $platform = foreach_pid($data);
        }else if ((!empty($shop_name)) && (!empty($name)) && (!empty($pid)) && (empty($status))){
            $data = db("platform")
                    ->where("pid", "like", "%" . $pid . "%")
                    ->where("name", "like", "%" . $name . "%")
                    ->where("shop_name", "like", "%" . $shop_name . "%")
                    ->select();
                    $platform = foreach_pid($data);
        } else if ((!empty($shop_name)) && (!empty($name)) && (!empty($pid)) && (!empty($status)) ) {
            $data = db("platform")
                    ->where("name", "like", "%" . $name . "%")
                    ->where("shop_name", "like", "%" . $shop_name . "%")
                    ->where("pid", "like", "%" . $pid . "%")
                    ->where("status", "like", "%" . $status . "%")
                    ->select(); 
                    $platform = foreach_pid($data);
        } else if ((!empty($shop_name)) && (empty($name)) && (empty($pid)) && (empty($status)) ) {
            $data = db("platform")
                    ->where("shop_name", "like", "%" . $shop_name . "%")
                    ->select(); 
                    $platform = foreach_pid($data);
        } else if ((empty($shop_name)) && (!empty($name)) && (empty($pid)) && (empty($status)) ) {
            $data = db("platform")
                    ->where("name", "like", "%" . $name . "%")
                    ->select(); 
                    $platform = foreach_pid($data);
        } else if ((empty($shop_name)) && (empty($name)) && (!empty($pid)) && (empty($status)) ) {
            $data = db("platform")
                    ->where("pid", "like", "%" . $pid . "%")
                    ->select(); 
                    $platform = foreach_pid($data);
        } else if ((empty($shop_name)) && (empty($name)) && (empty($pid)) && (!empty($status)) ) {
            $data = db("platform")
                    ->where("status", "like", "%" . $status . "%")
                    ->select(); 
                    $platform = foreach_pid($data);
        } else if ((!empty($shop_name)) && (!empty($name)) && (empty($pid)) && (empty($status)) ) {
            $data = db("platform")
                    ->where("name", "like", "%" . $name . "%")
                    ->where("shop_name", "like", "%" . $shop_name . "%")
                    ->select(); 
                    $platform = foreach_pid($data);
        } else if ((!empty($shop_name)) && (empty($name)) && (!empty($pid)) && (empty($status)) ) {
            $data = db("platform")
                    ->where("shop_name", "like", "%" . $shop_name . "%")
                    ->where("pid", "like", "%" . $pid . "%")
                    ->select(); 
                    $platform = foreach_pid($data);
        } else if ((!empty($shop_name)) && (empty($name)) && (empty($pid)) && (!empty($status)) ) {
            $data = db("platform")
                    ->where("shop_name", "like", "%" . $shop_name . "%")
                    ->where("status", "like", "%" . $status . "%")
                    ->select(); 
                    $platform = foreach_pid($data);
        } else if ((empty($shop_name)) && (!empty($name)) && (!empty($pid)) && (empty($status)) ) {
            $data = db("platform")
                    ->where("name", "like", "%" . $name . "%")
                    ->where("pid", "like", "%" . $pid . "%")
                    ->select(); 
                    $platform = foreach_pid($data);
        } else if ((empty($shop_name)) && (!empty($name)) && (empty($pid)) && (!empty($status)) ) {
            $data = db("platform")
                    ->where("name", "like", "%" . $name . "%")
                    ->where("status", "like", "%" . $status . "%")
                    ->select(); 
                    $platform = foreach_pid($data);
        }
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

}