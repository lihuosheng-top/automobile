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
        $user_name =Db::name('user')
            ->field('user_name,real_name,user_wallet')
            ->where('id',$recharge_data['user_id'])
            ->find();
        return view('edit',['recharge_data'=>$recharge_data,'user_name'=>$user_name]);
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

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:提现编辑保存
     **************************************
     */
    public function edit_save(Request $request){
                if($request->ispost()){
                    $id =$request->only("id")["id"];
                    $status =$request->only("status")["status"];
                    $recharge_describe =$request->only("recharge_describe")["recharge_describe"];
                    if($status==2){
                        $this->error("请选择审核或者不通过");
                    }else if($status==1){
                        $data =[
                            "recharge_describe"=>$recharge_describe,
                            "status"=>$status,
                            "money_status"=>1
                        ];
                        $res =Db::name("recharge_reflect")->where("id",$id)->update($data);
                        if($res){
                            $this->success("审核成功","admin/Recharge/index");
                        }else{
                            $this->error("审核失败");
                        }
                    }else if($status==-1){
                        //拒绝则退回申请的金额到余额里面
                        $data =[
                            "recharge_describe"=>$recharge_describe,
                            "status"=>$status,
                            "money_status"=>2
                        ];
                        $res =Db::name("recharge_reflect")->where("id",$id)->update($data);
                        if($res){

                            $money =Db::name("recharge_reflect")->where("id",$id)->find();
                            $time=date("Y-m-d",time());
                            $v=explode('-',$time);
                            $time_second=date("H:i:s",time());
                            $vs=explode(':',$time_second);
                            $parts_order_number =$v[0].$v[1].$v[2].$vs[0].$vs[1].$vs[2].rand(1000,9999).$money["user_id"]; //订单编号
                            //用户钱包退回资金
                            $old_wallet =Db::name("user")->where("id",$money["user_id"])->value("user_wallet");
                            $user_data=[
                                "user_wallet"=>$old_wallet + $money["operation_amount"],
                            ];
                            Db::name("user")->where("id",$money["user_id"])->update($user_data);
                            //进行消费记录
                            $wallet_data =[
                                "user_id"=>$money["user_id"],
                                "wallet_operation"=> $money["operation_amount"],//消费金额
                                "wallet_type"=>1, //消费类型（1获得，-1消费）
                                "operation_time"=>date("Y-m-d H:i:s"),//操作时间
                                "wallet_remarks"=>"提现未通过退回".$money["operation_amount"]."元",
                                "wallet_img"=>"index/image/back.png",
                                "title"=>"提现退回金额",
                                "order_nums"=>$parts_order_number,//订单编号
                                "pay_type"=>"平台退回", //支付宝微信支付
                            ];
                            Db::name("wallet")->insert($wallet_data);
                            $this->success("审核成功","admin/Recharge/index");
                        }else{
                            $this->error("审核失败");
                        }
                    }

                }
        }

}