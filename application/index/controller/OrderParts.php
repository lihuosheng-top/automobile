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
     * Notes:配件商订单状态全部订单接口
     **************************************
     * @param Request $request
     */
    public function   ios_api_order_parts_all(Request $request){
    if($request->isPost()){
        $datas =session('member');
//        $datas =[
//            'phone_num'=>'18998906797'
//        ];
        if(!empty($datas)){
            $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
            if(!empty($datas)){
                $data =Db::name('order_parts')
                    ->field('parts_order_number,group_concat(id) order_parts_id')
                    ->where('user_id',$member_id['id'])
                    ->order('order_create_time','desc')
                    ->group('parts_order_number')
                    ->select();
                if(!empty($data)){
                    foreach ($data as $k=>$v){
                        $all_id[] =$v['order_parts_id'];
                    }
                    foreach ($all_id as $key=>$value){
                        if(strpos($value,',')){
                            $result =explode(',',$value);
                            foreach ($result as $ks=>$vs){
                                $data_return[$key][] =Db::name('order_parts')->where('id',$vs)->find();
                            }
                        }else{
                            $data_return[$key][] =Db::name('order_parts')->where('id',$value)->find();
                        }
                    }
                    foreach ($data_return as $keys=>$values){
//                        dump($values);
                        foreach ($values as $ke=>$item){
//                            $newArr =[];
                           $newArr[$keys][$item['store_id']][] = $item;
                        }
                    }
//                    dump($newArr);


                    if(!empty($newArr)){
                        return ajax_success('全部信息返回成功',$newArr);
                    }else{
                        return ajax_error('没有订单',['status'=>0]);
                    }
                }else{
                    return ajax_error('没有订单',['status'=>0]);
                }


            }
        }else{
            return ajax_error('请登录',['status'=>0]);
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
        if($request->isPost()){
            $datas =session('member');
            if(!empty($datas)){
                $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
                if(!empty($datas)){
                    $data =Db::name('order_parts')
                        ->field('parts_order_number,group_concat(id) order_parts_id')
                        ->where('user_id',$member_id['id'])
                        ->where('status',1)
                        ->order('order_create_time','desc')
                        ->group('parts_order_number')
                        ->select();
                    if(!empty($data)){
                        foreach ($data as $k=>$v){
                            $all_id[] =$v['order_parts_id'];
                        }
                        foreach ($all_id as $key=>$value){
                            if(strpos($value,',')){
                                $result =explode(',',$value);
                                foreach ($result as $ks=>$vs){
                                    $data_return[$key][] =Db::name('order_parts')->where('id',$vs)->find();
                                }
                            }else{
                                $data_return[$key][] =Db::name('order_parts')->where('id',$value)->find();
                            }
                        }
                        if(!empty($data_return)){
                            return ajax_success('全部信息返回成功',$data_return);
                        }else{
                            return ajax_error('没有订单',['status'=>0]);
                        }
                    }else{
                        return ajax_error('没有订单',['status'=>0]);
                    }


                }
            }else{
                return ajax_error('请登录',['status'=>0]);
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
        if($request->isPost()){
            $datas =session('member');
            if(!empty($datas)){
                $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
                if(!empty($datas)){
                    $condition ="`status` = '2' or `status` = '3' or `status` = '4' or `status` = '5' ";
                    $data =Db::name('order_parts')
                        ->field('parts_order_number,group_concat(id) order_parts_id')
                        ->where('user_id',$member_id['id'])
                        ->where($condition)
                        ->order('order_create_time','desc')
                        ->group('parts_order_number')
                        ->select();
                    if(!empty($data)){
                        foreach ($data as $k=>$v){
                            $all_id[] =$v['order_parts_id'];
                        }
                        foreach ($all_id as $key=>$value){
                            if(strpos($value,',')){
                                $result =explode(',',$value);
                                foreach ($result as $ks=>$vs){
                                    $data_return[$key][] =Db::name('order_parts')->where('id',$vs)->find();
                                }
                            }else{
                                $data_return[$key][] =Db::name('order_parts')->where('id',$value)->find();
                            }
                        }
                        if(!empty($data_return)){
                            return ajax_success('全部信息返回成功',$data_return);
                        }else{
                            return ajax_error('没有订单',['status'=>0]);
                        }
                    }else{
                        return ajax_error('没有订单',['status'=>0]);
                    }

                }
            }else{
                return ajax_error('请登录',['status'=>0]);
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
        if($request->isPost()){
            $datas =session('member');
            if(!empty($datas)){
                $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
                if(!empty($datas)){
                    $condition ="`status` = '6' or `status` = '7'";
                    $data =Db::name('order_parts')
                        ->field('parts_order_number,group_concat(id) order_parts_id')
                        ->where('user_id',$member_id['id'])
                        ->where($condition)
                        ->order('order_create_time','desc')
                        ->group('parts_order_number')
                        ->select();
                    if(!empty($data)){
                        foreach ($data as $k=>$v){
                            $all_id[] =$v['order_parts_id'];
                        }
                        foreach ($all_id as $key=>$value){
                            if(strpos($value,',')){
                                $result =explode(',',$value);
                                foreach ($result as $ks=>$vs){
                                    $data_return[$key][] =Db::name('order_parts')->where('id',$vs)->find();
                                }
                            }else{
                                $data_return[$key][] =Db::name('order_parts')->where('id',$value)->find();
                            }
                        }
                        if(!empty($data_return)){
                            return ajax_success('全部信息返回成功',$data_return);
                        }else{
                            return ajax_error('没有订单',['status'=>0]);
                        }
                    }else{
                        return ajax_error('没有订单',['status'=>0]);
                    }

                }
            }else{
                return ajax_error('请登录',['status'=>0]);
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
        if($request->isPost()){
            $datas =session('member');
            if(!empty($datas)){
                $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
                if(!empty($datas)){
                    $data =Db::name('order_parts')
                        ->field('parts_order_number,group_concat(id) order_parts_id')
                        ->where('user_id',$member_id['id'])
                        ->where('status',11)
                        ->order('order_create_time','desc')
                        ->group('parts_order_number')
                        ->select();
                    if(!empty($data)){
                        foreach ($data as $k=>$v){
                            $all_id[] =$v['order_parts_id'];
                        }
                        foreach ($all_id as $key=>$value){
                            if(strpos($value,',')){
                                $result =explode(',',$value);
                                foreach ($result as $ks=>$vs){
                                    $data_return[$key][] =Db::name('order_parts')->where('id',$vs)->find();
                                }
                            }else{
                                $data_return[$key][] =Db::name('order_parts')->where('id',$value)->find();
                            }
                        }
                        if(!empty($data_return)){
                            return ajax_success('全部信息返回成功',$data_return);
                        }else{
                            return ajax_error('没有订单',['status'=>0]);
                        }
                    }else{
                        return ajax_error('没有订单',['status'=>0]);
                    }


                }
            }else{
                return ajax_error('请登录',['status'=>0]);
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
            $member_data = session('member');
            $member = Db::name('user')->field('id,harvester,harvester_phone_num,harvester_real_address')->where('phone_num', $member_data['phone_num'])->find();
            if (empty($member['harvester']) || empty($member['harvester_phone_num']) || empty($member['harvester_real_address']) ) {
                return ajax_error('请填写收货人信息',['status'=>0]);
            }
            $commodity_id = $_POST['goods_id'];
            if (!empty($commodity_id)) {
                $goods_data = Db::name('goods')->where('id', $commodity_id)->find();
                $create_time = time();
                if (!empty($data)) {
                    $datas = [
                        'goods_img' => $goods_data['goods_show_images'],
                        'goods_name' => $data['goods_name'],
                        'order_num' => $data['order_num'],
                        'user_id' => $member['id'],
                        'harvester' => $member['harvester'],
                        'harvest_phone_num' => $member['harvester_phone_num'],
                        'harvest_address' => $position,
                        'create_time' => $create_time,
                        'pay_money' => $data['all_pay'],
                        'status' => 1,
                        'goods_id' => $commodity_id,
                        'send_money' => $data['express_fee'],
                        'order_information_number' => $create_time . $member['id'],//时间戳+用户id构成订单号
                    ];
                    $res = Db::name('order')->insertGetId($datas);
                    if ($res) {
                        //TODO:
//                        Session::delete('goods_id');
                        session('order_id', $res);
                        $discounts =  Db::name('discounts_user')->field('discounts_id')->where('user_id',$member['id'])->find();
                        if(!empty($discounts)){
                            $bools =Db::name('discounts')->where('id',$discounts['discounts_id'])->update(['status'=>2]);
                        }
                        return ajax_success('下单成功', $datas['order_information_number']);
                    }
                }
            }
        }
    }




}