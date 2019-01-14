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
     **************李火生*******************
     * @param Request $request
     * Notes:店铺首页
     **************************************
     * @return \think\response\View
     */
    public function index(Request $request){
        if($request->isPost()){
            $store_id =$request->only("store_id")["store_id"];
            $store_info =Db::name("store")->where("store_id",$store_id)->field("store_name,store_logo_images")->find();
            $parts_attitude_stars =Db::name("order_parts_evaluate")
                ->where("store_id",$store_id)
                ->avg('service_attitude_stars');
            $service_attitude_stars =Db::name("order_service_evaluate")
                ->where("store_id",$store_id)
                ->avg("service_attitude_stars");
            $sum =(round($parts_attitude_stars,2) + round($service_attitude_stars,2))/2;
            $store_star =round($sum,2);
            $data =[
                "store_info"=>$store_info,
                "store_star"=>$store_star,
            ];
            if(!empty($data)){
                return ajax_success("成功",$data);
            }else{
                return ajax_error("请重新进入",["status"=>0]);
            }
        }
        return view("store_index");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:店铺内的商品（综合）
     **************************************
     */
    public function store_goods_info(Request $request){
        if($request->isPost()){
            $store_id = $request->only(["id"])["id"];
            $goods_data = [];
            $goods = db("goods")
                ->where("store_id",$store_id)
                ->select();
            foreach ($goods as $kye=>$value){
                $where = "`store_is_button` = '1' and `del_status` = '1' and `operation_status` = '1'";
                $store = db("store")->where("store_id",$value["store_id"])->where($where)->find();
                if(!empty($store)){
                    if($value["goods_status"] == 1 && !empty($store)){
                        $special_data[] =db("special")
                            ->where("goods_id",$value["id"])
                            ->select();
                        $statistical_quantity[] =db("order_parts")
                            ->where("goods_id",$value["id"])
                            ->count();
                        unset($goods[$kye]);
                        $goods_data[] = $value;
                    }
                }
            }
            if(!empty($special_data)){
                foreach ($special_data as $k=>$v){
                    $goods_data[$k]["special"] =$v;
                }
            }
            if(!empty($statistical_quantity)){
                foreach ($statistical_quantity as $k=>$v){
                    $goods_data[$k]["statistical_quantity"] =$v;
                }
            }
            if($goods_data){
                return ajax_success("获取成功",$goods_data);
            }else{
                return ajax_error("获取失败");
            }
        }
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
            $is_set_store_name =Db::name("store")->where("store_name",$store_name)->find();
            if(!empty($is_set_store_name)){
                return ajax_success("店铺名已存在",["status"=>0]);
            }
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
                      if(count($explode_data) ==2){
                          $store_detailed_address =$explode_data[0].$explode_data[1].$store_street_address;
                      }
                      if(count($explode_data) ==3){
                          $store_detailed_address =$explode_data[0].$explode_data[1].$explode_data[2].$store_street_address;
                      }
                   db("user")->where('id',$user_id)->update(['real_name'=>$real_name,'phone_num'=>$phone_num,'sex'=>$sex]);
                   $data =[
                       'store_name'=>$store_name,
                       'store_detailed_address'=>$store_detailed_address,//店铺具体地址
                       'store_owner_seat_num'=>$store_owner_seat_num,
                       'store_is_pay'=>0, //是否付费上架（0是没付费，1是付费）
//                       'operation_status'=>0, //平台审核状态（-1为未通过，0为待审核，1为审核通过）
                       'store_logo_images'=>$evaluation_images,
                       'store_do_bussiness_time'=>$store_do_bussiness_time,
                       'service_setting_id'=>$service_setting_id,
                       'store_street_address'=>$store_street_address,
                       'store_city_address'=>$store_city_address,
                       'store_owner_email'=>$store_owner_email,
                       'store_owner_wechat'=>$store_owner_wechat,
                       'store_information'=>$store_information,
                       'user_id'=> $user_id,
                       'role_id'=>$role_id,
                       'is_hot_store'=>-1,
                   ];
                   $bool = db("store")->insertGetId($data);
                   if($bool > 0){
                       $store_data =Db::name('store')->where('store_id',$bool)->find();
                       $departmemt =Db::name("role")->field('name')->where('id',$store_data['role_id'])->find();
                       $user_informations =Db::name('user')->field("phone_num,password,sex,real_name")->where('id',$user_id)->find();
                       $role_datas =[
                           "account"=>$user_informations["phone_num"],
                           "passwd"=>$user_informations["password"],
                           "sex"=>$user_informations["sex"],
                           "stime"=>date('Y-m:d H:i:s'),
                           "role_id"=>$store_data['role_id'],
                           "email"=>$store_data["store_owner_email"],
                           "phone"=>$user_informations["phone_num"],
                           "status"=>0,
                           "department"=>$departmemt['name'],
                           "name"=>$user_informations["real_name"],
                       ];
                       Db::name("admin")->insert($role_datas);
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
            $old_phone_num =Db::name("user")->field('phone_num')->where('id',$user_id)->find();
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
                if(count($explode_data) ==2){
                    $store_detailed_address =$explode_data[0].$explode_data[1].$store_street_address;
                }
                if(count($explode_data) ==3){
                    $store_detailed_address =$explode_data[0].$explode_data[1].$explode_data[2].$store_street_address;
                }
                db("user")->where('id',$user_id)->update(['real_name'=>$real_name,'phone_num'=>$phone_num,'sex'=>$sex]);
                $isset_admin =Db::name('admin')->field('id')->where('phone',$old_phone_num["phone_num"])->find();//判断是否提交过申请
                if(!empty($isset_admin)){
                    if($role_id ==5){
                        db("admin")->where('id',$isset_admin['id'])->update(['name'=>$real_name,'phone'=>$phone_num,'sex'=>$sex,"role_id"=>$role_id,"department"=>"配件商"]);
                    }
                    if($role_id ==13){
                        db("admin")->where('id',$isset_admin['id'])->update(['name'=>$real_name,'phone'=>$phone_num,'sex'=>$sex,"role_id"=>$role_id,"department"=>"服务商"]);
                    }

                }
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
                if(count($explode_data) ==2){
                    $store_detailed_address =$explode_data[0].$explode_data[1].$store_street_address;
                }
                if(count($explode_data) ==3){
                    $store_detailed_address =$explode_data[0].$explode_data[1].$explode_data[2].$store_street_address;
                }
                db("user")->where('id',$user_id)->update(['real_name'=>$real_name,'phone_num'=>$phone_num,'sex'=>$sex]);
                $isset_admin =Db::name('admin')->field('id')->where('phone',$old_phone_num["phone_num"])->find();//判断是否提交过申请
                if(!empty($isset_admin)){
                    if($role_id ==5){
                        db("admin")->where('id',$isset_admin['id'])->update(['name'=>$real_name,'phone'=>$phone_num,'sex'=>$sex,"role_id"=>$role_id,"department"=>"配件商"]);
                    }
                    if($role_id ==13){
                        db("admin")->where('id',$isset_admin['id'])->update(['name'=>$real_name,'phone'=>$phone_num,'sex'=>$sex,"role_id"=>$role_id,"department"=>"服务商"]);
                    }

                }
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
                    'del_status'=>1, //判断店铺是否被伪删除（-1为伪删除，1为正常状态）
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
            $user_id = Session::get("user");
            $store_id =Db::name('store')->field('store_id')->where('user_id',$user_id)->find();
            //身份证正面
            $store_identity_card_file = $request->file('store_identity_card');
            $lnglat =$request->only("lnglat")["lnglat"];
            $real_address =$request->only("address")["address"];
            if(!empty($lnglat)){
                $longitude_data =explode(",",$lnglat);
                $longitude =$longitude_data[0];
                $latitude  = $longitude_data[1];
                $data["longitude"] =$longitude;
                $data["latitude"] =$latitude;
                $data["real_address"] = $real_address;
            }
            if(!empty($store_identity_card_file)){
                $info = $store_identity_card_file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $store_identity_card = str_replace("\\","/",$info->getSaveName());
                $data['store_identity_card'] =$store_identity_card;
                $del_img_url_1 =Db::name("store")->where('user_id',$user_id)->field('store_identity_card')->find();
            }
            //身份证反面
            $store_reverse_images_file = $request->file('store_reverse_images');
            if(!empty($store_reverse_images_file)){
                $info = $store_reverse_images_file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $store_reverse_images = str_replace("\\","/",$info->getSaveName());
                $data['store_reverse_images'] =$store_reverse_images;
                $del_img_url_2= Db::name("store")->where('user_id',$user_id)->field('store_reverse_images')->find();
            }
            //营业执照正面
            $store_do_bussiness_positive_img_file = $request->file('store_do_bussiness_positive_img');
            if(!empty($store_do_bussiness_positive_img_file)){
                $info = $store_do_bussiness_positive_img_file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $store_do_bussiness_positive_img = str_replace("\\","/",$info->getSaveName());
                $data['store_do_bussiness_positive_img'] =$store_do_bussiness_positive_img;
                $del_img_url_3= Db::name("store")->where('user_id',$user_id)->field('store_do_bussiness_positive_img')->find();
            }
            //营业执照反面
            $store_do_bussiness_side_img_file = $request->file('store_do_bussiness_side_img');
            if(!empty($store_do_bussiness_side_img_file)){
                $info = $store_do_bussiness_side_img_file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $store_do_bussiness_side_img= str_replace("\\","/",$info->getSaveName());
                $data['store_do_bussiness_side_img'] =$store_do_bussiness_side_img;
                $del_img_url_4= Db::name("store")->where('user_id',$user_id)->field('store_do_bussiness_side_img')->find();
            }
            //验证实体店面第一张
            $verifying_physical_storefront_one_file = $request->file('verifying_physical_storefront_one');
            if(!empty($verifying_physical_storefront_one_file)){
                $info = $verifying_physical_storefront_one_file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $verifying_physical_storefront_one = str_replace("\\","/",$info->getSaveName());
                $data['verifying_physical_storefront_one'] =$verifying_physical_storefront_one;
                $del_img_url_5= Db::name("store")->where('user_id',$user_id)->field('verifying_physical_storefront_one')->find();
            }
            //验证实体店面第二张
            $verifying_physical_storefront_two = [];
            $verifying_physical_storefront_two_file = $request->file('verifying_physical_storefront_two');
            if(!empty($verifying_physical_storefront_two_file)){
                foreach ($verifying_physical_storefront_two_file as $k=>$v) {
                    $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                    $verifying_physical_storefront_two[] = str_replace("\\", "/", $info->getSaveName());
                }
                $data['verifying_physical_storefront_two'] =implode(',',$verifying_physical_storefront_two);
                $del_img_url_6= Db::name("store")->where('user_id',$user_id)->field('verifying_physical_storefront_two')->find();
            }
            $user_id = Session::get("user");
            $time=date("Y-m-d",time());
            $v=explode('-',$time);
            $time_second=date("H:i:s",time());
            $vs=explode(':',$time_second);
            $store_pay_num =$v[0].$v[1].$v[2].$vs[0].$vs[1].$vs[2].rand(1000,9999).$user_id;
            $data['store_pay_num'] =$store_pay_num;//订单号
            $data['operation_status'] =0;  //当前可操作（0待审核，1通过，-1拒绝）
            $data['store_is_button'] =1;  //（1全部信息填写完毕提交审核，-1未填写完全部信息没有权利提交到后台审核）
            $bool =Db::name("store")->where('user_id',$user_id)->update($data);
            if($bool){
                //删除图片
                if(!empty($del_img_url_1['store_identity_card'])){
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$del_img_url_1['store_identity_card']);
                }
                if(!empty($del_img_url_2['store_reverse_images'])){
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$del_img_url_2['store_reverse_images']);
                }
                if(!empty($del_img_url_3['store_do_bussiness_positive_img'])){
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$del_img_url_3['store_do_bussiness_positive_img']);
                }
                if(!empty($del_img_url_4['store_do_bussiness_side_img'] )){
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$del_img_url_4['store_do_bussiness_side_img']);
                }
                if(!empty($del_img_url_5['verifying_physical_storefront_one'] )){
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$del_img_url_5['verifying_physical_storefront_one']);
                }
                if(!empty($del_img_url_6["verifying_physical_storefront_two"] )){
                    if (strpos($del_img_url_6["verifying_physical_storefront_two"], ',')){
                        $all_img_url =explode(',',$del_img_url_6["verifying_physical_storefront_two"]);
                        foreach ($all_img_url as $ks=>$vs){
                            unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$vs);
                        }
                    }else{
                        unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$del_img_url_6["verifying_physical_storefront_two"]);
                    }
                }
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
                    if(!empty($data['verifying_physical_storefront_two'])){
                        if(strpos($data['verifying_physical_storefront_two'],',')){
                            $ver_img =explode(',',$data['verifying_physical_storefront_two']);
                            $data['imgs']=$ver_img;
                        }else{
                            $data['imgs'][]=$data['verifying_physical_storefront_two'];
                        }
                    }
                    return ajax_success('成功获取数据',$data);
                }else{
                    return ajax_error('获取数据失败',['status'=>0]);
                }
            }else{
                return ajax_error('未登录',['status'=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:多图上传的时候删除图片
     **************************************
     */
    public function url_img_del(Request $request){
        if($request->isPost()){
            $img_url =$request->only('image_del')['image_del'];
            if(!empty($img_url)){
                $user_id = Session::get("user");
                $data =Db::name('store')->field('verifying_physical_storefront_two')->where('user_id',$user_id)->find();
                $datas =explode(',',$data['verifying_physical_storefront_two']);
                foreach ($datas as $k=>$v){
                    if($v==$img_url){
                        unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$v);
                    }else{
                        $new_data[] =$v;
                    }
                }
                if(!empty($new_data)){
                    $new_imgs_url =implode(',',$new_data);
                    $res = Db::name('store')->where('user_id',$user_id)->update(['verifying_physical_storefront_two'=>$new_imgs_url]);
                }else{
                    $res = Db::name('store')->where('user_id',$user_id)->update(['verifying_physical_storefront_two'=>NULL]);
                }
                if($res){
                    return ajax_success('删除成功',['status'=>1]);
                }else{
                    return ajax_success('删除失败',['status'=>0]);
                }

            }
        }
    }

}