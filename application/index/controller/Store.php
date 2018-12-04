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
            $input_data = $_POST;
            $store_name =trim($input_data['store_name']);
            $real_name =trim($input_data['real_name']);
            $phone_num =trim($input_data['phone_num']);
            $store_owner_seat_num =trim($input_data['store_owner_seat_num']);
            $sex =trim($input_data['sex']);
//            $store_logo_images =trim($input_data['store_logo_images']);//图片
            $store_do_bussiness_time =trim($input_data['store_do_bussiness_time']); //营业时间
            $service_setting_id =trim($input_data['service_setting_id']);
            $store_city_address =trim($input_data['tore_city_address']);
            $store_street_address =trim($input_data['store_street_address']);
            $store_information =trim($input_data['store_information']);
            $store_owner_email =trim($input_data['store_owner_email']);
            $store_owner_wechat =trim($input_data['store_owner_wechat']);
            $role_id =trim($input_data['role_id']);
               $file = $request->file('store_logo_images');
               if(!empty($file)){
                       $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                       $store_img_url = str_replace("\\","/",$info->getSaveName());
                       $evaluation_images = ["store_img_url"=>$store_img_url];
                       $ex_address =explode(',',$store_city_address);
                       foreach ($ex_address as $k=>$v){
                           $explode_data[] =$v;
                       }
                    $store_detailed_address =$explode_data[0].$explode_data[1].$explode_data[2].$store_street_address;
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
                       return ajax_success('添加成功',$bool);
                   }else{
                       return ajax_error('添加失败',['status'=>0]);
                   }
               }else{
                   return ajax_error('请上传店铺logo',['status'=>0]);
               }
           }
    }




}