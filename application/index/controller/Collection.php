<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17 0017
 * Time: 16:51
 */
namespace app\admin\controller;


use think\Controller;

class  Collection extends Controller{

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:添加收藏
     **************************************
     * @param Request $request
     */
    public function  add(Request $request){
        if($request->isPost()){
            $member = Session::get('member');
            if(!empty($member)){
                $datas =$_POST;
                if(!empty($datas)){
                    $member_data = Db::name('user')->field('id')->where('phone_num',$member['phone_num'])->find();
                    $member_id = $member_data['id'];
                    $goods_id = $datas['id'];
                    $history_res =Db::name('collection')->where('user_id',$member_id)->where('goods_id',$goods_id)->find();
                    if($history_res){
                        $res = Db::name('collection')->where('user_id',$member_id)->where('goods_id',$goods_id)->delete();
                        if($res)
                        {
                            return ajax_success('取消收藏成功');
                        }
                    }
                    /*当同一个商品被同一个人再次收藏的时候就变为取消收藏*/
                    if(!$history_res){
                        $data =[
                            'user_id'=>$member_id,
                            'goods_id'=>$goods_id,
                            'status'=>1
                        ];
                        $res = Db::name('collection')->insert($data);
                        $see_status = Db::name('collection')->field('status')->where($data)->find();
                        if($res&&$see_status){
                            return ajax_success('收藏成功',$res);
                        }else{
                            return ajax_error('收藏失败',['status'=>0]);
                        }

                    }
                }

            }
        }
    }



}