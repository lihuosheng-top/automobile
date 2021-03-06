<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/13 0013
 * Time: 14:00
 */
namespace  app\index\controller;


use think\Controller;
use think\Request;
use think\Db;

class  Logistics extends  Controller{

    public function logistics_index(Request $request){
        if($request->isPost()) {
            $id = $request->only(['id'])['id'];
            if (!empty($id)) {
                //配送状态
                $data = db("delivery_order")
                    ->where("order_id", $id)
                    ->field("status,delivery_id")
                    ->find();
                $datas = db("delivery")
                    ->where("id", $data["delivery_id"])
                    ->field("name,number")
                    ->find();
                if (!empty($datas)) {
                    $data["name"] = $datas["name"]; //配送人
                    $data["number"] = $datas["number"]; //配送人电话
                }
                if (!empty($data)) {
                    return ajax_success('订单信息成功返回', $data);
                } else {
                    return ajax_error('该订单没有数据记录');
                }
            } else {
                return ajax_error('沒有订单Id');
            }
        }
        return view("logistics_index");
    }
}