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
            if(!empty($images)){
                foreach ($images as $value){
                    $image = $value->move(ROOT_PATH . 'public' . DS . 'uploads');
                    $issue["images"] = explode(",",str_replace("\\", "/", $image->getSaveName()));
                    halt($issue["images"]);
                }

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
    public function detail(){
        return view('detail');
    }
}