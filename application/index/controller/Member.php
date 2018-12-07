<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4 0004
 * Time: 11:35
 */
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Session;

class  Member extends Controller{
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:会员权益
     **************************************
     * @return \think\response\View
     */
    public function member_equity(){
        return view('member_equity');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:地址管理
     **************************************
     * @return \think\response\View
     */
    public function member_address(){
        return view('member_address');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:所有地址列表数据返回
     **************************************
     */
    public function member_address_information(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");
            $data =Db::name("user_address")->where('user_id',$user_id)->select();
            if(!empty($data)){
                return ajax_success('地址列表信息',$data);
            }else{
                return ajax_error('没有填写地址记录',['status'=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:收货地址添加
     **************************************
     * @param Request $request
     */
    public function member_address_adds(Request $request){
        if($request->isPost()){
            $user_id = Session::get("user");
            $harvester = $request->only('harvester')['harvester'];
            $harvester_phone_num = $request->only('harvester_phone_num')['harvester_phone_num'];
            $address_name = $request->only('address_name')['address_name'];
            $status = $request->only('status')['status'];
            $harvester_real_address =$request->only('harvester_real_address')['harvester_real_address'];
            $data =[
                "user_id"=>$user_id,
                "harvester"=>$harvester,
                "harvester_phone_num"=>$harvester_phone_num,
                "address_name"=>$address_name,
                "status"=> $status,
                "harvester_real_address"=>$harvester_real_address
            ];
            $bool_id =Db::name("user_address")->insertGetId($data);
            if($bool_id){
                if($status==1){
                    Db::name('user_address')->where('user_id',$user_id)->where('id','NEQ',$bool_id)->update(['status'=>-1]);
                }
                return ajax_success("添加成功",$bool_id);
            }else{
                return ajax_error("添加失败",['status'=>0]);
            }


        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:删除
     **************************************
     * @param Request $request
     */
    public function member_address_del(Request $request){
        if($request->isPost()){
            $id =$request->only('id')['id'];
            if($id){
                $bool =Db::name('user_address')->bind(["id"=>[$id,\PDO::PARAM_INT]])->delete();
                if($bool){
                    return ajax_success('删除成功',['status'=>1]);
                }else{
                    return ajax_error('删除失败',['status'=>0]);
                }
            }else{
                return ajax_error('这条地址信息不正确',['status']);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:编辑地址数据返回
     **************************************
     * @param Request $request
     */
    public function member_address_edit_information(Request $request){
        if($request->isPost()){
            $id = Session::get('address_id');
            $data =Db::name("user_address")->where('id',$id)->find();
            if(!empty($data)){
                Session::delete("address_id");
                return ajax_success('地址信息返回成功',$data);
            }else{
                return ajax_error('地址信息返回失败',['status'=>0]);
            }
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:编辑地址点击一个id传给我
     **************************************
     * @param Request $request
     */
    public function member_save_address_id(Request $request){
        if($request->isPost()){
            $id =$request->only(['id'])['id'];
            if(!empty($id)){
                Session::set('address_id',$id);
                return ajax_success('保存地址id成功',['status'=>1]);
            }else{
                return ajax_error('没有这条地址',['status'=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:收货地址编辑
     **************************************
     * @param Request $request
     */
    public function member_address_edit(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");
            $id = $request->only('id')['id'];
            $harvester = $request->only('harvester')['harvester'];
            $harvester_phone_num = $request->only('harvester_phone_num')['harvester_phone_num'];
            $address_name = $request->only('address_name')['address_name'];
            $status = $request->only('status')['status'];
            $harvester_real_address =$request->only('harvester_real_address')['harvester_real_address'];
            $data =[
                "harvester"=>$harvester,
                "harvester_phone_num"=>$harvester_phone_num,
                "address_name"=>$address_name,
                "status"=> $status,
                "harvester_real_address"=>$harvester_real_address
            ];
            $bool_id =Db::name("user_address")->where('id',$id)->update($data);
            if($bool_id){
                if($status==1){
                    Db::name('user_address')->where('user_id',$user_id)->where('id','NEQ',$id)->update(['status'=>-1]);
                }
                return ajax_success("编辑成功",$bool_id);
            }else{
                return ajax_error("编辑失败",['status'=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:地址管理添加
     **************************************
     * @return \think\response\View
     */
    public function member_address_add(){
        return view('member_address_add');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:设置默认地址
     **************************************
     * @param Request $request
     */
    public function member_address_status(Request $request){
        if($request->isPost()){
            $user_id =Session::get("user");
            $id =$request->only('id')['id'];
            if(!empty($id)){
              $bool=  Db::name('user_address')->where("user_id",$user_id)->where("id","EQ",$id)->update(['status'=>1]);
              if($bool){
                  Db::name('user_address')->where("user_id",$user_id)->where("id","NEQ",$id)->update(['status'=>-1]);
                  return ajax_success("设置成功",['status'=>1]);
              }else{
                  return ajax_error('设置失败',['status'=>0]);
              }

            }
        }
    }




    /**
     **************李火生*******************
     * @param Request $request
     * Notes:我的收藏
     **************************************
     * @return \think\response\View
     */
    public function member_collection(){
        return view('member_collection');
    }
}