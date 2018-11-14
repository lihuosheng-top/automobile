<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/22
 * Time: 11:25
 */
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Install extends Controller{

    /**
     * 价格调整设置
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index(){

        return view("install_index");

    }



    /**
     * 推荐奖励积分设置
     * 陈绪
     */
    public function recommend(){

        return view("recommend_index");

    }



    /**
     **************李火生*******************
     * @param Request $request
     * Notes:积分折扣设置
     **************************************
     * @return \think\response\View
     */
    public function integral(){
        $integral_data =Db::name('integral_discount_settings')->select();
        return view("integral_index",['integral_data'=>$integral_data]);

    }



    /**
     * 上架年限设置
     * 陈绪
     */
    public function putaway(){

        return view("putaway_index");

    }



    /**
     * 充值设置
     * 陈绪
     */
    public function recharge(){

        return view("recharge_index");

    }


    /**
     * 服务显示
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function service_index(){

        return view("service_index");

    }




    /**
     * 服务添加
     * 陈绪
     */
    public function service_add(){

        return view("service_add");

    }




    /**
     * 服务入库
     * 陈绪
     */
    public function service_save(){



    }



    /**
     * 服务编辑
     * 陈绪
     */
    public function service_edit(){

        return view("service_edit");

    }




    /**
     * 服务更新
     * 陈绪
     */
    public function service_updata(){



    }



    /**
     * 服务删除
     * 陈绪
     */
    public function service_del(){



    }



    /**
     * 消息显示
     * 陈绪
     */
    public function message_index(){

        return view("message_index");

    }



    /**
     * 消息添加
     * 陈绪
     */
    public function message_save(){



    }



    /**
     * 消息删除
     * 陈绪
     */
    public function message_del(){



    }

}