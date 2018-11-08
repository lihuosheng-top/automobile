<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/10/25
 * Time: 11:22
 */
namespace  app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class User extends Controller{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员首页
     **************************************
     */
    public function index(){
        $user_data =Db::name('user')->order('create_time','desc')->paginate(3);
        return view('index',['user_data'=>$user_data]);
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:后台会员账户状态修改
     **************************************
     */
    public function  status(Request $request){
        if($request->isPost()) {
            $status = $request->only(["status"])["status"];
            if($status == 0) {
                $id = $request->only(["id"])["id"];
                $bool = Db::name("user")->where("id", $id)->update(["status" => 0]);
                if ($bool) {
                    $this->redirect("admin/User/index");
                } else {
                    $this->error("修改失败", url("admin/User/index"));
                }
            }
            if($status == 1){
                $id = $request->only(["id"])["id"];
                $bool = Db::name("user")->where("id", $id)->update(["status" => 1]);
                if ($bool) {
                    $this->redirect("admin/User/index");
                } else {
                    $this->error("修改失败", url("admin/User/index"));
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员编辑
     **************************************
     */
    public function edit($id){
        $user_data = Db::name("user")->where("id",$id)->select();
        return view('edit',['user_data'=>$user_data]);
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:会员删除操作
     **************************************
     * @param $id
     */
    public function del($id){
        $res =Db::name('user')->where('id',$id)->delete();
        if($res){
           $this->success('删除成功','admin/User/index');
        }else{
            $this->error('删除失败','admin/User/index');
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:批量删除
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
            $list =  Db::name('user')->where($where)->delete();
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
     * @param Request $request
     * Notes:会员搜索
     **************************************
     * @param Request $request
     */
    public function search(){
        $keywords =input('search_key');
        $timemin  =strtotime(input('date_min'));
        $timemax  =strtotime(input('date_max'));
        if(!empty($keywords)){
            $condition = " `phone_num` like '%{$keywords}%' or `user_name` like '%{$keywords}%' ";
            if((!empty($timemin))&&(!empty($timemax))){
                $time_condition  = "create_time>{$timemin} and create_time< {$timemax}";
                $user_data=Db::name("user")
                    ->where($condition)
                    ->where($time_condition)
                    ->order('create_time','desc')
                    ->paginate(3 ,false, [
                        'query' => request()->param(),
                    ]);
            }else if((!empty($timemin))&&(empty($timemax)) ||(empty($timemin))&&(!empty($timemax)) ||(empty($timemin))&&(empty($timemax))){
                $user_data=Db::name("user")
                    ->where($condition)
                    ->order('create_time','desc')
                    ->paginate(3 ,false, [
                        'query' => request()->param(),
                    ]);
            }
            if(!empty($data)){
                return view('order_index',['user_data'=>$user_data]);
            }
        }
    }



    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员等级
     **************************************
     */
    public function  grade(){
        $user_grade_data =Db::name('user_grade')->order('grade_id','asc')->select();
        return view('grade',['user_grade_data'=>$user_grade_data]);
    }

}