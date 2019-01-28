<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/10/25
 * Time: 11:37
 *
 */

namespace  app\admin\controller;

use think\Controller;
use think\Db;
use think\paginator\driver\Bootstrap;

class Capital extends Controller{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 资金管理首页
     **************************************
     */
    public function index(){
        $user_list = Db::name("user") ->select();
        foreach ($user_list as $key=>$val){
            /*提现*/
            $all_del =Db::name('recharge_reflect')
                ->where('operation_type',"-1")
                ->where('user_id',$val['id'])
                ->where("status",1)
                ->sum("operation_amount");
            $user_list[$key]['all_reflect']=round($all_del,2);
            /*充值*/
            $all_add=Db::name('recharge_reflect')
                ->where('operation_type',"1")
                ->where('user_id',$val['id'])
                ->sum("operation_amount");
            $user_list[$key]['all_recharge'] =round($all_add,2);
            /*账户余额（商家+车主）*/
            $arr_condition ="`status` = '1' and `is_deduction` = '1' and `user_id` = ".$val["id"];
            $money =Db::name("business_wallet")
                ->where($arr_condition)
                ->sum("money");
            if(empty($money)){
                $user_list[$key]['all_balance'] =$val["user_wallet"];
            }else{
                $user_list[$key]['all_balance'] =round($val["user_wallet"] + $money,2);
            }
        }
        $all_idents =$user_list ;//这里是需要分页的数据
        $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
        $listRow = 20;//每页3行记录
        $showdata = array_slice($all_idents, ($curPage - 1)*$listRow, $listRow,true);// 数组中根据条件取出一段值，并返回
        $user_list = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path'     => url('admin/Capital/index'),//这里根据需要修改url
            'query'    =>  [],
            'fragment' => '',
        ]);
        $user_list->appends($_GET);
        $this->assign('listpage', $user_list->render());
        return view('index',['user_list'=>$user_list]);
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:搜索功能
     **************************************
     * @return \think\response\View
     */
    public function search(){
        $keywords =input('search_key');
        $timemin  =strtotime(input('date_min'));
        /*添加一天（23：59：59）*/
        $time_max_data =strtotime(input('date_max'));
        $t=date('Y-m-d H:i:s',$time_max_data+1*24*60*60);
        $timemax  = strtotime($t);
        if(empty($keywords)){
                if((!empty($timemin))&&(empty($timemax))){
                    $time_condition  = "create_time>{$timemin}";
                    $user_list = Db::name("user")->where($time_condition) ->select();
                    foreach ($user_list as $key=>$val){
                        /*提现*/
                        $all_del =Db::name('recharge_reflect')->where('operation_type',"-1")->where('user_id',$val['id'])->sum("operation_amount");
                        $user_list[$key]['all_reflect']=round($all_del,2);
                        /*充值*/
                        $all_add=Db::name('recharge_reflect')->where('operation_type',"1")->where('user_id',$val['id'])->sum("operation_amount");
                        $user_list[$key]['all_recharge'] =round($all_add,2);
                        /*账户余额（商家+车主）*/
                        $arr_condition ="`status` = '1' and `is_deduction` = '1' and `user_id` = ".$val["id"];
                        $money =Db::name("business_wallet")
                            ->where($arr_condition)
                            ->sum("money");
                        if(empty($money)){
                            $user_list[$key]['all_balance'] =$val["user_wallet"];
                        }else{
                            $user_list[$key]['all_balance'] =round($val["user_wallet"] + $money,2);
                        }
                    }
                    $all_idents =$user_list ;//这里是需要分页的数据
                    $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
                    $listRow = 20;//每页3行记录
                    $showdata = array_slice($all_idents, ($curPage - 1)*$listRow, $listRow,true);// 数组中根据条件取出一段值，并返回
                    $user_list = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
                        'var_page' => 'page',
                        'path'     => url('admin/Capital/search'),//这里根据需要修改url
                        'query'    =>  request()->param(),
                        'fragment' => '',
                    ]);
                    $user_list->appends($_GET);
                }else if((empty($timemin))&&(!empty($timemax))){
                    $time_condition  = "create_time< {$timemax}";
                    $user_list = Db::name("user")->where($time_condition)->select();
                    foreach ($user_list as $key=>$val){
                        /*提现*/
                        $all_del =Db::name('recharge_reflect')->where('operation_type',"-1")->where('user_id',$val['id'])->sum("operation_amount");
                        $user_list[$key]['all_reflect']=round($all_del,2);
                        /*充值*/
                        $all_add=Db::name('recharge_reflect')->where('operation_type',"1")->where('user_id',$val['id'])->sum("operation_amount");
                        $user_list[$key]['all_recharge'] =round($all_add,2);
                        /*账户余额（商家+车主）*/
                        $arr_condition ="`status` = '1' and `is_deduction` = '1' and `user_id` = ".$val["id"];
                        $money =Db::name("business_wallet")
                            ->where($arr_condition)
                            ->sum("money");
                        if(empty($money)){
                            $user_list[$key]['all_balance'] =$val["user_wallet"];
                        }else{
                            $user_list[$key]['all_balance'] =round($val["user_wallet"] + $money,2);
                        }
                    }
                    $all_idents =$user_list ;//这里是需要分页的数据
                    $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
                    $listRow = 20;//每页3行记录
                    $showdata = array_slice($all_idents, ($curPage - 1)*$listRow, $listRow,true);// 数组中根据条件取出一段值，并返回
                    $user_list = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
                        'var_page' => 'page',
                        'path'     => url('admin/Capital/search'),//这里根据需要修改url
                        'query'    =>  request()->param(),
                        'fragment' => '',
                    ]);
                    $user_list->appends($_GET);

                }else if((!empty($timemin))&&(!empty($timemax))){
                    $time_condition  = "create_time>{$timemin} and create_time< {$timemax}";
                    $user_list = Db::name("user")->where($time_condition)->select();
                    foreach ($user_list as $key=>$val){
                        /*提现*/
                        $all_del =Db::name('recharge_reflect')->where('operation_type',"-1")->where('user_id',$val['id'])->sum("operation_amount");
                        $user_list[$key]['all_reflect']=round($all_del,2);
                        /*充值*/
                        $all_add=Db::name('recharge_reflect')->where('operation_type',"1")->where('user_id',$val['id'])->sum("operation_amount");
                        $user_list[$key]['all_recharge'] =round($all_add,2);
                        /*账户余额（商家+车主）*/
                        $arr_condition ="`status` = '1' and `is_deduction` = '1' and `user_id` = ".$val["id"];
                        $money =Db::name("business_wallet")
                            ->where($arr_condition)
                            ->sum("money");
                        if(empty($money)){
                            $user_list[$key]['all_balance'] =$val["user_wallet"];
                        }else{
                            $user_list[$key]['all_balance'] =round($val["user_wallet"] + $money,2);
                        }
                    }
                    $all_idents =$user_list ;//这里是需要分页的数据
                    $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
                    $listRow = 20;//每页3行记录
                    $showdata = array_slice($all_idents, ($curPage - 1)*$listRow, $listRow,true);// 数组中根据条件取出一段值，并返回
                    $user_list = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
                        'var_page' => 'page',
                        'path'     => url('admin/Capital/search'),//这里根据需要修改url
                        'query'    =>  request()->param(),
                        'fragment' => '',
                    ]);
                    $user_list->appends($_GET);

                }else {
                    $user_list = Db::name("user") ->select();
                    foreach ($user_list as $key=>$val){
                        /*提现*/
                        $all_del =Db::name('recharge_reflect')->where('operation_type',"-1")->where('user_id',$val['id'])->sum("operation_amount");
                        $user_list[$key]['all_reflect']=round($all_del,2);
                        /*充值*/
                        $all_add=Db::name('recharge_reflect')->where('operation_type',"1")->where('user_id',$val['id'])->sum("operation_amount");
                        $user_list[$key]['all_recharge'] =round($all_add,2);
                        /*账户余额（商家+车主）*/
                        $arr_condition ="`status` = '1' and `is_deduction` = '1' and `user_id` = ".$val["id"];
                        $money =Db::name("business_wallet")
                            ->where($arr_condition)
                            ->sum("money");
                        if(empty($money)){
                            $user_list[$key]['all_balance'] =$val["user_wallet"];
                        }else{
                            $user_list[$key]['all_balance'] =round($val["user_wallet"] + $money,2);
                        }
                    }
                    $all_idents =$user_list ;//这里是需要分页的数据
                    $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
                    $listRow = 20;//每页3行记录
                    $showdata = array_slice($all_idents, ($curPage - 1)*$listRow, $listRow,true);// 数组中根据条件取出一段值，并返回
                    $user_list = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
                        'var_page' => 'page',
                        'path'     => url('admin/Capital/search'),//这里根据需要修改url
                        'query'    =>  request()->param(),
                        'fragment' => '',
                    ]);
                    $user_list->appends($_GET);
                }

            }

        if(!empty($keywords)){
            $condition = " `phone_num` like '%{$keywords}%' or `user_name` like '%{$keywords}%' ";
            if((!empty($timemin))&&(!empty($timemax))){
                $time_condition  = "create_time>{$timemin} and create_time< {$timemax}";
                $user_list = Db::name("user")->where($condition)->where($time_condition)->select();
                foreach ($user_list as $key=>$val){
                    /*提现*/
                    $all_del =Db::name('recharge_reflect')->where('operation_type',"-1")->where('user_id',$val['id'])->sum("operation_amount");
                    $user_list[$key]['all_reflect']=round($all_del,2);
                    /*充值*/
                    $all_add=Db::name('recharge_reflect')->where('operation_type',"1")->where('user_id',$val['id'])->sum("operation_amount");
                    $user_list[$key]['all_recharge'] =round($all_add,2);
                    /*账户余额（商家+车主）*/
                    $arr_condition ="`status` = '1' and `is_deduction` = '1' and `user_id` = ".$val["id"];
                    $money =Db::name("business_wallet")
                        ->where($arr_condition)
                        ->sum("money");
                    if(empty($money)){
                        $user_list[$key]['all_balance'] =$val["user_wallet"];
                    }else{
                        $user_list[$key]['all_balance'] =round($val["user_wallet"] + $money,2);
                    }
                }
                $all_idents =$user_list ;//这里是需要分页的数据
                $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
                $listRow = 20;//每页3行记录
                $showdata = array_slice($all_idents, ($curPage - 1)*$listRow, $listRow,true);// 数组中根据条件取出一段值，并返回
                $user_list = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
                    'var_page' => 'page',
                    'path'     => url('admin/Capital/search'),//这里根据需要修改url
                    'query'    =>  request()->param(),
                    'fragment' => '',
                ]);
                $user_list->appends($_GET);
            }else if((!empty($timemin))&&(empty($timemax)) ||(empty($timemin))&&(!empty($timemax)) ||(empty($timemin))&&(empty($timemax))){
                $user_list = Db::name("user")->where($condition)->select();
                foreach ($user_list as $key=>$val){
                    /*提现*/
                    $all_del =Db::name('recharge_reflect')->where('operation_type',"-1")->where('user_id',$val['id'])->sum("operation_amount");
                    $user_list[$key]['all_reflect']=round($all_del,2);
                    /*充值*/
                    $all_add=Db::name('recharge_reflect')->where('operation_type',"1")->where('user_id',$val['id'])->sum("operation_amount");
                    $user_list[$key]['all_recharge'] =round($all_add,2);
                    /*账户余额（商家+车主）*/
                    $arr_condition ="`status` = '1' and `is_deduction` = '1' and `user_id` = ".$val["id"];
                    $money =Db::name("business_wallet")
                        ->where($arr_condition)
                        ->sum("money");
                    if(empty($money)){
                        $user_list[$key]['all_balance'] =$val["user_wallet"];
                    }else{
                        $user_list[$key]['all_balance'] =round($val["user_wallet"] + $money,2);
                    }
                }
                $all_idents =$user_list ;//这里是需要分页的数据
                $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
                $listRow = 20;//每页3行记录
                $showdata = array_slice($all_idents, ($curPage - 1)*$listRow, $listRow,true);// 数组中根据条件取出一段值，并返回
                $user_list = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
                    'var_page' => 'page',
                    'path'     => url('admin/Capital/search'),//这里根据需要修改url
                    'query'    =>  request()->param(),
                    'fragment' => '',
                ]);
                $user_list->appends($_GET);
            }

        }
        if(!empty($user_list)){
            return view('index',['user_list'=>$user_list]);
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:详情
     * 参数：id用户的id
     **************************************
     */
    public function detail($id){
            //用户的id
            $user_list = Db::name("user")->where("id",$id) ->find();
            /*提现*/
            $all_del =Db::name('recharge_reflect')
                ->where('operation_type',"-1")
                ->where("status",1)
                ->where('user_id',$id)
                ->sum("operation_amount");
            $user_list['all_reflect']=round($all_del,2);
            /*充值*/
            $all_add=Db::name('recharge_reflect')
                ->where('operation_type',"1")
                ->where("status",1)
                ->where('user_id',$id)
                ->sum("operation_amount");
            $user_list['all_recharge'] =round($all_add,2);

            $wallet_data =Db::table('tb_wallet')
                ->field("tb_wallet.*,tb_user.phone_num phone_num,tb_user.user_name user_name,tb_user.user_wallet user_wallet")
                ->join("tb_user","tb_wallet.user_id=tb_user.id",'left')
                ->where('tb_wallet.user_id',$id)
                ->order('tb_wallet.operation_time','desc')
                ->paginate(20);

            return view("detail",['user_list'=>$user_list,"wallet_data"=>$wallet_data]);

    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:资金详情
     **************************************
     */
    public function edit(){
        $wallet_data =Db::table('tb_wallet')
            ->field("tb_wallet.*,tb_user.phone_num phone_num,tb_user.user_name user_name,tb_user.user_wallet user_wallet ,tb_user.id user_id")
            ->join("tb_user","tb_wallet.user_id=tb_user.id",'left')
            ->order('tb_wallet.operation_time','desc')
            ->paginate(20);
        return view("edit",["user_list"=>null,"wallet_data"=>$wallet_data]);
    }

}