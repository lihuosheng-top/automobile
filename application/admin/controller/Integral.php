<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/10/25
 * Time: 11:44
 */

namespace  app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Integral extends Controller{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 积分中心
     **************************************
     */
    public function index(){
        $integral_data =Db::table('tb_integral')
            ->field("tb_integral.*,tb_user.phone_num phone_num")
            ->join("tb_user","tb_integral.user_id=tb_user.id",'left')
            ->order('tb_integral.operation_time','desc')
            ->paginate(3);
        return view('center',['integral_data'=>$integral_data]);
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 积分详情(User_id)
     **************************************
     */
    public function detail($id){
        $user_data =Db::name('user')->field('user_name,user_integral_wallet,user_integral_wallet_consumed')->where('id',$id)->find();
        $integral_data =Db::table('tb_integral')
            ->field("tb_integral.*,tb_user.phone_num phone_num,tb_user.user_name user_name,tb_user.user_integral_wallet user_integral_wallet")
            ->join("tb_user","tb_integral.user_id=tb_user.id",'left')
            ->where('tb_integral.user_id',$id)
            ->order('tb_integral.operation_time','desc')
            ->paginate(3);
        return view('detail',['integral_data'=>$integral_data,'user_data'=>$user_data]);
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:赠送积分
     **************************************
     * @param Request $request
     */
    public function add(Request $request){
        if($request->isPost()){
            $integral_id =$_POST['integral_id'];
            $add_integral=$_POST['integral'];
            $integral_remarks =$_POST['integral_remarks'];
            if(empty($integral_id) ||empty($add_integral)){
                return ajax_error('请填入积分',['status'=>0]);
            }
            if(empty($integral_remarks)){
                $integral_remarks ='管理员赠送';
            }
            $user_id =Db::name('integral')->field('user_id')->where('integral_id',$integral_id)->find();
            if(empty($user_id )){
                return ajax_error('用户不存在',['status'=>0]);
            }
            /**
             * 用户积分添加
             */
            $user_integral_wallet =Db::name('user')->field('user_integral_wallet')->where('id',$user_id['user_id'])->find();
            $add_data =$user_integral_wallet['user_integral_wallet']+$add_integral;
            $user_add_integral= Db::name("user")->update(['user_integral_wallet'=>$add_data,'id'=>$user_id['user_id']]);
            $user_integral_wallet_update =Db::name('user')->field('user_integral_wallet')->where('id',$user_id['user_id'])->find();
            if(!$user_add_integral){
                return ajax_error('充值不成功',['status'=>0]);
            }
            $integral_arr =[
                'user_id'=>$user_id['user_id'],
                'integral_operation'=>$add_integral,
                'integral_balance'=>$user_integral_wallet_update['user_integral_wallet'],
                'integral_type'=>1,
                'operation_time'=>time(),
                'integral_remarks'=>$integral_remarks
            ];
            $integral_record =Db::name('integral')->insert( $integral_arr);
            if($integral_record){
                return ajax_success('赠送成功',['status'=>1]);
            }else{
                return ajax_error('赠送失败',['status'=>0]);
            }
        }



    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:删除
     **************************************
     * @param $id
     */
    public function del($id){
        $res =Db::name('integral')->where('integral_id',$id)->delete();
        if($res){
            $this->success('删除成功','admin/integral/index');
        }else{
            $this->error('删除失败','admin/integral/index');
        }
    }


}