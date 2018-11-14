<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/10/24
 * Time: 9:56
 * 订单管理
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
class Order extends Controller{


    /***
     * TODO：配件商订单开始
     */

    /**
     **************李火生*******************
     * @return \think\response\View
     * 订单列表
     **************************************
     */
    public function index(){

        $order_parts_data =Db::table('tb_order_parts')
            ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
            ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
            ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
            ->order('tb_order_parts.order_create_time','desc')
            ->paginate(3);

       return view('index',['order_parts_data'=>$order_parts_data]);
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单弹窗的订单信息（前端传过来一个id）
     * 路由："order_processing"=>"admin/Order/order_processing"
     **************************************
     * @param Request $request
     */
    public function order_processing(Request $request){
        if($request->isPost()){
            $id =$request->only(['id'])['id'];
            if(!empty($id)){
                $data =Db::name('order_parts')->where('id',$id)->find();
                if(!empty($data)){
                    return ajax_success('订单信息成功返回',$data);
                }else{
                    return ajax_error('该订单没有数据记录',['status'=>0]);
                }
            }else{
                return ajax_error('沒有订单Id',['status'=>0]);
            }
        }
    }



    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商列表模糊搜索
     **************************************
     * @return \think\response\View
     */
    public function search()
    {
        $keywords = input('parts_order_number'); //订单编号
        $goods_name = input('goods_name');            //商品名称
        $phone_num = input('phone_num');         //用户账号
        $order_status = input('order_status');   // 订单状态
        $timemin = strtotime(input('date_min'));           //开始时间
        /*添加一天（23：59：59）*/
        $time_max_data = strtotime(input('date_max'));
        $t = date('Y-m-d H:i:s', $time_max_data + 1 * 24 * 60 * 60);
        $timemax = strtotime($t);                       //结束时间
        $order_parts_data=[];

        if (empty($keywords)) { //空
            if((!empty($goods_name)) && (empty($phone_num)) && (empty($order_status)) && (empty($timemin)) && (empty($time_max_data))) {
                $order_parts_data = Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user", "tb_order_parts.user_id=tb_user.id", 'left')
                    ->join("tb_goods", "tb_order_parts.goods_id=tb_goods.id", "left")
                    ->where("tb_order_parts.parts_goods_name", "like","%" .$goods_name ."%")
                    ->order('tb_order_parts.order_create_time', 'desc')
                    ->paginate(3, false, [
                        'query' => request()->param(),
                    ]);
            }
            else if((empty($goods_name)) && (!empty($phone_num)) && (empty($order_status)) && (empty($timemin)) && (empty($time_max_data))) {
                $order_parts_data = Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user", "tb_order_parts.user_id=tb_user.id", 'left')
                    ->join("tb_goods", "tb_order_parts.goods_id=tb_goods.id", "left")
                    ->where("tb_user.phone_num", "like", "%" . $phone_num . "%")
                    ->order('tb_order_parts.order_create_time', 'desc')
                    ->paginate(3, false, [
                        'query' => request()->param(),
                    ]);
            }
            else if((empty($goods_name)) && (empty($phone_num)) && (!empty($order_status)) && (empty($timemin)) && (empty($time_max_data))) {
                $order_parts_data = Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user", "tb_order_parts.user_id=tb_user.id", 'left')
                    ->join("tb_goods", "tb_order_parts.goods_id=tb_goods.id", "left")
                    ->where("tb_order_parts.status", "like", "%" . $order_status . "%")
                    ->order('tb_order_parts.order_create_time', 'desc')
                    ->paginate(3, false, [
                        'query' => request()->param(),
                    ]);
            }
            else if((empty($goods_name)) && (empty($phone_num)) && (empty($order_status)) && (!empty($timemin)) && (empty($time_max_data))) {
                $order_parts_data = Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user", "tb_order_parts.user_id=tb_user.id", 'left')
                    ->join("tb_goods", "tb_order_parts.goods_id=tb_goods.id", "left")
                    ->where('tb_order_parts.order_create_time','>=',"$timemin")
                    ->order('tb_order_parts.order_create_time', 'desc')
                    ->paginate(3, false, [
                        'query' => request()->param(),
                    ]);
            }
            else if ((empty($goods_name)) && (empty($phone_num)) && (empty($order_status)) && (empty($timemin)) && (!empty($time_max_data))) {
                $order_parts_data = Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user", "tb_order_parts.user_id=tb_user.id", 'left')
                    ->join("tb_goods", "tb_order_parts.goods_id=tb_goods.id", "left")
                    ->where('tb_order_parts.order_create_time','<=',"timemax")
                    ->order('tb_order_parts.order_create_time', 'desc')
                    ->paginate(3, false, [
                        'query' => request()->param(),
                    ]);
            }
            else if ((empty($goods_name)) && (empty($phone_num)) && (empty($order_status)) && (!empty($timemin)) && (!empty($time_max_data))) {
                $res = "tb_order_parts.order_create_time>{$timemin} and tb_order_parts.order_create_time <{$timemax}";
                $order_parts_data = Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user", "tb_order_parts.user_id=tb_user.id", 'left')
                    ->join("tb_goods", "tb_order_parts.goods_id=tb_goods.id", "left")
                    ->where($res)
                    ->order('tb_order_parts.order_create_time', 'desc')
                    ->paginate(3, false, [
                        'query' => request()->param(),
                    ]);
            }
            else if((!empty($goods_name)) && (!empty($phone_num)) && (empty($order_status)) && (empty($timemin)) && (empty($time_max_data))){
                $order_parts_data =Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                    ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                    ->where("tb_order_parts.parts_goods_name", "like","%" .$goods_name ."%")
                    ->whereOr("tb_user.phone_num", "like", "%" . $phone_num . "%")
                    ->order('tb_order_parts.order_create_time','desc')
                    ->paginate(3);
            }
            else if((empty($goods_name)) && (empty($phone_num)) && (empty($order_status)) && (empty($timemin)) && (empty($time_max_data))){
                $order_parts_data =Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                    ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                    ->order('tb_order_parts.order_create_time','desc')
                    ->paginate(3);
            }
            return view('index', ['order_parts_data' => $order_parts_data]);
        }
          if (!empty($keywords)) { //有值
            if((!empty($goods_name)) && (empty($phone_num)) && (empty($order_status)) && (empty($timemin)) && (empty($time_max_data))) {
                $order_parts_data = Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user", "tb_order_parts.user_id=tb_user.id", 'left')
                    ->join("tb_goods", "tb_order_parts.goods_id=tb_goods.id", "left")
                    ->where("tb_order_parts.parts_goods_name", "like","%" .$goods_name ."%")
                    ->whereOr("tb_order_parts.parts_order_number", "like", "%" . $keywords . "%")
                    ->order('tb_order_parts.order_create_time', 'desc')
                    ->paginate(3, false, [
                        'query' => request()->param(),
                    ]);
            }
            else if((empty($goods_name)) && (!empty($phone_num)) && (empty($order_status)) && (empty($timemin)) && (empty($time_max_data))) {
                $order_parts_data = Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user", "tb_order_parts.user_id=tb_user.id", 'left')
                    ->join("tb_goods", "tb_order_parts.goods_id=tb_goods.id", "left")
                    ->where("tb_user.phone_num", "like", "%" . $phone_num . "%")
                    ->whereOr("tb_order_parts.parts_order_number", "like", "%" . $keywords . "%")
                    ->order('tb_order_parts.order_create_time', 'desc')
                    ->paginate(3, false, [
                        'query' => request()->param(),
                    ]);
            }
            else if((empty($goods_name)) && (empty($phone_num)) && (!empty($order_status)) && (empty($timemin)) && (empty($time_max_data))) {
                $order_parts_data = Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user", "tb_order_parts.user_id=tb_user.id", 'left')
                    ->join("tb_goods", "tb_order_parts.goods_id=tb_goods.id", "left")
                    ->where("tb_order_parts.status", "like", "%" . $order_status . "%")
                    ->whereOr("tb_order_parts.parts_order_number", "like", "%" . $keywords . "%")
                    ->order('tb_order_parts.order_create_time', 'desc')
                    ->paginate(3, false, [
                        'query' => request()->param(),
                    ]);
            }
            else if((empty($goods_name)) && (empty($phone_num)) && (empty($order_status)) && (!empty($timemin)) && (empty($time_max_data))) {
                $order_parts_data = Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user", "tb_order_parts.user_id=tb_user.id", 'left')
                    ->join("tb_goods", "tb_order_parts.goods_id=tb_goods.id", "left")
                    ->where('tb_order_parts.order_create_time','>=',"$timemin")
                    ->whereOr("tb_order_parts.parts_order_number", "like", "%" . $keywords . "%")
                    ->order('tb_order_parts.order_create_time', 'desc')
                    ->paginate(3, false, [
                        'query' => request()->param(),
                    ]);
            }
            else if ((empty($goods_name)) && (empty($phone_num)) && (empty($order_status)) && (empty($timemin)) && (!empty($time_max_data))) {
                $order_parts_data = Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user", "tb_order_parts.user_id=tb_user.id", 'left')
                    ->join("tb_goods", "tb_order_parts.goods_id=tb_goods.id", "left")
                    ->where('tb_order_parts.order_create_time','<=',"$timemax")
                    ->whereOr("tb_order_parts.parts_order_number", "like", "%" . $keywords . "%")
                    ->order('tb_order_parts.order_create_time', 'desc')
                    ->paginate(3, false, [
                        'query' => request()->param(),
                    ]);
            }
            else if (((!empty($goods_name)) && (!empty($phone_num))) || (empty($order_status)) || (empty($timemin)) || (empty($time_max_data))) {
                $order_parts_data = Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user", "tb_order_parts.user_id=tb_user.id", 'left')
                    ->join("tb_goods", "tb_order_parts.goods_id=tb_goods.id", "left")
                    ->where("tb_order_parts.parts_goods_name", "like","%" .$goods_name ."%")
                    ->whereOr("tb_order_parts.parts_goods_name", "like","%" .$goods_name ."%")
                    ->whereOr("tb_user.phone_num", "like", "%" . $phone_num . "%")
                    ->order('tb_order_parts.order_create_time', 'desc')
                    ->paginate(3, false, [
                        'query' => request()->param(),
                    ]);
            }
            else if((empty($goods_name)) && (empty($phone_num)) && (empty($order_status)) && (empty($timemin)) && (empty($time_max_data))){
                $order_parts_data =Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                    ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                    ->where("tb_order_parts.parts_order_number", "like", "%" . $keywords . "%")
                    ->order('tb_order_parts.order_create_time','desc')
                    ->paginate(3);
            }
            return view('index', ['order_parts_data' => $order_parts_data]);
        }
    }






    /**
     **************李火生*******************
     * @param Request $request
     * Notes:配件商订单列表批量删除
     **************************************
     * @param Request $request
     */
    public function dels(Request $request){
        if($request->isPost()){
            $id =$_POST['id'];
            if(is_array($id)){
                $where ='id in('.implode(',',$id).')';
            }else{
                $where ='id='.$id;
            }
            $list =  Db::name('order_parts')->where($where)->delete();
            if($list!==false)
            {
                return ajax_success('成功删除!',['status'=>1]);
            }else{
                return ajax_error('删除失败',['status'=>0]);
            }
        }
    }


    /**
     **************李火生*******************
     * @return \think\response\View
     *订单编辑
     **************************************
     */
    public function edit($id){
        $order_parts_data = Db::name("order_parts")->where("id",$id)->select();
        dump($order_parts_data);
        return view('edit',['order_parts_data'=>$order_parts_data]);
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     *订单订单评价
     **************************************
     */
    public function evaluate(){
        return view('evaluate');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 订单评价详情
     **************************************
     */
    public function evaluate_details(){
        return view('evaluate_details');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     *订单维修售后
     **************************************
     */
    public function after_sale(){
        return view('after_sale');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     *订单维修售后待处理
     **************************************
     */
    public function after_sale_wait_handle(){
        return view('after_sale_wait_handle');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     *订单维修售后待发货
     **************************************
     */
    public function after_sale_wait_deliver(){
        return view('after_sale_wait_deliver');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     *发票列表
     **************************************
     */
    public function invoice(){
        return view('invoice');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     *发票信息
     **************************************
     */
    public function invoice_edit(){
        return view('invoice_edit');
    }

    /**
     * TODO:配件商订单结束
     */


    /**
     * TODO:平台订单开始
     */
    /**
     **************李火生*******************
     * @return \think\response\View
     * 平台商服务商订单列表
     **************************************
     */
    public function platform_order_service_index(){
        return view('platform_order_service_index');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 平台商配件商订单列表
     **************************************
     */
    public function platform_order_parts_index(){
        $order_parts_data =Db::table('tb_order_parts')
            ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
            ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
            ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
            ->order('tb_order_parts.order_create_time','desc')
            ->paginate(3 );
        return view('platform_order_parts_index',['order_parts_data'=>$order_parts_data]);
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:平台商配件商订单列表模糊搜索
     **************************************
     * @return \think\response\View
     */
    public function platform_order_parts_search(){
        $keywords = input('parts_order_number'); //订单编号
        $goods_name = input('goods_name');            //商品名称
        $phone_num = input('phone_num');         //用户账号
        $order_status = input('order_status');   // 订单状态
        $timemin = strtotime(input('date_min'));           //开始时间
        /*添加一天（23：59：59）*/
        $time_max_data = input('date_max');
        $time_max =strtotime($time_max_data);
        $t = date('Y-m-d H:i:s', $time_max + 1 * 24 * 60 * 60);
        $timemax = strtotime($t);                       //结束时间

        if((empty($keywords)) && (empty($goods_name)) && (empty($phone_num)) && (empty($order_status))){
            if((!empty($timemin))&&(!empty($time_max_data))){
                $time_condition  = "order_create_time > {$timemin} and order_create_time < {$timemax}";
                $order_parts_data =Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                    ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                    ->where($time_condition)
                    ->order('tb_order_parts.order_create_time','desc')
                    ->paginate(3 ,false, [
                        'query' => request()->param(),
                    ]);
            }else if((!empty($timemin))&&(empty($time_max_data))){
                $time_condition  = "order_create_time>{$timemin} ";
                $order_parts_data =Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                    ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                    ->where($time_condition)
                    ->order('tb_order_parts.order_create_time','desc')
                    ->paginate(3 ,false, [
                        'query' => request()->param(),
                    ]);
            }else if((empty($timemin))&&(!empty($time_max_data))){
                $time_condition  = "order_create_time< {$timemax}";
                $order_parts_data =Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                    ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")

                    ->where($time_condition)
                    ->order('tb_order_parts.order_create_time','desc')
                    ->paginate(3 ,false, [
                        'query' => request()->param(),
                    ]);
            }else{
                $order_parts_data =Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                    ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                    ->order('tb_order_parts.order_create_time','desc')
                    ->paginate(3 ,false, [
                        'query' => request()->param(),
                    ]);
            }
        }else{
            $keyword= (!empty($keywords)) ? $keywords :((!empty($goods_name)) ? $goods_name : ((!empty($phone_num)) ? $phone_num : ((!empty($order_status)) ? $order_status : '')));
            if(!empty($keyword)){
                if((!empty($timemin))&&(!empty($time_max_data))){
                    $condition = " `parts_goods_name` like '%{$keyword}%' or `parts_order_number` like '%{$keyword}%'  or `user_account_name` like '%{$keyword}%' or `user_phone_number` like '%{$keyword}%'";
                    $time_condition  = "order_create_time>{$timemin} and order_create_time< {$timemax}";
                    $order_parts_data =Db::table('tb_order_parts')
                        ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                        ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                        ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                        ->where($condition)
                        ->where($time_condition)
                        ->order('tb_order_parts.order_create_time','desc')
                        ->paginate(3 ,false, [
                            'query' => request()->param(),
                        ]);
                }else if((!empty($timemin))&&(empty($time_max_data))){
                    $condition = " `parts_goods_name` like '%{$keyword}%' or `parts_order_number` like '%{$keyword}%'  or `user_account_name` like '%{$keyword}%' or `user_phone_number` like '%{$keyword}%'";
                    $time_condition  = "order_create_time>{$timemin} ";
                    $order_parts_data =Db::table('tb_order_parts')
                        ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                        ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                        ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                        ->where($condition)
                        ->where($time_condition)
                        ->order('tb_order_parts.order_create_time','desc')
                        ->paginate(3 ,false, [
                            'query' => request()->param(),
                        ]);
                }else if((empty($timemin))&&(!empty($time_max_data))){
                    $condition = " `parts_goods_name` like '%{$keyword}%' or `parts_order_number` like '%{$keyword}%'  or `user_account_name` like '%{$keyword}%' or `user_phone_number` like '%{$keyword}%'";
                    $time_condition  = "order_create_time< {$timemax}";
                    $order_parts_data =Db::table('tb_order_parts')
                        ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                        ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                        ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                        ->where($condition)
                        ->where($time_condition)
                        ->order('tb_order_parts.order_create_time','desc')
                        ->paginate(3 ,false, [
                            'query' => request()->param(),
                        ]);
                }else{
                    $condition = " `parts_goods_name` like '%{$keyword}%' or `parts_order_number` like '%{$keyword}%'  or `user_account_name` like '%{$keyword}%' or `user_phone_number` like '%{$keyword}%'";
                    $order_parts_data =Db::table('tb_order_parts')
                        ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                        ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                        ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                        ->where($condition)
                        ->order('tb_order_parts.order_create_time','desc')
                        ->paginate(3 ,false, [
                            'query' => request()->param(),
                        ]);
                }

            }else{

                $order_parts_data =Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                    ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                    ->order('tb_order_parts.order_create_time','desc')
                    ->paginate(3 ,false, [
                        'query' => request()->param(),
                    ]);

            }
        }
        return view('platform_order_parts_index',['order_parts_data'=>$order_parts_data]);
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:平台配件商订单管理列表批量删除
     **************************************
     * @param Request $request
     */
    public function platform_order_parts_dels(Request $request){
        if($request->isPost()){
            $id =$_POST['id'];
            if(is_array($id)){
                $where ='id in('.implode(',',$id).')';
            }else{
                $where ='id='.$id;
            }
            $list =  Db::name('order_parts')->where($where)->delete();
            if($list!==false)
            {
                return ajax_success('成功删除!',['status'=>1]);
            }else{
                return ajax_error('删除失败',['status'=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 平台商售后服务
     **************************************
     *
     */
    public function platform_after_sale(){
        return view('platform_after_sale');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 平台商发票列表
     **************************************
     */
    public function platform_invoice_index(){
        return view('platform_invoice_index');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 平台商发票详情
     **************************************
     */
    public function platform_invoice_details(){
        return view('platform_invoice_details');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 平台商订单评价
     **************************************
     */
    public function platform_order_evaluate(){
        return view('platform_order_evaluate');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 平台商订单评价编辑
     **************************************
     */
    public function platform_order_evaluate_edit(){
        return view('platform_order_evaluate_edit');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 平台商订单设置
     **************************************
     */
    public function platform_order_set_up(){
        return view('platform_order_set_up');
    }



    /**
     * TODO:平台商订结束
     */



    /**
     * TODO:服务商订单开始
     */
    /**
     **************李火生*******************
     * @return \think\response\View
     * 服务商界面服务商订单列表
     **************************************
     */
    public function service_order_index(){
//        $service_order_data =Db::table('tb_order_service')
//            ->field("tb_order_service.*,tb_user.phone_num phone_num,tb_goods.name gname,tb_goods.show_images gimg")
//            ->join("tb_user","tb_integral.user_id=tb_user.id",'left')
//            ->order('tb_integral.operation_time','desc')
//            ->paginate(3 ,false, [
//                'query' => request()->param(),
//            ]);
        return view('service_order_index');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 服务商界面订单评价
     **************************************
     */
    public function service_order_evaluate(){
        return view('service_order_evaluate');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 服务商界面订单评价
     **************************************
     */
    public function service_order_evaluate_edit(){
        return view('service_order_evaluate_edit');
    }

    /**
     * TODO:服务商订结束
     */


}