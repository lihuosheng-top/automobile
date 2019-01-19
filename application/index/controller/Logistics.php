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
                $data = Db::name('order_parts')->where('id', $id)->select();
                foreach ($data as $key => $value) {
                    $data[$key]["delivery_status"] = db("delivery_order")
                        ->where("order_id", $value["id"])
                        ->field("status,delivery_id")
                        ->find();
                    $data[$key]["delivery_name_phone"] = db("delivery")
                        ->where("id", $data[$key]["delivery_status"]["delivery_id"])
                        ->find();
                }
                if (!empty($data)) {
                    return ajax_success('订单信息成功返回', $data);
                } else {
                    return ajax_error('该订单没有数据记录', ['status' => 0]);
                }
            } else {
                return ajax_error('沒有订单Id', ['status' => 0]);
            }
        }
        return view("logistics_index");
    }
}