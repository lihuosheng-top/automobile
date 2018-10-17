<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/9
 * Time: 10:04
 */

namespace app\index\controller;

use think\console\command\make\Model;
use think\Controller;
use think\Request;
use think\Db;
use think\Session;


class Share extends Controller{

    /****************************晒单部分********************************************/

    /**
     **************李火生*******************
     * @return \think\response\View
     * 晒单首页
     **************************************
     */
    public function share_index(){
        return view("share_index");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 点击全部按钮时需要显示的所有的晒单信息
     **************************************
     */
        public function all_information(Request $request){
            if($request->isPost()){
                $get_id= $request->only(['id'])['id'];
                if(!empty($get_id)&&$get_id==2){
                    $all_evaluation_data=Db::table("tb_evaluate")
                        ->field("tb_evaluate.*,tb_goods.goods_name goods_name,tb_goods.goods_show_images goods_show_images ,tb_user.phone_num phone_num")
                        ->join("tb_goods","tb_evaluate.goods_id=tb_goods.id",'left')
                        ->join("tb_user","tb_evaluate.user_id=tb_user.id",'left')
                        ->where('tb_evaluate.status',1)
                        ->order('tb_evaluate.create_time','desc')
                        ->select();
                    return ajax_success('全部数据返回',$all_evaluation_data);
                }
            }
        }

    /**
     **************李火生*******************
     * @param Request $request
     * 一进入晒单页面需要显示的全部信息
     **************************************
     */
        public function all_information_share(Request $request){
            if($request->isPost()){
                $all_evaluation_data=Db::table("tb_evaluate")
                    ->field("tb_evaluate.*,tb_goods.goods_name goods_name,tb_goods.goods_show_images goods_show_images ,tb_user.phone_num phone_num")
                    ->join("tb_goods","tb_evaluate.goods_id=tb_goods.id",'left')
                    ->join("tb_user","tb_evaluate.user_id=tb_user.id",'left')
                    ->where('tb_evaluate.status',1)
                    ->order('tb_evaluate.create_time','desc')
                    ->select();
                if(!empty($all_evaluation_data)){
                    foreach ($all_evaluation_data as $value){
                        $all_evaluation_img_url =Db::table('tb_evaluate_images')->where('evaluate_order_id',$value['order_id'])->select();
                        if(!empty($all_evaluation_img_url)){
                            $datas[] =[
                                'id'=>$value['id'],
                                'evaluate_content'=>$value['evaluate_content'],
                                'evaluate_images'=>$value['evaluate_images'],
                                'goods_id'=>$value['goods_id'],
                                'user_id'=>$value['user_id'],
                                'status'=>$value['status'],
                                'order_id'=>$value['order_id'],
                                'create_time'=>$value['create_time'],
                                'goods_name'=>$value['goods_name'],
                                'goods_show_images'=>$value['goods_show_images'],
                                'phone_num'=>$value['phone_num'],
                                'evaluate_images'=>$all_evaluation_img_url
                            ];
                        }else{
                            $datas[] =[
                                'id'=>$value['id'],
                                'evaluate_content'=>$value['evaluate_content'],
                                'evaluate_images'=>$value['evaluate_images'],
                                'goods_id'=>$value['goods_id'],
                                'user_id'=>$value['user_id'],
                                'status'=>$value['status'],
                                'order_id'=>$value['order_id'],
                                'create_time'=>$value['create_time'],
                                'goods_name'=>$value['goods_name'],
                                'goods_show_images'=>$value['goods_show_images'],
                                'phone_num'=>$value['phone_num'],
                            ];
                        }


                    }
                    return ajax_success('全部数据返回',$datas);
                }

            }
        }

//    public function share_index(){
//        $all_evaluation_data=Db::table("tb_evaluate")
//            ->field("tb_evaluate.*,tb_goods.goods_name goods_name,tb_goods.goods_show_images goods_show_images ,tb_user.phone_num phone_num")
//            ->join("tb_goods","tb_evaluate.goods_id=tb_goods.id",'left')
//            ->join("tb_user","tb_evaluate.user_id=tb_user.id",'left')
//            ->where('tb_evaluate.status',1)
//            ->order('tb_evaluate.create_time','desc')
//            ->select();
//        $this->assign("all_evaluation_data",$all_evaluation_data);
//        if(session::has('phone_evaluation_data')){
//            $phone_evaluation_data =Session::get('phone_evaluation_data');
//            //dump($phone_evaluation_data);
//            $this->assign('phone_evaluation_data',$phone_evaluation_data);
//            session('phone_evaluation_data',null);
//        }
//
//        return view("share_index");
//    }


    /**
     **************李火生*******************
     * @return \think\response\View
     * [晒单详情]
     **************************************
     */
    public function share_detail(){
        $evaluation_id = Session::get('evalues_id');
        $evaluation_data = Db::table("tb_evaluate")
            ->field("tb_evaluate.*,tb_goods.goods_name goods_name,tb_goods.goods_show_images goods_show_images ,tb_user.phone_num phone_num")
            ->join("tb_goods","tb_evaluate.goods_id=tb_goods.id",'left')
            ->join("tb_user","tb_evaluate.user_id=tb_user.id",'left')
            ->where('tb_evaluate.id',$evaluation_id)
            ->find();
        if(!empty($evaluation_data)){
            $this->assign('evaluation_data',$evaluation_data);
        }
        return view("share_detail");
    }


    /**
     **************李火生*******************
     * @param Request $request
     * 评价图片详情
     **************************************
     */
    public function get_evalution_imgs(Request $request){
        if($request->isPost()){
            $evaluation_id = Session::get('evalues_id');
            if(!empty( $evaluation_id)){
                $data_id = $evaluation_id;
                if(!empty($data_id)){
                    $order_id =Db::name('evaluate')->field('order_id')->where('id',$data_id)->find();
                    if(!empty($order_id)){
                        $evaluate_imgs =Db::name('evaluate_images')->field('images')->where('evaluate_order_id',$order_id['order_id'])->select();
                        if(!empty($evaluate_imgs)){
                            return ajax_success('成功',$evaluate_imgs);
                        }
                    }
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 晒单详情页获取id
     **************************************
     */
    public function share_evaluation(Request $request){
        if($request->isPost()){
            $evaluation_id =$_POST['evaluation_id'];
            if(!empty($evaluation_id)){
                session('evalues_id',$evaluation_id);
                return ajax_success('成功',$evaluation_id);
            }
        }
    }


    /**
     **************李火生*******************
     * @return \think\response\View
     * 显示评价信息
     **************************************
     */
    public function evaluation(){
       $evaluation_order_id = Session::get('evaluation_order_id');
       if(!empty($evaluation_order_id)){
           $res = Db::name('order')->where('id',$evaluation_order_id)->find();
           $this->assign('res',$res);
       }
        return view("evaluation");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 获取手机类型给前端铺数据
     **************************************
     */
    public function phone_type(Request $request){
        if($request->isPost()){
            $data = Db::name('goods_type')->select();
            if(!empty($data)){
                return ajax_success('成功获取',$data);
            }
        }
    }

    /**
     * 当我点击某一个手机类型的时候异步刷新铺数据
     */
    public function get_phone_type_informations(Request $request){
        if($request->isPost()){
            $goods_type_id =$request->only(['id'])['id'];
            if(!empty($goods_type_id)&&($goods_type_id>2)){
                    $goods_id_data =Db::name('goods')->field('id')->where('goods_type_id',$goods_type_id)->select();
                    if(!empty($goods_id_data)){
                        if(is_array($goods_id_data)){
                            foreach ($goods_id_data as $key=>$v){
                                $all_evaluation_data[]=Db::table("tb_evaluate")
                                    ->field("tb_evaluate.*,tb_goods.goods_name goods_name,tb_goods.goods_show_images goods_show_images ,tb_user.phone_num phone_num")
                                    ->join("tb_goods","tb_evaluate.goods_id=tb_goods.id",'left')
                                    ->join("tb_user","tb_evaluate.user_id=tb_user.id",'left')
                                    ->where('tb_evaluate.status',1)
                                    ->where('tb_evaluate.goods_id',$v['id'])
                                    ->order('tb_evaluate.create_time','desc')
                                    ->select();
                            }
                            return ajax_success('数据返回成功',$all_evaluation_data);
                        }
                    }
            }
            if(!empty($goods_type_id)&&($goods_type_id==2)){
                $all_evaluation_data=Db::table("tb_evaluate")
                    ->field("tb_evaluate.*,tb_goods.goods_name goods_name,tb_goods.goods_show_images goods_show_images ,tb_user.phone_num phone_num")
                    ->join("tb_goods","tb_evaluate.goods_id=tb_goods.id",'left')
                    ->join("tb_user","tb_evaluate.user_id=tb_user.id",'left')
                    ->where('tb_evaluate.status',1)
                    ->order('tb_evaluate.create_time','desc')
                    ->select();
                return ajax_success('全部数据返回',$all_evaluation_data);
            }
        }
    }





    /***********************评价部分**********************************/
    /**
     **************李火生*******************
     * @param Request $request
     * 发表评价
     **************************************
     */
    public function  evaluation_add(Request $request){
        if($request->isPost()){
            $evaluation_order_id = Session::get('evaluation_order_id');
            if(!empty($evaluation_order_id)){
                $data =$_POST;
                if(!empty($data)){
                    $content = $data['evaluation_content'];
                    if(empty($content)){
                        $this->error('请输入您的评价');
                    }
                    if(!empty($content)){
                        $member =Session::get('member');
                        $user_id =Db::name('user')->field('id')->where('phone_num',$member['phone_num'])->find();
                       $goods_id =Db::name('order')->field('goods_id,order_information_number')->where('id',$evaluation_order_id)->find();
                       if(!empty($user_id)&&!empty($goods_id)){
                           $datas = [
                               'order_id'=> $evaluation_order_id,
                               'order_information_number'=>$goods_id['order_information_number'],
                               'evaluate_content'=>$content,
                               'goods_id'=>$goods_id['goods_id'],
                               'user_id'=>$user_id['id'],
                               'create_time'=>time(),
                               'status'=>0
                           ];
                           $res = Db::name('evaluate')->insert($datas);
                           $order_status_check =Db::name('order')->where('id',$evaluation_order_id)->update(['status'=>10]);
                           if($res!==null &&$order_status_check !==null){
                               return ajax_success('成功',$res);
                           }
                       }

                    }

                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 图片获取
     **************************************
     */
//    public  function  evaluation_add_img(Request $request){
//        if($request->isPost()){
//            $evaluation_order_id = Session::get('evaluation_order_id');
//            if(!empty($evaluation_order_id)){
//                $evaluation_images = [];
//                $file = $request->file('evaluation_images');
//                foreach ($file as $k=>$v){
//                    $info = $v->move(ROOT_PATH . 'public' . DS . 'upload');
//                    $evaluation_url = str_replace("\\","/",$info->getSaveName());
//                    $evaluation_images[] = ["images"=>$evaluation_url,"evaluate_order_id"=>$evaluation_order_id];
//                }
//                if(!empty($evaluation_images)){
//                    $res = model('evaluate_images')->saveAll($evaluation_images);
//                    if($res)
//                    {
//                        $this->success('评价成功',url('index/Order/evaluate'));
//                    }
//                }else{
//                    $this->error('请上传图片');
//                }
//
//
//            }
//        }
//    }


    /**
     **************李火生*******************
     * @param Request $request
     * 评价发表
     **************************************
     */
    public  function  evaluation_add_img(Request $request){
        if($request->isPost()){
            $evaluation_order_id = Session::get('evaluation_order_id');
            $content =input('evaluation_content');
            if(!empty($evaluation_order_id)){
                $evaluation_images = [];
                $file = $request->file('evaluation_images');
                foreach ($file as $k=>$v){
                    $info = $v->move(ROOT_PATH . 'public' . DS . 'upload');
                    $evaluation_url = str_replace("\\","/",$info->getSaveName());
                    $evaluation_images[] = ["images"=>$evaluation_url,"evaluate_order_id"=>$evaluation_order_id];
                }
//                dump($evaluation_images);exit;


                if(empty($content)){
                    $this->error('请输入评价的内容');
                }
                if(!empty($content)){
                    $member =Session::get('member');
                    $user_id =Db::name('user')->field('id')->where('phone_num',$member['phone_num'])->find();
                    $goods_id =Db::name('order')->field('goods_id,order_information_number')->where('id',$evaluation_order_id)->find();
                    if(!empty($user_id)&&!empty($goods_id)){
                        $datas = [
                            'order_id'=> $evaluation_order_id,
                            'order_information_number'=>$goods_id['order_information_number'],
                            'evaluate_content'=>$content,
                            'goods_id'=>$goods_id['goods_id'],
                            'user_id'=>$user_id['id'],
                            'create_time'=>time(),
                            'status'=>0
                        ];
                        if(!empty($evaluation_images)){
                            $res_boll = model('evaluate_images')->saveAll($evaluation_images);
                        }
                        $res = Db::name('evaluate')->insert($datas);
                        $order_status_check =Db::name('order')->where('id',$evaluation_order_id)->update(['status'=>10]);
                        if($res!==null &&$order_status_check !==null){
                            $this->success('评价成功',url('index/Order/evaluate'));
                        }else{
                            $this->error('评价失败');
                        }
                    }
                }

            }
        }
    }



    /**
     **************李火生*******************
     * @param Request $request
     * 获取需要评价的order_id
     **************************************
     */
    public function evaluation_get_order_id(Request $request){
        if($request->isPost()){
            $order_id =$request->only(["order_id"])['order_id'];
            if(!empty($order_id)){
                    session('evaluation_order_id',$order_id);
                    return ajax_success('成功',$order_id);
            }
        }
    }





}