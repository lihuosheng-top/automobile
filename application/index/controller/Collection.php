<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17 0017
 * Time: 16:51
 */
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
class  Collection extends Controller{


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:我的收藏
     **************************************
     */
    public function collection_index(){
        $user_id =Session::get("user");
        if(!empty($user_id)){
            $data=Db::table("tb_collection")
                ->field("tb_collection.*,tb_goods.goods_adjusted_money goods_adjusted_money,tb_goods.goods_name goods_name,tb_goods.goods_show_images goods_show_images,tb_goods.goods_selling goods_selling")
                ->join("tb_goods","tb_collection.goods_id=tb_goods.id and tb_collection.user_id=$user_id",'left')
                ->order('id','desc')
                ->where('tb_collection.user_id',$user_id)
                ->select();
            if(!empty($data)){
                return ajax_success('返回成功',$data);
            }else{
                return ajax_error('失败',["status"=>0]);
            }
        }else{
            return ajax_error('请登陆',["status"=>0]);
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:添加收藏
     **************************************
     * @param Request $request
     */
    public function  collection_add(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");
            if(!empty($user_id)){
                $datas =$request->only("id")["id"];
                if(!empty($datas)){
                    $goods_id = $datas;//商品id
                    $history_res =Db::name('collection')->where('user_id',$user_id)->where('goods_id',$goods_id)->find();
                    if($history_res){
                        $res = Db::name('collection')->where('user_id',$user_id)->where('goods_id',$goods_id)->delete();
                        if($res)
                        {
                            return ajax_success('取消收藏成功');
                        }
                    }
                    /*当同一个商品被同一个人再次收藏的时候就变为取消收藏*/
                    if(!$history_res){
                        $data =[
                            'user_id'=>$user_id,
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


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:收藏删除
     **************************************
     * @param Request $request
     */
    public function collection_del(Request $request){
        if($request->isPost()){
            $id =$_POST['id'];
            $user_id =Session::get("user");
            if(is_array($id)){
                $where ='id in('.implode(',',$id).')';
            }else{
                $where ='id='.$id;
            }
            $list =  Db::name('collection')->where($where)->where("user_id",$user_id)->delete();
            if($list!==false)
            {
                return ajax_success('删除成功!',$list);
            }else{
                return ajax_error('删除失败',$list);
            }
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:收藏样式返回的数据库状态值
     **************************************
     * @param Request $request
     */
    public function show_collection(Request $request){
        if($request->isPost()){
            $data = $_POST;
            $user_id =Session::get("user");
            if(empty($user_id)){
               return ajax_error("用户未登陆",["status"=>0]);
            }
            if(!empty($data)){
                $goods_id = $data['id'];
                if(!empty($goods_id)&&!empty($user_id)){
                    $data =[
                        'user_id'=> $user_id,
                        'goods_id'=>$goods_id,
                        'status'=>1
                    ];
                    $see_status = Db::name('collection')->field('status')->where($data)->find();
                    if($see_status){
                        return ajax_success('有收藏',$see_status);
                    }else{
                        return ajax_error('没有收藏',['status'=>0]);
                    }
                }
            }
        }
    }
}