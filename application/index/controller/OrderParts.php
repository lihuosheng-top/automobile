<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/26 0026
 * Time: 18:39
 */
namespace app\index\controller;

use think\Controller;
use  think\Request;
use  think\Db;
use think\Session;

class OrderParts extends Controller{
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态全部订单页面
     **************************************
     * @return \think\response\View
     */
    public function order_parts_all(){
        return view('order_parts_all');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单详情页面
     **************************************
     * @return \think\response\View
     */
    public function order_parts_detail(){
        return view('order_parts_detail');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态全部订单接口
     **************************************
     * @param Request $request
     */

    public function   ios_api_order_parts_all(Request $request)
    {
        if ($request->isPost()) {
            $datas = session('member');
            if (!empty($datas)) {
                $member_id = Db::name('user')->field('id')->where('phone_num', $datas['phone_num'])->find();
                if (!empty($datas)) {
                    $data = Db::name('order_parts')
                        ->field('parts_order_number,group_concat(id) order_parts_id')
                        ->where('user_id', $member_id['id'])
                        ->order('order_create_time', 'desc')
                        ->group('parts_order_number')
                        ->select();
                    foreach ($data as $key => $value) {
                        if (strpos($value['order_parts_id'], ',')) {
                            $order_id = explode(',', $value['order_parts_id']);
                            foreach ($order_id as $k => $v) {
                                $return_data[] = Db::name('order_parts')->where('id', $v) ->where('user_id', $member_id['id'])->find();
                            }
                            foreach ($return_data as $ke => $item) {
                                $order_store_id[] = $item['store_id'];
                            }
                            $da_store_id = array_unique($order_store_id); //去重之后的商户
                            foreach ($da_store_id as $da_k => $da_v) {
                                $order_undate['info'][] = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->select();
                                $names = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->find();
                                $order_undate['store_name'][] = $names['store_name'];
                                $order_undate['status'][] = $names['status'];
                            }
                        }
                        else{
                            $return_datas = Db::name('order_parts')->where('id', $value['order_parts_id'])->find();
                            $data_infomation['name'][]= $return_datas['store_name'];
                            $data_infomation['status'][] = $return_datas['status'];
                            $data_infomation['all'][] = Db::name('order_parts')->where('id', $value['order_parts_id'])->find();
                        }
                    };

                    if (!empty($order_undate)) {
                        foreach ($order_undate['info'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr[] =$j;
                            }
                        }
                        foreach ($new_arr as $i=>$j){
                            $end_info[$i]['info'] = $j;
                        }
                        foreach ($order_undate['store_name'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_name[] =$j;
                            }
                        }
                        foreach ($new_arr_name as $i=>$j){
                            $end_info[$i]['store_name'] = $j;
                        }

                        foreach ($order_undate['status'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_status[] = $j;
                            }
                        }
                        foreach ($new_arr_status as $i=>$j){
                            $end_info[$i]['status'] = $j;
                        }
                    }
                    if(!empty($data_infomation)){
                        if(!empty($new_arr)){
                            $coutn =count($new_arr);
                        }else{
                            $coutn =0;
                        }
                        foreach ($data_infomation['name'] as $a=>$b){
                            $end_info[$a+$coutn]['store_name'] = $b;
                        }
                        foreach ($data_infomation['status'] as $a=>$b){
                            $end_info[$a+$coutn]['status'] = $b;
                        }
                        foreach ($data_infomation['all'] as $a=>$b){
                            $end_info[$a+$coutn]['info'][] = $b;
                        }
                    }

                    if (!empty($end_info)) {
                        return ajax_success('数据', $end_info);
                    } else {
                        return ajax_error('没数据');
                    }
                } else {
                    return ajax_error('请登录', ['status' => 0]);
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态待付款页面
     **************************************
     * @return \think\response\View
     */
    public function order_parts_wait_pay(){
        return view('order_parts_wait_pay');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态待付款接口
     **************************************
     * @param Request $request
     */
    public function   ios_api_order_parts_wait_pay(Request $request){
        if ($request->isPost()) {
            $datas = session('member');
            if (!empty($datas)) {
                $member_id = Db::name('user')->field('id')->where('phone_num', $datas['phone_num'])->find();
                if (!empty($datas)) {
                    $data = Db::name('order_parts')
                        ->field('parts_order_number,group_concat(id) order_parts_id')
                        ->where('user_id', $member_id['id'])
                        ->where('status',1)
                        ->order('order_create_time', 'desc')
                        ->group('parts_order_number')
                        ->select();
                    foreach ($data as $key => $value) {
                        if (strpos($value['order_parts_id'], ',')) {
                            $order_id = explode(',', $value['order_parts_id']);
                            foreach ($order_id as $k => $v) {
                                $return_data[] = Db::name('order_parts')->where('id', $v) ->where('user_id', $member_id['id'])->find();
                            }
                            foreach ($return_data as $ke => $item) {
                                $order_store_id[] = $item['store_id'];
                            }
                            $da_store_id = array_unique($order_store_id); //去重之后的商户
                            foreach ($da_store_id as $da_k => $da_v) {
                                $order_undate['info'][] = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->select();
                                $names = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->find();
                                $order_undate['store_name'][] = $names['store_name'];
                                $order_undate['status'][] = $names['status'];
                            }
                        }
                        else{
                            $return_datas = Db::name('order_parts')->where('id', $value['order_parts_id'])->find();
                            $data_infomation['name'][]= $return_datas['store_name'];
                            $data_infomation['status'][] = $return_datas['status'];
                            $data_infomation['all'][] = Db::name('order_parts')->where('id', $value['order_parts_id'])->find();
                        }
                    };

                    if (!empty($order_undate)) {
                        foreach ($order_undate['info'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr[] =$j;
                            }
                        }
                        foreach ($new_arr as $i=>$j){
                            $end_info[$i]['info'] = $j;
                        }
                        foreach ($order_undate['store_name'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_name[] =$j;
                            }
                        }
                        foreach ($new_arr_name as $i=>$j){
                            $end_info[$i]['store_name'] = $j;
                        }

                        foreach ($order_undate['status'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_status[] = $j;
                            }
                        }
                        foreach ($new_arr_status as $i=>$j){
                            $end_info[$i]['status'] = $j;
                        }
                    }
                    if(!empty($data_infomation)){
                        if(!empty($new_arr)){
                            $coutn =count($new_arr);
                        }else{
                            $coutn =0;
                        }
                        foreach ($data_infomation['name'] as $a=>$b){
                            $end_info[$a+$coutn]['store_name'] = $b;
                        }
                        foreach ($data_infomation['status'] as $a=>$b){
                            $end_info[$a+$coutn]['status'] = $b;
                        }
                        foreach ($data_infomation['all'] as $a=>$b){
                            $end_info[$a+$coutn]['info'][] = $b;
                        }
                    }

                    if (!empty($end_info)) {
                        return ajax_success('数据', $end_info);
                    } else {
                        return ajax_error('没数据');
                    }
                } else {
                    return ajax_error('请登录', ['status' => 0]);
                }
            }
        }

    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态待收货
     **************************************
     * @return \think\response\View
     */
    public function order_wait_deliver(){
        return view('order_wait_deliver');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态待收货接口
     **************************************
     * @param Request $request
     */
    public function   ios_api_order_wait_deliver(Request $request){
        if ($request->isPost()) {
            $datas = session('member');
            if (!empty($datas)) {
                $member_id = Db::name('user')->field('id')->where('phone_num', $datas['phone_num'])->find();
                if (!empty($datas)) {
                    $condition ="`status` = '2' or `status` = '3' or `status` = '4' or `status` = '5' ";
                    $data = Db::name('order_parts')
                        ->field('parts_order_number,group_concat(id) order_parts_id')
                        ->where('user_id', $member_id['id'])
                        ->where($condition)
                        ->order('order_create_time', 'desc')
                        ->group('parts_order_number')
                        ->select();
                    foreach ($data as $key => $value) {
                        if (strpos($value['order_parts_id'], ',')) {
                            $order_id = explode(',', $value['order_parts_id']);
                            foreach ($order_id as $k => $v) {
                                $return_data[] = Db::name('order_parts')->where('id', $v) ->where('user_id', $member_id['id'])->find();
                            }
                            foreach ($return_data as $ke => $item) {
                                $order_store_id[] = $item['store_id'];
                            }
                            $da_store_id = array_unique($order_store_id); //去重之后的商户
                            foreach ($da_store_id as $da_k => $da_v) {
                                $order_undate['info'][] = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->select();
                                $names = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->find();
                                $order_undate['store_name'][] = $names['store_name'];
                                $order_undate['status'][] = $names['status'];
                            }
                        }
                        else{
                            $return_datas = Db::name('order_parts')->where('id', $value['order_parts_id'])->find();
                            $data_infomation['name'][]= $return_datas['store_name'];
                            $data_infomation['status'][] = $return_datas['status'];
                            $data_infomation['all'][] = Db::name('order_parts')->where('id', $value['order_parts_id'])->find();
                        }
                    };

                    if (!empty($order_undate)) {
                        foreach ($order_undate['info'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr[] =$j;
                            }
                        }
                        foreach ($new_arr as $i=>$j){
                            $end_info[$i]['info'] = $j;
                        }
                        foreach ($order_undate['store_name'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_name[] =$j;
                            }
                        }
                        foreach ($new_arr_name as $i=>$j){
                            $end_info[$i]['store_name'] = $j;
                        }

                        foreach ($order_undate['status'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_status[] = $j;
                            }
                        }
                        foreach ($new_arr_status as $i=>$j){
                            $end_info[$i]['status'] = $j;
                        }
                    }
                    if(!empty($data_infomation)){
                        if(!empty($new_arr)){
                            $coutn =count($new_arr);
                        }else{
                            $coutn =0;
                        }
                        foreach ($data_infomation['name'] as $a=>$b){
                            $end_info[$a+$coutn]['store_name'] = $b;
                        }
                        foreach ($data_infomation['status'] as $a=>$b){
                            $end_info[$a+$coutn]['status'] = $b;
                        }
                        foreach ($data_infomation['all'] as $a=>$b){
                            $end_info[$a+$coutn]['info'][] = $b;
                        }
                    }

                    if (!empty($end_info)) {
                        return ajax_success('数据', $end_info);
                    } else {
                        return ajax_error('没数据');
                    }
                } else {
                    return ajax_error('请登录', ['status' => 0]);
                }
            }
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态待评价页面
     **************************************
     * @return \think\response\View
     */
    public function order_wait_evaluate(){
        return view('order_wait_evaluate');
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态待评价接口
     **************************************
     * @param Request $request
     */
    public function   ios_api_order_wait_evaluate(Request $request){
        if ($request->isPost()) {
            $datas = session('member');
            if (!empty($datas)) {
                $member_id = Db::name('user')->field('id')->where('phone_num', $datas['phone_num'])->find();
                if (!empty($datas)) {
                    $condition ="`status` = '6' or `status` = '7'";
                    $data = Db::name('order_parts')
                        ->field('parts_order_number,group_concat(id) order_parts_id')
                        ->where('user_id', $member_id['id'])
                        ->where($condition)
                        ->order('order_create_time', 'desc')
                        ->group('parts_order_number')
                        ->select();
                    foreach ($data as $key => $value) {
                        if (strpos($value['order_parts_id'], ',')) {
                            $order_id = explode(',', $value['order_parts_id']);
                            foreach ($order_id as $k => $v) {
                                $return_data[] = Db::name('order_parts')->where('id', $v) ->where('user_id', $member_id['id'])->find();
                            }
                            foreach ($return_data as $ke => $item) {
                                $order_store_id[] = $item['store_id'];
                            }
                            $da_store_id = array_unique($order_store_id); //去重之后的商户
                            foreach ($da_store_id as $da_k => $da_v) {
                                $order_undate['info'][] = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->select();
                                $names = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->find();
                                $order_undate['store_name'][] = $names['store_name'];
                                $order_undate['status'][] = $names['status'];
                            }
                        }
                        else{
                            $return_datas = Db::name('order_parts')->where('id', $value['order_parts_id'])->find();
                            $data_infomation['name'][]= $return_datas['store_name'];
                            $data_infomation['status'][] = $return_datas['status'];
                            $data_infomation['all'][] = Db::name('order_parts')->where('id', $value['order_parts_id'])->find();
                        }
                    };

                    if (!empty($order_undate)) {
                        foreach ($order_undate['info'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr[] =$j;
                            }
                        }
                        foreach ($new_arr as $i=>$j){
                            $end_info[$i]['info'] = $j;
                        }
                        foreach ($order_undate['store_name'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_name[] =$j;
                            }
                        }
                        foreach ($new_arr_name as $i=>$j){
                            $end_info[$i]['store_name'] = $j;
                        }

                        foreach ($order_undate['status'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_status[] = $j;
                            }
                        }
                        foreach ($new_arr_status as $i=>$j){
                            $end_info[$i]['status'] = $j;
                        }
                    }
                    if(!empty($data_infomation)){
                        if(!empty($new_arr)){
                            $coutn =count($new_arr);
                        }else{
                            $coutn =0;
                        }
                        foreach ($data_infomation['name'] as $a=>$b){
                            $end_info[$a+$coutn]['store_name'] = $b;
                        }
                        foreach ($data_infomation['status'] as $a=>$b){
                            $end_info[$a+$coutn]['status'] = $b;
                        }
                        foreach ($data_infomation['all'] as $a=>$b){
                            $end_info[$a+$coutn]['info'][] = $b;
                        }
                    }

                    if (!empty($end_info)) {
                        return ajax_success('数据', $end_info);
                    } else {
                        return ajax_error('没数据');
                    }
                } else {
                    return ajax_error('请登录', ['status' => 0]);
                }
            }
        }
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态退货
     **************************************
     * @return \think\response\View
     */
    public function order_parts_return_goods(){
        return view('order_parts_return_goods');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态退货接口
     **************************************
     * @param Request $request
     */
    public function ios_api_order_parts_return_goods(Request $request){
        if ($request->isPost()) {
            $datas = session('member');
            if (!empty($datas)) {
                $member_id = Db::name('user')->field('id')->where('phone_num', $datas['phone_num'])->find();
                if (!empty($datas)) {
                    $data = Db::name('order_parts')
                        ->field('parts_order_number,group_concat(id) order_parts_id')
                        ->where('user_id', $member_id['id'])
                        ->where('status',11)
                        ->order('order_create_time', 'desc')
                        ->group('parts_order_number')
                        ->select();
                    foreach ($data as $key => $value) {
                        if (strpos($value['order_parts_id'], ',')) {
                            $order_id = explode(',', $value['order_parts_id']);
                            foreach ($order_id as $k => $v) {
                                $return_data[] = Db::name('order_parts')->where('id', $v) ->where('user_id', $member_id['id'])->find();
                            }
                            foreach ($return_data as $ke => $item) {
                                $order_store_id[] = $item['store_id'];
                            }
                            $da_store_id = array_unique($order_store_id); //去重之后的商户
                            foreach ($da_store_id as $da_k => $da_v) {
                                $order_undate['info'][] = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->select();
                                $names = Db::name('order_parts')
                                    ->where('store_id', $da_v)
                                    ->where('user_id', $member_id['id'])
                                    ->where('parts_order_number', $value['parts_order_number'])
                                    ->find();
                                $order_undate['store_name'][] = $names['store_name'];
                                $order_undate['status'][] = $names['status'];
                            }
                        }
                        else{
                            $return_datas = Db::name('order_parts')->where('id', $value['order_parts_id'])->find();
                            $data_infomation['name'][]= $return_datas['store_name'];
                            $data_infomation['status'][] = $return_datas['status'];
                            $data_infomation['all'][] = Db::name('order_parts')->where('id', $value['order_parts_id'])->find();
                        }
                    };

                    if (!empty($order_undate)) {
                        foreach ($order_undate['info'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr[] =$j;
                            }
                        }
                        foreach ($new_arr as $i=>$j){
                            $end_info[$i]['info'] = $j;
                        }
                        foreach ($order_undate['store_name'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_name[] =$j;
                            }
                        }
                        foreach ($new_arr_name as $i=>$j){
                            $end_info[$i]['store_name'] = $j;
                        }

                        foreach ($order_undate['status'] as $i => $j) {
                            if(!empty($j)){
                                $new_arr_status[] = $j;
                            }
                        }
                        foreach ($new_arr_status as $i=>$j){
                            $end_info[$i]['status'] = $j;
                        }
                    }
                    if(!empty($data_infomation)){
                        if(!empty($new_arr)){
                            $coutn =count($new_arr);
                        }else{
                            $coutn =0;
                        }
                        foreach ($data_infomation['name'] as $a=>$b){
                            $end_info[$a+$coutn]['store_name'] = $b;
                        }
                        foreach ($data_infomation['status'] as $a=>$b){
                            $end_info[$a+$coutn]['status'] = $b;
                        }
                        foreach ($data_infomation['all'] as $a=>$b){
                            $end_info[$a+$coutn]['info'][] = $b;
                        }
                    }

                    if (!empty($end_info)) {
                        return ajax_success('数据', $end_info);
                    } else {
                        return ajax_error('没数据');
                    }
                } else {
                    return ajax_error('请登录', ['status' => 0]);
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态修改（未付款买家取消订单）
     **************************************
     * @param Request $request
     */
    public function ios_api_order_parts_no_pay_cancel(Request $request){
        if($request->isPost()){
            $order_id =$_POST['order_id'];
            if(is_array($order_id)){
                $where ='id in('.implode(',',$order_id).')';
            }else{
                $where ='id='.$order_id;
            }
            if(!empty($order_id)){
                $res =Db::name('order_parts')->where($where)->update(['status'=>9]);
                if($res){
                    return ajax_success('订单取消成功',['status'=>1]);
                }else{
                    return ajax_error('订单取消失败',['status'=>0]);
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单状态修改（买家确认收货）
     **************************************
     * @param Request $request
     */
    public function ios_api_order_parts_collect_goods(Request $request){
        if($request->isPost()){
            $order_id =$_POST['order_id'];
            if(is_array($order_id)){
                $where ='id in('.implode(',',$order_id).')';
            }else{
                $where ='id='.$order_id;
            }
            if(!empty($order_id)){
                $res =Db::name('order_parts')->where($where)->update(['status'=>7]);
                if($res){
                    return ajax_success('确认收货成功',['status'=>1]);
                }else{
                    return ajax_error('确认收货失败',['status'=>0]);
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商提交订单接口
     **************************************
     */
    public function  ios_api_order_parts_button(Request $request){
        if ($request->isPost()) {
            $data = $_POST;
            $user_id =Session::get("user");
            $is_address = Db::name('user_address')->where('user_id', $user_id)->find();
            if (empty($is_address) ) {
                return ajax_error('请填写收货地址',['status'=>0]);
            }else{
                $is_address_status = Db::name('user_address')->where('user_id', $user_id)->where('status',1)->find();
                if (empty($is_address_status) ) {
                    $is_address_status =$is_address;
                }
                $commodity_id = $_POST['goods_id'];
                if (!empty($commodity_id)) {
                    $goods_data = Db::name('goods')->where('id', $commodity_id)->find();
                    $create_time = time();
                    if (!empty($data)) {
                        $harvest_address_city =str_replace(',','',$is_address_status['address_name']);
                        $harvest_address =$harvest_address_city.$is_address_status['harvester_real_address']; //收货人地址
                        $time=date("Y-m-d",time());
                        $v=explode('-',$time);
                        $time_second=date("H:i:s",time());
                        $vs=explode(':',$time_second);
                        $parts_order_number =$v[0].$v[1].$v[2].$vs[0].$vs[1].$vs[2].rand(1000,9999).$user_id; //订单编号
                        $datas = [
                            'goods_image' => $goods_data['goods_show_images'],//图片
                            'parts_goods_name' => $goods_data['goods_name'],//名字
                            'order_quantity' => $data['order_quantity'],//订单数量
                            'user_id' => $user_id,
                            'harvester' => $is_address_status['harvester'],
                            'harvest_phone_num' => $is_address_status['harvester_phone_num'],
                            'harvester_address' => $harvest_address,
                            'order_create_time' => $create_time,
                            'order_amount' => $data['order_amount'], //订单金额
                            'status' => 1,
                            'goods_id' => $commodity_id,
                            'store_id' => $data['shop_id'],
                            'goods_standard'=>$data["goods_standard"], //商品规格
                            'parts_order_number' => $parts_order_number,//时间+4位随机数+用户id构成订单号
                        ];
                        $res = Db::name('order_parts')->insertGetId($datas);
                        if ($res) {
                            return ajax_success('下单成功', $datas['parts_order_number']);
                        }else{
                            return ajax_error('失败',['status'=>0]);
                        }
                    }
                }
            }


        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单确认订单页面
     **************************************
     */
    public function ios_api_order_parts_firm_order(){
            return view("order_parts_firm_order");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:立即购买存储数据，方便确定订单提交
     **************************************
     * @param Request $request
     */
    public function get_goods_id_save(Request $request){
        if($request->isPost()){
            $goods_id =$request->only('goods_id')['goods_id'];//商品id
            $goods_number=$request->only('goods_number')['goods_number'];//数量
            $goods_standard=$request->only('goods_standard')['goods_standard'];//规格
            if(!empty($goods_id)){
                $data =[
                    "goods_id"=>$goods_id,
                    "goods_number"=>$goods_number,
                    "goods_standard"=>$goods_standard
                ];
                Session::set('part_goods_info',$data);
                return ajax_success('保存商品id成功',$data);
            }else{
                return ajax_error('保存商品失败',['status'=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:提交订单页面返回购买页面传过来的数据
     **************************************
     */
    public function return_order_buy_information(Request $request){
        if($request->isPost()){
           $part_goods_info = Session::get("part_goods_info");
           if(!empty($part_goods_info)){
               $goods_id = $part_goods_info["goods_id"];
               $goods = db("goods")->where("id",$goods_id)->select();
               foreach ($goods as $key=>$value){
                   $goods[$key]["goods_standard_name"] = explode(",",$value["goods_standard_name"]);
                   $goods_standard_value = explode(",",$value["goods_standard_value"]);
                   $goods[$key]["goods_standard_value"] = array_chunk($goods_standard_value,"8");
                   $goods[$key]["goods_brand"] = db("brand")->where("id",$value["goods_brand_id"])->find();
                   $goods[$key]["images"] = db("goods_images")->where("goods_id",$value["id"])->select();
               }
               if(!empty($goods)){
                   $part_goods_info['goods'] =$goods;
                   return ajax_success("数据返回成功",$part_goods_info);
               }else{
                   return ajax_error("没有数据",["status"=>0]);
               }
           }else{
               return ajax_success("没有数据",["status"=>0]);
           }
        }
    }




    /**
     **************李火生*******************
     * @param Request $request
     * Notes:生成订单(未用)
     **************************************
     * @param Request $request
     */
    public function ios_return_parts_num(Request $request){
        if($request->isPost()){
            $ios_return_num =$request->only(['num'])['num'];
            $data =Db::name('order_parts')->where('parts_order_number',$ios_return_num)->find();
            if($data){
                return ajax_success('成功',$data);
            }else{
                return ajax_error('失败');
            }
        }
    }




}