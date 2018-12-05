<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/23
 * Time: 14:05
 */
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;

class Store extends Controller{


    /**
     * 店铺首页
     * 陈绪
     */
    public function index(){

        return view("store_index");

    }



    /**
     * 我要加盟
     * 陈绪
     */
    public function league(Request $request){
        if($request->isPost()) {
            $user_id = Session::get("user");
            $user = db("user")->where("id", $user_id)->field("phone_num,sex,real_name")->select();
            $roles = db("role")->where("status", "1")->field("id,name")->select();
            foreach ($roles as $key => $value) {
                 if($value["id"] != 2){
                    $role[] =$value;
                }
            }
            $service_setting_info =db("service_setting")
                ->field("service_setting_id,service_setting_name")
                ->where("service_setting_status",1)
                ->select();
            if ($roles) {
                return ajax_success("获取成功", array("user" => $user, "roles" => $role,"service_setting_info"=>$service_setting_info));
            } else {
                return ajax_error("获取失败");
            }
        }
        return view("store_league");
    }

    /**
     * 身份验证
     * 陈绪
     */
    public function verify(){
        return view("store_verify");
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:第一页加盟添加
     * 店铺名称，store_name
     *负责人姓名，real_name
     *手机号，phone_num
     *座机号码，store_owner_seat_num
     *性别（男女中文），sex
     *店铺logo,store_logo_images
     *营业时间，store_do_bussiness_time
     *经营范围id（逗号隔开），service_setting_id
     *店铺所在区域（逗号隔开）（广东省,深圳市,南山区），store_city_address
     *店铺详细地址，store_street_address
     *店铺信息，store_information
     *邮箱，store_owner_email
     *绑定微信，store_owner_wechat
     *我要加盟为，role_id
     **************************************
     */
    public function add(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");
            $is_store =Db::name('store')->field('store_id,role_id')->where('user_id',$user_id)->find();
            if(!empty($is_store)){
                $store_type =Db::name("role")->field('name')->where('id',$is_store['role_id'])->find();
                return ajax_success('此用户已经加盟过'.$store_type['name'],['store_id'=>$is_store['store_id']]);
            }
            $input_data = $_POST;
            $store_name =trim($input_data['store_name']);
            $real_name =trim($input_data['real_name']);
            $phone_num =trim($input_data['phone_num']);
            $store_owner_seat_num =trim($input_data['store_owner_seat_num']);
            $sex =trim($input_data['sex']);
            $store_do_bussiness_time =trim($input_data['store_do_bussiness_time']); //营业时间
            if(!empty($input_data['service_setting_id'])){
                $service_setting_id =trim($input_data['service_setting_id']);
            }else{
                $service_setting_id =null;
            }
            $store_city_address =trim($input_data['store_city_address']);
            $store_street_address =trim($input_data['store_street_address']);
            $store_information =trim($input_data['store_information']);
            $store_owner_email =trim($input_data['store_owner_email']);
            $store_owner_wechat =trim($input_data['store_owner_wechat']);
            $role_id =trim($input_data['role_id']);
               $file = $request->file('store_logo_images');
               if(!empty($file)){
                       $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                       $evaluation_images = str_replace("\\","/",$info->getSaveName()); //图片

                       $ex_address =explode(',',$store_city_address);
                       foreach ($ex_address as $k=>$v){
                           $explode_data[] =$v;
                       }
                    $store_detailed_address =$explode_data[0].$explode_data[1].$explode_data[2].$store_street_address;
                   db("user")->where('id',$user_id)->update(['real_name'=>$real_name,'phone_num'=>$phone_num,'sex'=>$sex]);
                   $data =[
                       'store_name'=>$store_name,
                       'store_detailed_address'=>$store_detailed_address,//店铺具体地址
                       'store_owner_seat_num'=>$store_owner_seat_num,
                       'store_is_pay'=>0, //是否付费上架（0是没付费，1是付费）
                       'store_examine_status'=>0, //平台审核状态（-1为未通过，0为待审核，1为审核通过）
                       'store_logo_images'=>$evaluation_images,
                       'store_do_bussiness_time'=>$store_do_bussiness_time,
                       'service_setting_id'=>$service_setting_id,
                       'store_street_address'=>$store_street_address,
                       'store_city_address'=>$store_city_address,
                       'store_owner_email'=>$store_owner_email,
                       'store_owner_wechat'=>$store_owner_wechat,
                       'store_information'=>$store_information,
                       'user_id'=> $user_id,
                       'role_id'=>$role_id
                   ];
                   $bool = db("store")->insertGetId($data);
                   if($bool > 0){
                       return ajax_success('添加成功',['store_id'=>$bool]);
                   }else{
                       return ajax_error('添加失败',['status'=>0]);
                   }
               }else{
                   return ajax_error('请上传店铺logo',['status'=>0]);
               }
           }
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:店铺编辑更新(也是第二页完善店铺信息)
     **************************************
     * @param Request $request
     */
    public function save(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");
            $input_data = $_POST;
            $store_name =trim($input_data['store_name']);
            $real_name =trim($input_data['real_name']);
            $phone_num =trim($input_data['phone_num']);
            $store_owner_seat_num =trim($input_data['store_owner_seat_num']);
            $sex =trim($input_data['sex']);
            $store_do_bussiness_time =trim($input_data['store_do_bussiness_time']); //营业时间
            if(!empty($input_data['service_setting_id'])){
                $service_setting_id =trim($input_data['service_setting_id']);
            }else{
                $service_setting_id =null;
            }
            $store_city_address =trim($input_data['store_city_address']);
            $store_street_address =trim($input_data['store_street_address']);
            $store_information =trim($input_data['store_information']);
            $store_owner_email =trim($input_data['store_owner_email']);
            $store_owner_wechat =trim($input_data['store_owner_wechat']);
            $role_id =trim($input_data['role_id']);
            $file = $request->file('store_logo_images');
            if(!empty($file)){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $evaluation_images = str_replace("\\","/",$info->getSaveName()); //图片
                $image_url = db("store")->where("user_id",$user_id)->field("store_logo_images")->find();
                $ex_address =explode(',',$store_city_address);
                foreach ($ex_address as $k=>$v){
                    $explode_data[] =$v;
                }
                $store_detailed_address =$explode_data[0].$explode_data[1].$explode_data[2].$store_street_address;
                db("user")->where('id',$user_id)->update(['real_name'=>$real_name,'phone_num'=>$phone_num,'sex'=>$sex]);
                $data =[
                    'store_name'=>$store_name,
                    'store_detailed_address'=>$store_detailed_address,//店铺具体地址
                    'store_owner_seat_num'=>$store_owner_seat_num,
                    'store_logo_images'=>$evaluation_images,
                    'store_do_bussiness_time'=>$store_do_bussiness_time,
                    'service_setting_id'=>$service_setting_id,
                    'store_street_address'=>$store_street_address,
                    'store_city_address'=>$store_city_address,
                    'store_owner_email'=>$store_owner_email,
                    'store_owner_wechat'=>$store_owner_wechat,
                    'store_information'=>$store_information,
                    'role_id'=>$role_id,
                ];
                $bool = db("store")->where('user_id',$user_id)->update($data);
                $store_id =db("store")->field('store_id')->where('user_id',$user_id)->find();
                if($bool > 0){
                    //删除图片
                    if($image_url['store_logo_images'] != null){
                        unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$image_url['store_logo_images']);
                    }
                    return ajax_success('编辑成功',['store_id'=>$store_id['store_id']]);
                }else{
                    return ajax_error('编辑失败',['status'=>0]);
                }
            }else{
                $ex_address =explode(',',$store_city_address);
                foreach ($ex_address as $k=>$v){
                    $explode_data[] =$v;
                }
                $store_detailed_address =$explode_data[0].$explode_data[1].$explode_data[2].$store_street_address;
                db("user")->where('id',$user_id)->update(['real_name'=>$real_name,'phone_num'=>$phone_num,'sex'=>$sex]);
                $data =[
                    'store_name'=>$store_name,
                    'store_detailed_address'=>$store_detailed_address,//店铺具体地址
                    'store_owner_seat_num'=>$store_owner_seat_num,
                    'store_do_bussiness_time'=>$store_do_bussiness_time,
                    'service_setting_id'=>$service_setting_id,
                    'store_street_address'=>$store_street_address,
                    'store_city_address'=>$store_city_address,
                    'store_owner_email'=>$store_owner_email,
                    'store_owner_wechat'=>$store_owner_wechat,
                    'store_information'=>$store_information,
                    'role_id'=>$role_id,
                ];
                $bool = db("store")->where('user_id',$user_id)->update($data);
                $store_id =db("store")->field('store_id')->where('user_id',$user_id)->find();
                if($bool > 0){
                    return ajax_success('编辑成功',['store_id'=>$store_id['store_id']]);
                }else{
                    return ajax_error('没有数据改变',['status'=>0]);
                }
            }

        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:店铺编辑更新(也是第二页完善店铺信息)
     **************************************
     * @param Request $request
     */
    public function update(Request $request){
        if($request->isPost()){
            //身份证正面
            $store_identity_card_file = $request->file('store_identity_card');
//            if(empty($store_identity_card_file)){
//                return ajax_error('身份证正面照未上传',['status'=>0]);
//            }
            if(!empty($store_identity_card_file)){
                $info = $store_identity_card_file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $store_identity_card = str_replace("\\","/",$info->getSaveName());
                $data['store_identity_card'] =$store_identity_card;
            }
            //身份证反面
            $store_reverse_images_file = $request->file('store_reverse_images');
            if(!empty($store_reverse_images_file)){
                $info = $store_reverse_images_file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $store_reverse_images = str_replace("\\","/",$info->getSaveName());
                $data['store_reverse_images'] =$store_reverse_images;
            }
            //营业执照正面
            $store_do_bussiness_positive_img_file = $request->file('store_do_bussiness_positive_img');
            if(!empty($store_do_bussiness_positive_img_file)){
                $info = $store_do_bussiness_positive_img_file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $store_do_bussiness_positive_img = str_replace("\\","/",$info->getSaveName());
                $data['store_do_bussiness_positive_img'] =$store_do_bussiness_positive_img;
            }
            //营业执照反面
            $store_do_bussiness_side_img_file = $request->file('store_do_bussiness_side_img');
            if(!empty($store_do_bussiness_side_img_file)){
                $info = $store_do_bussiness_side_img_file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $store_do_bussiness_side_img= str_replace("\\","/",$info->getSaveName());
                $data['store_do_bussiness_side_img'] =$store_do_bussiness_side_img;
            }
            //验证实体店面第一张
            $verifying_physical_storefront_one_file = $request->file('verifying_physical_storefront_one');
            if(!empty($verifying_physical_storefront_one_file)){
                $info = $verifying_physical_storefront_one_file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $verifying_physical_storefront_one = str_replace("\\","/",$info->getSaveName());
                $data['verifying_physical_storefront_one'] =$verifying_physical_storefront_one;
            }
            //验证实体店面第二张
            $verifying_physical_storefront_two_file = $request->file('verifying_physical_storefront_two');
            if(!empty($verifying_physical_storefront_two_file)){
                $info = $verifying_physical_storefront_two_file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $verifying_physical_storefront_two= str_replace("\\","/",$info->getSaveName());
                $data['verifying_physical_storefront_two'] =$verifying_physical_storefront_two;
            }
            //验证实体店面第三张
            $verifying_physical_storefront_three_file = $request->file('verifying_physical_storefront_three');
            if(!empty($verifying_physical_storefront_three_file)){
                $info = $verifying_physical_storefront_three_file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $verifying_physical_storefront_three = str_replace("\\","/",$info->getSaveName());
                $data['verifying_physical_storefront_three'] =$verifying_physical_storefront_three;
            }
            //验证实体店面第四张
            $verifying_physical_storefront_four_file = $request->file('verifying_physical_storefront_four');
            if(!empty($verifying_physical_storefront_four_file)){
                $info = $verifying_physical_storefront_four_file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $verifying_physical_storefront_four = str_replace("\\","/",$info->getSaveName());
                $data['verifying_physical_storefront_four'] =$verifying_physical_storefront_four;
            }
            $user_id = Session::get("user");
            $time=date("Y-m-d",time());
            $v=explode('-',$time);
            $time_second=date("H:i:s",time());
            $vs=explode(':',$time_second);
            $store_pay_num =$v[0].$v[1].$v[2].$vs[0].$vs[1].$vs[2].rand(1000,9999).$user_id;
            $data['store_pay_num'] =$store_pay_num;//订单号
            $data['operation_status'] =0;  //当前可操作（0待审核，1通过，-1拒绝）
            $bool =Db::name("store")->where('user_id',$user_id)->update($data);
            if($bool){
                return ajax_success('更新成功',['store_pay_num'=>$store_pay_num]);
            }else{
                return ajax_error('更新失败',['status'=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:返回店铺信息
     **************************************
     */
    public function return_store_information(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");
            if(!empty($user_id)){
                $data =Db::name('store')
                    ->where('user_id',$user_id)
                    ->find();
                if(!empty($data)){
                    $real_name = db("user")->field('real_name,phone_num,sex')->where('id',$user_id)->find();
                    $data['real_name'] =$real_name['real_name'];
                    $data['phone_num'] =$real_name['phone_num'];
                    $data['sex'] =$real_name['sex'];
                    return ajax_success('成功获取数据',$data);
                }else{
                    return ajax_error('获取数据失败',['status'=>0]);
                }
            }else{
                return ajax_error('未登录',['status'=>0]);
            }
        }
    }






}