<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Db;
class Admin extends Controller
{
    /**
     * [管理员列表]
     * 陈绪
     */
    public function index(Request $request){
        $account_list = db("admin")->order("id")->select();
        foreach ($account_list as $key=>$value){
            $account_list[$key]["role_name"] = db("role")->where("id",$value["role_id"])->value("name");
        }
        //halt($account_list);
        $roleList = getSelectList("role");
        return view("index",["account_list"=>$account_list,"roleList"=>$roleList]);
    }

    /**
     * [管理员查询]
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function add(){
        $roles = db("role")->where("status","1")->field("id,name")->select();
        $roleList = getSelectList("role");
        return view("save",["role"=>$roles,"roleList"=>$roleList]);
    }

    /**
     * [管理员添加入库]
     * 陈绪
     */
    public function save(Request $request){
        $data = $request->param();
        $data["passwd"] = password_hash($data["passwd"],PASSWORD_DEFAULT);
        $data["stime"] = date("Y-m-d H:i:s");
        $boolData = model("Admin")->sSave($data);

        if($boolData){
            $this->redirect(url("admin/admin/index"));
        }else{
            $this->redirect(url("admin/admin/add"));
        }
    }

    /**
     * [管理员删除]
     * 陈绪
     */
    public function del($id){
        $bool = model("Admin")->where("id",$id)->delete();
        if($bool){
            $this->redirect(url("admin/admin/index"));
        }else{
            $this->error(url("admin/admin/index"));
        }
    }

    /**
     * [管理员编辑]
     * 陈绪
     */
    public function edit($id){
        $admin = db("Admin")->where("id","$id")->select();
        $roleList = getSelectList("role");

        return view("edit",["admin"=>$admin,"roleList"=>$roleList]);
    }

    /**
     * [管理员修改]
     * 陈绪
     */
    public function updata(Request $request){
        $data = $request->param();
        $data["passwd"] = password_hash($data["passwd"],PASSWORD_DEFAULT);
        $data["stime"] = date("Y-m-d H:i:s");
        $id = $request->only(['id'])['id'];
        $bool = db("Admin")->where('id', $id)->update($data);
        if ($bool){
            $this->success("编辑成功",url("admin/admin/index"));
        }else{
            $this->error("编辑失败",url("admin/admin/edit"));
        }
    }



    /**
     * 管理员状态修改
     * 陈绪
     */
    public function status(Request $request){
        if($request->isPost()) {
            $status = $request->only(["status"])["status"];
            if($status == 0) {
                $id = $request->only(["id"])["id"];
                $bool = db("Admin")->where("id", $id)->update(["status" => 0]);
                if ($bool) {
                    $phone = db("Admin")->field('phone')->where("id", $id)->find();
                  $user_id = Db::name('user')->field('id')->where('phone_num',$phone['phone'])->find();
                  Db::name('store')->where('user_id',$user_id)->update(['operation_status'=>-1]);
                    $this->redirect(url("admin/admin/index"));
                } else {
                    $this->error("修改失败", url("admin/admin/index"));
                }
            }
            if($status == 1){
                $id = $request->only(["id"])["id"];
                $bool = db("Admin")->where("id", $id)->update(["status" => 1]);
                if ($bool) {
                    $phone = db("Admin")->field('phone')->where("id", $id)->find();
                    $user_id = Db::name('user')->field('id')->where('phone_num',$phone['phone'])->find();
                    Db::name('store')->where('user_id',$user_id)->update(['operation_status'=>1]);
                    $this->redirect(url("admin/admin/index"));
                } else {
                    $this->error("修改失败", url("admin/admin/index"));
                }
            }
        }
    }




    /**
     * 密码修改
     * 陈绪
     */
    public function passwd(Request $request){
        $id = $request->only(['id'])['id'];
        $second_passwd =$request->only(['second_passwd'])['second_passwd'];
        $passwd = $request->only(["passwd"])["passwd"];
        $old_password =$request->only(['old_password'])['old_password'];
        if(!empty($old_password)){
            $userInfo = db("admin")->field('passwd')->where("id",$id)->select();
            if (password_verify($old_password , $userInfo[0]["passwd"])) {
                if($second_passwd !=$passwd){
                    $this->error('两次密码不相同');
                }
                $passwords =password_hash(trim($passwd),PASSWORD_DEFAULT);
                $admin_phone =db('admin')->field('phone')->where('id',$id)->find();
                if(!empty($admin_phone)){
                    $user_phone =db('user')->where('phone_num',$admin_phone['phone'])->find();
                    if(!empty($user_phone)){
                        db('user')->where('phone_num',$admin_phone['phone'])->update(['password'=>$passwords]);
                    }
                }
                $bool = db("Admin")->where("id",$id)->update(["passwd"=>$passwords]);
                if($bool){
                    $this->success("修改成功，请重新登录", url("admin/Login/index"));
                }
            }else{
                $this->error('原密码不正确');
            }
        }
    }

}