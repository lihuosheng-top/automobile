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
use think\paginator\driver\Bootstrap;

class Operation extends Controller{


    /**
     * 投诉中心
     * 陈绪
     */
    public function complaint_index(){

        $issue = db("complaint")->select();
        foreach ($issue as $key=>$value){
            $issue[$key]["user"] = db("user")->where("id",$value["user_id"])->find();
            $issue[$key]["images"] = explode(",",$value["images"]);
        }
        $all_idents = $issue;//这里是需要分页的数据
        $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
        $listRow = 20;//每页20行记录
        $showdata = array_slice($all_idents, ($curPage - 1) * $listRow, $listRow, true);// 数组中根据条件取出一段值，并返回
        $platform = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path' => url('admin/operation/complaint_index'),//这里根据需要修改url
            'query' => [],
            'fragment' => '',
        ]);
        $platform->appends($_GET);
        $this->assign('platforme', $platform->render());
        return view("complaint_index",["issue"=>$issue]);
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
            $this->success("修改成功",url("admin/operation/urgency_index"));
        }else{
            $this->error("修改失败",url("admin/operation/urgency_index"));
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
            $this->success("删除成功",url("admin/operation/urgency_index"));
        }else{
            $this->error("删除失败",url("admin/operation/urgency_index"));
        }


    }




    /**
     * 投诉中心修改
     * 陈绪
     */
    public function complaint_updata(Request $request){

        $id = $request->only(["id"])["id"];
        $data = $request->param();
        unset($data["id"]);
        $bool = db("complaint")->where("id",$id)->update($data);
        if($bool){
            $this->success("修改成功",url("admin/operation/complaint_index"));
        }else{
            $this->error("修改失败",url("admin/operation/complaint_index"));
        }
    }



    /**
     * 投诉中心处理详情查看
     * 陈绪
     */
    public function complaint_select(Request $request){

        if($request->isPost()){
            $id = $request->only(["id"])["id"];
            $user_dispose = db("complaint")->where("id",$id)->field("issue_remark")->select();
            return ajax_success("获取成功",$user_dispose);
        }

    }



    /**
     * 投诉中心删除
     * 陈绪
     */
    public function complaint_del(Request $request){

        $id = $request->only(["id"])["id"];
        $bool = db("complaint")->where("id",$id)->delete();
        if($bool){
            $this->success("删除成功",url("admin/operation/complaint_index"));
        }else{
            $this->error("删除失败",url("admin/operation/complaint_index"));
        }


    }
}