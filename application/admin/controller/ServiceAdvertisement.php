<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/10/26
 * Time: 19:17
 */
namespace  app\admin\controller;

use think\Controller;

class  ServiceAdvertisement extends  Controller{


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商广告
     **************************************
     * @return \think\response\View
     */
    public function Service_business_advertising(){
        return view('Service_business_advertising');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商广告添加
     **************************************
     * @return \think\response\View
     */
    public function Service_business_add(){
        return view('Service_business_add');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商广告删除
     **************************************
     * @return \think\response\View
     */
    public function Service_business_edit(){
        return view('Service_business_edit');
    }


}