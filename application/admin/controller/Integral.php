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
            ->select();
        return view('detail',['integral_data'=>$integral_data,'user_data'=>$user_data]);
    }

    public function add(Request $request){
        $integral_id =$request->only(['integral_id'])['integral_id'];
        $add_integral=$request->only(['integral'])['integral'];
        $integral_remarks =$request->only(['integral_remarks'])['integral_remarks'];

    }


}