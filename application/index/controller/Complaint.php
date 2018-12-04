<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4 0004
 * Time: 12:03
 */

namespace app\index\controller;
use think\Controller;

class Complaint extends Controller{

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:投诉中心
     **************************************
     * @return \think\response\View
     */
    public function index(){
        return view('index');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:投诉详情
     **************************************
     * @return \think\response\View
     */
    public function detail(){
        return view('detail');
    }
}