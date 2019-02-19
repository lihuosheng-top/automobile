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
        $store_data  =Db::name('store')
            ->where("store_is_button",1)
            ->where("del_status",1)
            ->select();
       foreach ($store_data as $k=>$v){
           $store_data[$k]['store_id'] =$v['store_id'];
           $store_data[$k]['store_name'] =$v['store_name'];
           $store_data[$k]['store_detailed_address'] =$v['store_detailed_address'];
           $store_data[$k]['store_is_pay'] =$v['store_is_pay'];
           $store_data[$k]['operation_status'] =$v['operation_status'];
           $user_data =Db::name("user")->field("real_name,phone_num")->where('id',$v['user_id'])->find();
           $store_data[$k]['real_name']=$user_data['real_name'];
           $store_data[$k]['phone_num']=$user_data['phone_num'];
           $role_datas =Db::name("role")->field('name')->where('id',$v['role_id'])->find();
           $store_data[$k]['role_name']=$role_datas['name'];
       }
        $all_idents =$store_data ;//这里是需要分页的数据
        $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
        $listRow = 20;//每页3行记录
        $showdata = array_slice($all_idents, ($curPage - 1)*$listRow, $listRow,true);// 数组中根据条件取出一段值，并返回
        $store_data = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path'     => url('admin/Shop/index'),//这里根据需要修改url
            'query'    =>  [],
            'fragment' => '',
        ]);
        $store_data->appends($_GET);
        $this->assign('listpage', $store_data->render());
        return view("shop_index",['store_data'=>$store_data]);

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
        return view("shop_add",['data'=>$store_datas,'service_setting_data'=>$service_setting_data,'imgs'=>$imgs,'address'=>$address,'service_setting_id'=>$service_setting_id]);
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:店铺验证是体面店的删除
     **************************************
     */
    public function img_del(Request $request){
        if($request->isPost()){
            $data =$_POST;
            $bool =Db::name("store")->where('store_id',$data["id"])->update(["verifying_physical_storefront_one"=>null]);
            if(!empty($bool)){
                if(!empty($data["title"])){
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$data['title']);
                }
                return ajax_success('更新成功',["status"=>1]);
            }else{
                return ajax_error('更新失败',["status"=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:店铺店内体面店的删除
     **************************************
     * @param Request $request
     */
    public function img_store_img_del(Request $request){
        if($request->isPost()){
            $form_data =$_POST;
            $res = Db::name('store') ->where('store_id',$form_data["id"])->update(['verifying_physical_storefront_two'=>NULL]);
                $img_url =$request->only('title')['title'];
                if(!empty($img_url)){
                    $data =Db::name('store')
                        ->field('verifying_physical_storefront_two')
                        ->where('store_id',$form_data["id"])
                        ->find();
                    $datas =explode(',',$data['verifying_physical_storefront_two']);
                    foreach ($datas as $k=>$v) {
                        if ($v == $img_url) {
                            unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $v);
                        } else {
                            $new_data[] = $v;
                        }
                    }
                    if(!empty($new_data)){
                        $new_imgs_url =implode(',',$new_data);
                        $res = Db::name('store') ->where('store_id',$form_data["id"])->update(['verifying_physical_storefront_two'=>$new_imgs_url]);
                    }else{
                        $res = Db::name('store') ->where('store_id',$form_data["id"])->update(['verifying_physical_storefront_two'=>NULL]);
                    }
                    if($res){
                        return ajax_success('删除成功',['status'=>1]);
                    }else{
                        return ajax_success('删除失败',['status'=>0]);
                    }
                }
        }
    }



    /**
     **************李火生*******************
     * @param Request $request
     * Notes:店铺更新保存
     **************************************
     * @param $id
     */
    public function update($id){
        $img_data =$this->request->post();
        $form_data =[
            "store_is_pay" =>intval($img_data["store_is_pay"]),
            "store_order_num" =>intval($img_data["store_order_num"]),
            "operation_status" =>intval($img_data["operation_status"]),
           "is_hot_store" =>intval($img_data["is_hot_store"]),
            "operation_remarks" =>$img_data["operation_remarks"],
        ];
        //验证实体店面
        $verifying_physical_storefront_one_file = $this->request->file('imgs');
        if(!empty($verifying_physical_storefront_one_file)){
            $info = $verifying_physical_storefront_one_file->move(ROOT_PATH . 'public' . DS . 'uploads');
            $verifying_physical_storefront_one = str_replace("\\","/",$info->getSaveName());
            $form_data['verifying_physical_storefront_one'] =$verifying_physical_storefront_one;
        }
        //验证实体店内图片
        $verifying_physical_storefront_two = [];
        $verifying_physical_storefront_two_file = $this->request->file('all_imgs');
        if(!empty($verifying_physical_storefront_two_file)){
            foreach ($verifying_physical_storefront_two_file as $k=>$v) {
                $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                $verifying_physical_storefront_two[] = str_replace("\\", "/", $info->getSaveName());
            }
            if(count($verifying_physical_storefront_two) > 1){
                $new =implode(',',$verifying_physical_storefront_two);
            }else{
                $new =$verifying_physical_storefront_two[0];
            }
            if($new){
                $old =Db::name('store')->where('store_id',$id)->value("verifying_physical_storefront_two");
                if(!empty($old)){
                    $form_data['verifying_physical_storefront_two'] =$old.",".$new;
                }else{
                    $form_data['verifying_physical_storefront_two'] =$new;
                }
            }
        }
        $bool =Db::name('store')->where('store_id',$id)->update($form_data);
        if($bool){
            //通过则可以登录后台
            if($form_data['operation_status']==1){
                $user_id =Db::name("store")->field("user_id")->where("store_id",$id)->find();
                $phone =Db::name("user")->field("phone_num")->where('id',$user_id['user_id'])->find();
                Db::name('admin')->where('phone',$phone["phone_num"])->update(['status'=>1]);
                $content = "尊敬的用户您好，您的店铺审核通过，可以上架商品了";
                phone("qiche", "123qwe", $phone["phone_num"], $content);
            }
            //拒绝不可以登录后台
            if($form_data['operation_status']==-1){
                $user_id =Db::name("store")->field("user_id")->where("store_id",$id)->find();
                $phone =Db::name("user")->field("phone_num")->where('id',$user_id['user_id'])->find();
                Db::name('admin')->where('phone',$phone["phone_num"])->update(['status'=>0]);
                $content = "尊敬的用户您好，您的店铺审核不通过，请重新提交店铺信息";
                phone("qiche", "123qwe", $phone["phone_num"], $content);
            }
            $this->success('修改成功','admin/Shop/index');
        }else{
            $this->error('修改失败');
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:设置为热门店铺
     **************************************
     */
    public function store_hot_status(Request $request){
        if($request->isPost()) {
            $status = $request->only(["status"])["status"];
            if($status == -1) {
                $id = $request->only(["id"])["id"];
                $bool = Db::name("store")->where("store_id", $id)->update(["is_hot_store" => -1]);
                if ($bool) {
                    return ajax_success('修改成功',['status'=>1]);
                } else {
                    return ajax_error('修改失败',['status'=>0]);
                }
            }
            if($status == 1){
                $id = $request->only(["id"])["id"];
                $bool = Db::name("store")->where("store_id", $id)->update(["is_hot_store" => 1]);
                if ($bool) {
                    return ajax_success('修改成功',['status'=>1]);
                } else {
                    return ajax_error('修改失败',['status'=>0]);
                }
            }
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
            $phone =Db::name("user")->field('phone_num')->where('id',$user_id["user_id"])->find();
            $datas =Db::name("admin")->where('phone',$phone['phone_num'])->find();
            if(!empty($datas)){
                Db::name("admin")->where('phone',$phone['phone_num'])->update(["status"=>0]);//账号删除(伪删除)
            }
            $bool =Db::name('store')->where('store_id',$id)->update(["del_status"=>-1,"operation_status"=>-1]);//店铺删除（伪删除）
            if($bool){
                //所有的订单删除
                $this->success('删除成功','admin/Shop/index');
            }else{
                $this->error('删除失败');
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:批量删除(伪删除)
     **************************************
     * @param Request $request
     */
    public function dels(Request $request){
        if($request->isPost()){
            $id =$_POST['id'];
            if(is_array($id)){
                $where ='store_id in('.implode(',',$id).')';
                foreach ($id as $ks=>$vs){
                    $user_id =Db::name("store")->field('user_id')->where('store_id',$vs)->find();
                    $phone_data =Db::name("user")->field('phone_num')->where('id',$user_id["user_id"])->find();
                    $phone[] =$phone_data['phone_num'];
                }
                if(!empty($phone)){
                    $phones ='phone in('.implode(',',$phone).')';
                }
            }else{
                $where ='store_id='.$id;
                $user_id =Db::name("store")->field('user_id')->where('store_id',$id)->find();
                $phone_data =Db::name("user")->field('phone_num')->where('id',$user_id["user_id"])->find();
                $phones ='phone='.$phone_data['phone_num'];

            }
            $list =  Db::name('store')->where($where)->update(["status"=>0]);
            if($list!==false)
            {
                Db::name('admin')->where($phones)->update(["del_status"=>-1,"operation_status"=>-1]);
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
                ->where("del_status",1)
                ->select();
            foreach ($store_data as $k=>$v){
                $store_data[$k]['store_id'] =$v['store_id'];
                $store_data[$k]['store_name'] =$v['store_name'];
                $store_data[$k]['store_detailed_address'] =$v['store_detailed_address'];
                $store_data[$k]['store_is_pay'] =$v['store_is_pay'];
                $store_data[$k]['operation_status'] =$v['operation_status'];
                $user_data =Db::name("user")->field("real_name,phone_num")->where('id',$v['user_id'])->find();
                $store_data[$k]['real_name']=$user_data['real_name'];
                $store_data[$k]['phone_num']=$user_data['phone_num'];
                $role_datas =Db::name("role")->field('name')->where('id',$v['role_id'])->find();
                $store_data[$k]['role_name']=$role_datas['name'];
            }
        }else{
            if(!empty($keyword)){
            $conditions  = "`real_name` like '%{$keyword}%'";
                $store_data  =Db::table('tb_store')
                    ->join("tb_user", "tb_store.user_id=tb_user.id", 'left')
                    ->where($conditions)
                    ->where("del_status",1)
                    ->where("store_is_button",1)
                    ->select();
                foreach ($store_data as $k=>$v){
                    $store_data[$k]['store_id'] =$v['store_id'];
                    $store_data[$k]['store_name'] =$v['store_name'];
                    $store_data[$k]['store_detailed_address'] =$v['store_detailed_address'];
                    $store_data[$k]['store_is_pay'] =$v['store_is_pay'];
                    $store_data[$k]['operation_status'] =$v['operation_status'];
                    $user_data =Db::name("user")->field("real_name,phone_num")->where('id',$v['user_id'])->find();
                    $store_data[$k]['real_name']=$user_data['real_name'];
                    $store_data[$k]['phone_num']=$user_data['phone_num'];
                    $role_datas =Db::name("role")->field('name')->where('id',$v['role_id'])->find();
                    $store_data[$k]['role_name']=$role_datas['name'];
                }
        }else{
                $store_data  =Db::name('store')
                    ->where("store_is_button",1)
                    ->where("del_status",1)
                    ->select();
                foreach ($store_data as $k=>$v){
                    $store_data[$k]['store_id'] =$v['store_id'];
                    $store_data[$k]['store_name'] =$v['store_name'];
                    $store_data[$k]['store_detailed_address'] =$v['store_detailed_address'];
                    $store_data[$k]['store_is_pay'] =$v['store_is_pay'];
                    $store_data[$k]['operation_status'] =$v['operation_status'];
                    $user_data =Db::name("user")->field("real_name,phone_num")->where('id',$v['user_id'])->find();
                    $store_data[$k]['real_name']=$user_data['real_name'];
                    $store_data[$k]['phone_num']=$user_data['phone_num'];
                    $role_datas =Db::name("role")->field('name')->where('id',$v['role_id'])->find();
                    $store_data[$k]['role_name']=$role_datas['name'];
                }
            }
        }

        $all_idents =$store_data ;//这里是需要分页的数据
        $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
        $listRow = 20;//每页3行记录
        $showdata = array_slice($all_idents, ($curPage - 1)*$listRow, $listRow,true);// 数组中根据条件取出一段值，并返回
        $store_data = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path'     => url('admin/Shop/index'),//这里根据需要修改url
            'query'    =>  [],
            'fragment' => '',
        ]);
        $store_data->appends($_GET);
        $this->assign('listpage', $store_data->render());
        return view("shop_index",['store_data'=>$store_data]);
    }



}