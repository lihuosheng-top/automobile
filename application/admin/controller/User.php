<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/10/25
 * Time: 11:22
 */
namespace  app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class User extends Controller{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员首页
     **************************************
     */
    public function index(){
        $user_data =Db::name('user')->order('create_time','desc')->paginate(3);
        return view('index',['user_data'=>$user_data]);
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:后台会员账户状态修改
     **************************************
     */
    public function  status(Request $request){
        if($request->isPost()) {
            $status = $request->only(["status"])["status"];
            if($status == 0) {
                $id = $request->only(["id"])["id"];
                $bool = Db::name("user")->where("id", $id)->update(["status" => 0]);
                if ($bool) {
                    return ajax_success('修改成功',['status'=>1]);
                } else {
                    return ajax_error('修改失败',['status'=>0]);
                }
            }
            if($status == 1){
                $id = $request->only(["id"])["id"];
                $bool = Db::name("user")->where("id", $id)->update(["status" => 1]);
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
     * @return \think\response\View
     * 会员编辑
     **************************************
     */
    public function edit($id){
        $user_data = Db::name("user")->where("id",$id)->select();
        $address_data =Db::name("user_address")->where("user_id",$id)->select();//地址
        if(!empty($address_data)){
            foreach ($address_data as $k=>$v){
                $address_data[$k]['id'] =$v['id'];
                $address_data[$k]['user_id'] =$v['user_id'];
                $address_data[$k]['address_name'] =str_replace(',','',$v['address_name']);
                $address_data[$k]['status'] =$v['status'];
                $address_data[$k]['harvester'] =$v['harvester'];
                $address_data[$k]['harvester_phone_num'] =$v['harvester_phone_num'];
                $address_data[$k]['harvester_real_address'] =$v['harvester_real_address'];
        }
        }
        //我的爱车
        if($user_data[0]['type'] ==3){
            $love_car =Db::name("user_car")->where('user_id',$id)->select();
            if(!empty($love_car)){
                foreach ($love_car as $ks=>$vs){
                    $love_car[$ks]['id'] =$vs['id'];
                    $love_car[$ks]['user_car_massage'] =Db::name("user_car_message")->where('user_car_id',$vs['id'])->find();
                    $love_car[$ks]['brand'] =$vs['brand']; //汽车品牌
                    $love_car[$ks]['series'] =$vs['series'];//系列
                    $love_car[$ks]['displacement'] =$vs['displacement'];//排量
                    $love_car[$ks]['production_time'] =$vs['production_time']; //生产时间
                    $love_car[$ks]['status'] =$vs['status']; //设为默认(1默认，0不是默认)
                }
            }
        }
        return view('edit',['user_data'=>$user_data,'address_data'=>$address_data,'love_car'=>$love_car]);
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:会员删除操作
     **************************************
     * @param $id
     */
    public function del($id){
        $res =Db::name('user')->where('id',$id)->delete();
        if($res){
           $this->success('删除成功','admin/User/index');
        }else{
            $this->error('删除失败','admin/User/index');
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
                $where ='id in('.implode(',',$id).')';
            }else{
                $where ='id='.$id;
            }
            $list =  Db::name('user')->where($where)->delete();
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
     * Notes:会员搜索
     **************************************
     * @param Request $request
     */
    public function search(){
        $keywords =input('search_key');
        $keyword =input('search_keys');
        $timemin  =strtotime(input("date_min"));
        /*添加一天（23：59：59）*/
        $time_max_data =strtotime(input('date_max'));
        $t=date('Y-m-d H:i:s',$time_max_data+1*24*60*60);
        $timemax  =strtotime($t);
        if(empty($keywords)){
            $keywords=$keyword;
            if(empty($keywords)){
                if((!empty($timemin))&&(empty($time_max_data))){

                    $time_condition  = "create_time>{$timemin}";
                $user_data=Db::name("user")
                    ->where($time_condition)
                    ->order('create_time','desc')
                    ->paginate(3 ,false, [
                        'query' => request()->param(),
                    ]);
                }else if((empty($timemin))&&(!empty($time_max_data))){

                    $time_condition  = "create_time< {$timemax}";
                    $user_data=Db::name("user")
                        ->where($time_condition)
                        ->order('create_time','desc')
                        ->paginate(3 ,false, [
                            'query' => request()->param(),
                        ]);
                }else if((!empty($timemin))&&(!empty($time_max_data))){

                    $time_condition  = "create_time>{$timemin} and create_time< {$timemax}";
                    $user_data=Db::name("user")
                        ->where($time_condition)
                        ->order('create_time','desc')
                        ->paginate(3 ,false, [
                            'query' => request()->param(),
                        ]);
                }else {
                    $user_data=Db::name("user")
                        ->order('create_time','desc')
                        ->paginate(3 ,false, [
                            'query' => request()->param(),
                        ]);
                }

            }
        }
        if(!empty($keywords)){
            $condition = " `phone_num` like '%{$keywords}%' or `user_name` like '%{$keywords}%' ";
            if((!empty($timemin))&&(!empty($time_max_data))){
                $time_condition  = "create_time>{$timemin} and create_time< {$timemax}";
                $user_data=Db::name("user")
                    ->where($condition)
                    ->where($time_condition)
                    ->order('create_time','desc')
                    ->paginate(3 ,false, [
                        'query' => request()->param(),
                    ]);
            }else if((!empty($timemin))&&(empty($time_max_data)) ||(empty($timemin))&&(!empty($time_max_data)) ||(empty($timemin))&&(empty($time_max_data))){
                $user_data=Db::name("user")
                    ->where($condition)
                    ->order('create_time','desc')
                    ->paginate(3 ,false, [
                        'query' => request()->param(),
                    ]);
            }

        }
        if(!empty($user_data)){
            return view('index',['user_data'=>$user_data]);
        }
    }



    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员等级
     **************************************
     */
    public function  grade(){
        $user_grade_data =Db::name('user_grade')->order('grade_id','asc')->select();
        return view('grade',['user_grade_data'=>$user_grade_data]);
    }

}