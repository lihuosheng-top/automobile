<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4 0004
 * Time: 11:35
 */
namespace app\index\controller;
use think\Controller;

class  Member extends Controller{
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:会员权益
     **************************************
     * @return \think\response\View
     */
    public function member_equity(){
        return view('member_equity');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:地址管理
     **************************************
     * @return \think\response\View
     */
    public function member_address(){
        return view('member_address');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:地址管理添加
     **************************************
     * @return \think\response\View
     */
    public function member_address_add(){
        return view('member_address_add');
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:我的收藏
     **************************************
     * @return \think\response\View
     */
    public function member_collection(){
        return view('member_collection');
    }
}