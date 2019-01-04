<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4 0004
 * Time: 12:03
 */

namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;

class Complaint extends Controller{

    /**
     **************陈绪*******************
     * @param Request $request
     * Notes:投诉中心
     **************************************
     * @return \think\response\View
     */
    public function index(Request $request){
        if($request->isPost()){
            $issue = $request->param();
            $images = $request->file("images");
            if (!empty($images)) {
                $issue_images = [];
                foreach ($images as $value) {
                    $image = $value->move(ROOT_PATH . 'public' . DS . 'uploads');
                    $issue_images[] = str_replace("\\", "/", $image->getSaveName());
                }
                $issue["images"] = implode(",",$issue_images);
            }
            $user_id = Session::get("user");
            $issue["user_id"] = $user_id;
            $issue["status"] = 0;
            $bool = db("complaint")->insert($issue);
            if($bool){
                return ajax_success("存储成功");
            }else{
                return ajax_error("存储失败");
            }

        }
        return view('index');
    }


    /**
     **************陈绪*******************
     * @param Request $request
     * Notes:投诉详情
     **************************************
     * @return \think\response\View
     */
    public function detail(Request $request){

        if($request->isPost()){
            $issue = db("complaint")->select();
            if($issue){
                return ajax_success("获取成功",$issue);
            }else{
                return ajax_error("获取失败");
            }
        }
        return view('detail');
    }
}