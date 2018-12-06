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
        dump($store_datas);
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
            $bool =Db::name('store')->where('store_id',$id)->delete();
            if($bool){
                $this->success('删除成功','admin/Shop/index');
            }else{
                $this->error('删除失败');
            }
        }
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
            }else{
                $where ='store_id='.$id;
            }
            $list =  Db::name('store')->where($where)->delete();
            if($list!==false)
            {
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
            $condition  = "`store_name` like '%{$keywords}%' or `store_owner` like '%{$keywords}%' ";
        }
        if(!empty($keyword)){
            $condition  = "`store_name` like '%{$keyword}%' or `store_owner` like '%{$keyword}%' ";
        }
        $store_data  =Db::name('store')->where($condition)->paginate(3 ,false, [
            'query' => request()->param(),
        ]);
        return view("shop_index",['store_data'=>$store_data]);
    }



}