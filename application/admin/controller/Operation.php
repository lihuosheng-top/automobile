<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/7
 * Time: 11:33
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;

class Operation extends Controller{


    /**
     * 投诉中心
     * 陈绪
     */
    public function complaint_index(){
        return view("complaint_index");
    }



    /**
     * 紧急管理
     * 陈绪
     */
    public function urgency_index(){

        $rescue = db("rescue")->paginate(20);
        return view("urgency_index",["rescue"=>$rescue]);

    }



    /**
     * 紧急救援修改
     * 陈绪
     */
    public function urgency_updata(Request $request){

        $id = $request->only(["id"])["id"];
        $data = $request->param();
        unset($data["id"]);
        $bool = db("rescue")->where("id",$id)->update($data);
        if($bool){
            $this->success("修改成功",url("admin/Operation/urgency_index"));
        }else{
            $this->error("修改失败",url("admin/Operation/urgency_index"));
        }
    }



    /**
     * 紧急救援处理详情查看
     * 陈绪
     */
    public function urgency_select(Request $request){

        if($request->isPost()){
            $id = $request->only(["id"])["id"];
            $user_dispose = db("rescue")->where("id",$id)->field("user_dispose")->select();
            return ajax_success("获取成功",$user_dispose);
        }

    }



    /**
     * 紧急修远删除
     * 陈绪
     */
    public function urgency_del(Request $request){

        $id = $request->only(["id"])["id"];
        $bool = db("rescue")->where("id",$id)->delete();
        if($bool){
            $this->success("删除成功",url("admin/Operation/urgency_index"));
        }else{
            $this->error("删除失败",url("admin/Operation/urgency_index"));
        }


    }

}