<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/10/25
 * Time: 11:35
 */
namespace  app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Recharge extends Controller{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 充值管理首页
     **************************************
     */
    public function index(){
        $reg_data =Db::table("tb_recharge_reflect")
            ->field("tb_recharge_reflect.*,tb_user.user_name tname")
            ->join("tb_user","tb_recharge_reflect.user_id=tb_user.id",'left')
            ->order('tb_recharge_reflect.operation_time','desc')
            ->paginate(20);
        return view('index',['reg_data'=>$reg_data]);
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 充值编辑
     **************************************
     */
    public function edit($id){
        $recharge_data = Db::name("recharge_reflect")->where("id",$id)->find();
        $user_name =Db::name('user')->field('user_name')->where('id',$recharge_data['user_id'])->find();
        return view('edit',['recharge_data'=>$recharge_data,'user_name'=>$user_name['user_name']]);
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:充值提现删除
     **************************************
     * @param $id
     */
    public function del($id){
        $res =Db::name('recharge_reflect')->where('id',$id)->delete();
        if($res){
            $this->success('删除成功','admin/Recharge/index');
        }else{
            $this->error('删除失败','admin/Recharge/index');
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:充值提现批量删除
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
            $list =  Db::name('recharge_reflect')->where($where)->delete();
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
     * Notes:搜索
     **************************************
     * @return \think\response\View
     */
    public function search(){
        $keywords =input('search_key');
        //支付方式
        $pay_type_content =input('pay_type_content');
        //类型(提现-1充值1)
        $operation_type =input('operation_type');
        if(!empty($keywords)){
            if((!empty($pay_type_content))&&(!empty($operation_type))){
                $condition = "  `user_name` like '%{$keywords}%' ";
                $reg_data= Db::table("tb_recharge_reflect")
                    ->field("tb_recharge_reflect.*,tb_user.user_name tname")
                    ->join("tb_user","tb_recharge_reflect.user_id=tb_user.id",'left')
                    ->where($condition)
                    ->where('tb_recharge_reflect.operation_type', $operation_type)
                    ->where('tb_recharge_reflect.pay_type_content',$pay_type_content)
                    ->order('tb_recharge_reflect.operation_time','desc')
                    ->paginate(20 ,false, [
                        'query' => request()->param(),
                    ]);
            }else if((empty($pay_type_content))&&(empty($operation_type))){
                $condition = "  `user_name` like '%{$keywords}%' ";
                $reg_data= Db::table("tb_recharge_reflect")
                    ->field("tb_recharge_reflect.*,tb_user.user_name tname")
                    ->join("tb_user","tb_recharge_reflect.user_id=tb_user.id",'left')
                    ->where($condition)
                    ->order('tb_recharge_reflect.operation_time','desc')
                    ->paginate(20 ,false, [
                        'query' => request()->param(),
                    ]);
            }else if((!empty($pay_type_content))&&(empty($operation_type))) {
                $condition = "  `user_name` like '%{$keywords}%' ";
                $reg_data= Db::table("tb_recharge_reflect")
                    ->field("tb_recharge_reflect.*,tb_user.user_name tname")
                    ->join("tb_user","tb_recharge_reflect.user_id=tb_user.id",'left')
                    ->where($condition)
                    ->where('tb_recharge_reflect.pay_type_content',$pay_type_content)
                    ->order('tb_recharge_reflect.operation_time','desc')
                    ->paginate(20 ,false, [
                        'query' => request()->param(),
                    ]);
            }else{
                $condition = "  `user_name` like '%{$keywords}%' ";
                $reg_data= Db::table("tb_recharge_reflect")
                    ->field("tb_recharge_reflect.*,tb_user.user_name tname")
                    ->join("tb_user","tb_recharge_reflect.user_id=tb_user.id",'left')
                    ->where($condition)
                    ->where('tb_recharge_reflect.operation_type', $operation_type)
                    ->order('tb_recharge_reflect.operation_time','desc')
                    ->paginate(20 ,false, [
                        'query' => request()->param(),
                    ]);
            }

            }
            if(empty($keywords)){
                if((!empty($pay_type_content))&&(!empty($operation_type))){
                    $reg_data= Db::table("tb_recharge_reflect")
                        ->field("tb_recharge_reflect.*,tb_user.user_name tname")
                        ->join("tb_user","tb_recharge_reflect.user_id=tb_user.id",'left')
                        ->where('tb_recharge_reflect.operation_type', $operation_type)
                        ->where('tb_recharge_reflect.pay_type_content',$pay_type_content)
                        ->order('tb_recharge_reflect.operation_time','desc')
                        ->paginate(20 ,false, [
                            'query' => request()->param(),
                        ]);
                }else if((empty($pay_type_content))&&(empty($operation_type))){
                    $reg_data =Db::table("tb_recharge_reflect")
                        ->field("tb_recharge_reflect.*,tb_user.user_name tname")
                        ->join("tb_user","tb_recharge_reflect.user_id=tb_user.id",'left')
                        ->order('tb_recharge_reflect.operation_time','desc')
                        ->paginate(20);
                }else if((!empty($pay_type_content))&&(empty($operation_type))) {
                    $reg_data= Db::table("tb_recharge_reflect")
                        ->field("tb_recharge_reflect.*,tb_user.user_name tname")
                        ->join("tb_user","tb_recharge_reflect.user_id=tb_user.id",'left')
                        ->where('tb_recharge_reflect.pay_type_content',$pay_type_content)
                        ->order('tb_recharge_reflect.operation_time','desc')
                        ->paginate(20 ,false, [
                            'query' => request()->param(),
                        ]);
                }else{
                    $reg_data= Db::table("tb_recharge_reflect")
                        ->field("tb_recharge_reflect.*,tb_user.user_name tname")
                        ->join("tb_user","tb_recharge_reflect.user_id=tb_user.id",'left')
                        ->where('tb_recharge_reflect.operation_type', $operation_type)
                        ->order('tb_recharge_reflect.operation_time','desc')
                        ->paginate(20 ,false, [
                            'query' => request()->param(),
                        ]);
                }

            }
            if(!empty($reg_data)){
                return view('index',['reg_data'=>$reg_data]);
            }
        }

}