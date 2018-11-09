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
            $all_del =Db::name('recharge_reflect')->where('operation_type',"-1")->where('user_id',$val['id'])->sum("operation_amount");
            $user_list[$key]['all_reflect']=round($all_del,2);
            /*充值*/
            $all_add=Db::name('recharge_reflect')->where('operation_type',"1")->where('user_id',$val['id'])->sum("operation_amount");
            $user_list[$key]['all_recharge'] =round($all_add,2);
        }

        $all_idents =$user_list ;//这里是需要分页的数据
        $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
        $listRow = 1;//每页2行记录
        $showdata = array_slice($all_idents, ($curPage - 1)*$listRow, $listRow,true);// 数组中根据条件取出一段值，并返回

        $p = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path'     => url('/admin/addons/index'),//这里根据需要修改url
            'query'    => [],
            'fragment' => '',
        ]);
        $p->appends($_GET);
        $this->assign('list', $p);
        $this->assign('listpage', $p->render());
        return view('index',['user_list'=>$user_list]);
    }



}