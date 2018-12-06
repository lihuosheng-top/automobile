<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/22
 * Time: 19:53
 */
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\paginator\driver\Bootstrap;

class Shop extends Controller{


    /**
     **************李火生*******************
     * @return \think\response\View
     * 店铺列表
     **************************************
     */
    public function index(){
        $store_data  =Db::name('store')->where("store_is_button",1)->select();
       foreach ($store_data as $k=>$v){
           $store_datas[$k]['store_id'] =$v['store_id'];
           $store_datas[$k]['store_name'] =$v['store_name'];
           $store_datas[$k]['store_detailed_address'] =$v['store_detailed_address'];
           $store_datas[$k]['store_is_pay'] =$v['store_is_pay'];
           $store_datas[$k]['operation_status'] =$v['operation_status'];
           $user_data =Db::name("user")->field("real_name,phone_num")->where('id',$v['user_id'])->find();
           $store_datas[$k]['real_name']=$user_data['real_name'];
           $store_datas[$k]['phone_num']=$user_data['phone_num'];
           $role_datas =Db::name("role")->field('name')->where('id',$v['role_id'])->find();
           $store_datas[$k]['role_name']=$role_datas['name'];
       }
        $all_idents =$store_data ;//这里是需要分页的数据
        $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
        $listRow = 3;//每页3行记录
        $showdata = array_slice($all_idents, ($curPage - 1)*$listRow, $listRow,true);// 数组中根据条件取出一段值，并返回
        $store_data = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path'     => url('admin/Shop/index'),//这里根据需要修改url
            'query'    =>  [],
            'fragment' => '',
        ]);
        $store_data->appends($_GET);
        $this->assign('listpage', $store_data->render());
        return view("shop_index",['store_data'=>$store_datas]);

    }



    /**
     **************李火生*******************
     * @param Request $request
     * Notes:店铺基本信息
     **************************************
     * @param $id
     * @return \think\response\View
     */
    public function add($id){
<<<<<<< HEAD
        $data =Db::name('store')->where('store_id',$id)->find(); //所有数据
        $store_work_server_range =explode(',',$data['store_work_server_range']);//服务范围
      
        foreach ($store_work_server_range as $k=>$v){
            $range_data[] =Db::name('service_setting')->field('service_setting_name')->where('service_setting_id',$v)->find();
        }
      
        $store_city_address =explode(',',$data['store_city_address']);  //三级城市
        return view("shop_add",['data'=>$data,'store_city_address'=>$store_city_address,'range_data'=>$range_data]);
=======
        $store_data  =Db::name('store')->where('store_id',$id)->select();
        foreach ($store_data as $k=>$v){
            $store_datas[$k] =$v;
            $user_data =Db::name("user")->field("real_name,phone_num")->where('id',$v['user_id'])->find();
            $store_datas[$k]['real_name']=$user_data['real_name'];
            $store_datas[$k]['phone_num']=$user_data['phone_num'];
            $role_datas =Db::name("role")->field('name')->where('id',$v['role_id'])->find();
            $store_datas[$k]['role_name']=$role_datas['name'];
            $imgs =explode(',',$v['verifying_physical_storefront_two']);
            $address =explode(',',$v['store_city_address']);
            if(!empty($v['service_setting_id'])){
                if(strpos($v['service_setting_id'],',')){
                    $service_setting_id =explode(',',$v['service_setting_id']);
                }else{
                    $service_setting_id[0] =$v['service_setting_id'];
                }
            }else{
                $service_setting_id =null;
            }
        }
        $service_setting_data =Db::name('service_setting')->where('service_setting_status',1)->select();
//        dump($store_datas);
        return view("shop_add",['data'=>$store_datas,'service_setting_data'=>$service_setting_data,'imgs'=>$imgs,'address'=>$address,'service_setting_id'=>$service_setting_id]);
    }




    /**
     **************李火生*******************
     * @param Request $request
     * Notes:店铺更新保存
     **************************************
     * @param $id
     */
    public function update($id){
        $data =$this->request->post();
        $bool =Db::name('store')->where('store_id',$id)->update($data);
        if($bool){
            //通过则可以登录后台
            if($data['operation_status']==1){
                $user_id =Db::name("store")->field("user_id")->where("store_id",$id)->find();
                $phone =Db::name("user")->field("phone_num")->where('id',$user_id['user_id'])->find();
                Db::name('admin')->where('phone',$phone["phone_num"])->update(['status'=>1]);
            }
            //拒绝不可以登录后台
            if($data['operation_status']==-1){
                $user_id =Db::name("store")->field("user_id")->where("store_id",$id)->find();
                $phone =Db::name("user")->field("phone_num")->where('id',$user_id['user_id'])->find();
                Db::name('admin')->where('phone',$phone["phone_num"])->update(['status'=>0]);
            }
            $this->success('修改成功','admin/Shop/index');
        }else{
            $this->error('修改失败');
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:店铺删除
     **************************************
     * @param $id
     */
    public function del($id){
        if($id>0){
            $user_id =Db::name("store")->field('user_id')->where('store_id',$id)->find();
            $phone =Db::name("user")->field('phone_num')->where('user_id',$user_id["user_id"])->find();
            $datas =Db::name("admin")->where('phone',$phone['phone_num'])->find();
            if(!empty($datas)){
                Db::name("admin")->where('phone',$phone['phone_num'])->delete();
            }
            $bool =Db::name('store')->where('store_id',$id)->delete();
            if($bool){
                $this->success('删除成功','admin/Shop/index');
            }else{
                $this->error('删除失败');
            }
        }
>>>>>>> 06f0ac87db0570d776ca17ae3a9ba442b6e2bb28
    }



    /**
     **************李火生*******************
     * @param Request $request
     * Notes:批量删除
     **************************************
     * @param Request $request
     */
    public function dels(Request $request){
        if($request->isPost()){
            $id =$_POST['id'];
            if(is_array($id)){
                $where ='store_id in('.implode(',',$id).')';
                foreach ($id as $ks=>$vs){
                    $phone_data =Db::name("store")->field('phone_num')->where('store_id',$vs)->find();
                    $phone[] =$phone_data['phone_num'];
                }
                if(!empty($phone)){
                    $phones ='phone in('.implode(',',$phone).')';
                }
            }else{
                $where ='store_id='.$id;
                $phone_data =Db::name("store")->field('phone_num')->where('store_id',$id)->find();
                $phones ='phone='.$phone_data['phone_num'];

            }
            $list =  Db::name('store')->where($where)->delete();
            if($list!==false)
            {
                Db::name('admin')->where($phones)->delete();
                return ajax_success('成功删除!',['status'=>1]);
            }else{
                return ajax_error('删除失败',['status'=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:模糊查询
     **************************************
     * @return \think\response\View
     */
    public function search(){
        $keywords =input('store_name');
        $keyword =input('store_owner');
        if(!empty($keywords)){
            $condition  = "`store_name` like '%{$keywords}%' ";
            $store_data  =Db::name('store')
                ->where($condition)
                ->where("store_is_button",1)
                ->select();
        }else{
            $store_data  =Db::name('store')
                ->where("store_is_button",1)
                ->select();
        }
//        if(!empty($keyword)){
//            $conditions  = "`real_name` like '%{$keyword}%'";
//
//        }
        foreach ($store_data as $k=>$v){
            $store_datas[$k]['store_id'] =$v['store_id'];
            $store_datas[$k]['store_name'] =$v['store_name'];
            $store_datas[$k]['store_detailed_address'] =$v['store_detailed_address'];
            $store_datas[$k]['store_is_pay'] =$v['store_is_pay'];
            $store_datas[$k]['operation_status'] =$v['operation_status'];
            $user_data =Db::name("user")->field("real_name,phone_num")->where('id',$v['user_id'])->find();
            $store_datas[$k]['real_name']=$user_data['real_name'];
            $store_datas[$k]['phone_num']=$user_data['phone_num'];
            $role_datas =Db::name("role")->field('name')->where('id',$v['role_id'])->find();
            $store_datas[$k]['role_name']=$role_datas['name'];
        }
        $all_idents =$store_data ;//这里是需要分页的数据
        $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
        $listRow = 3;//每页3行记录
        $showdata = array_slice($all_idents, ($curPage - 1)*$listRow, $listRow,true);// 数组中根据条件取出一段值，并返回
        $store_data = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path'     => url('admin/Shop/index'),//这里根据需要修改url
            'query'    =>  [],
            'fragment' => '',
        ]);
        $store_data->appends($_GET);
        $this->assign('listpage', $store_data->render());
//        return view("shop_index",['store_data'=>$store_datas]);
    }



}