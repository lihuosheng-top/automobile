<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/22
 * Time: 11:25
 */
namespace app\admin\controller;
use app\admin\model\IntegralDiscountSettings;
use think\Controller;
use think\Db;
use think\Request;

class Install extends Controller{

    /**
     * 价格调整设置
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index(){

        return view("install_index");

    }



    /**
     * 推荐奖励积分设置
     ***** GY *****
     */
    public function recommend(){
        $recommend_data =Db::name('recommend_integral')->select();
        return view("recommend_index",['recommend_data'=>$recommend_data]);
    }

    /**
     * 推荐奖励积分设置更新
     ***** GY *****
     *
     */
    public function recommend_update(Request $request)
    {
        if($request->isPost()){
            $recommend_peoples = input("recommend_peoples");
            $recommend_integral = input("recommend_integral");
            $recommend_id = input("recommend_id");
            if(!empty($recommend_id)){
                $data_bool =Db::name('recommend_integral')->where('recommend_id',$recommend_id)->update(["number_peoples"=>$recommend_peoples,"recommend_integral"=>$recommend_integral]);
                if($data_bool){
                    return ajax_success('更新成功',['status'=>1]);
                }else{
                    return ajax_error('更新失败',['status'=>0]);
                }
            }else{
                return ajax_error('没有这条数据',['status'=>0]);
            }
        }

    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:积分折扣设置
     **************************************
     * @return \think\response\View
     */
    public function integral(){
        $integral_data =Db::name('integral_discount_settings')->select();
        return view("integral_index",['integral_data'=>$integral_data]);

    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:积分折扣设置添加
     **************************************
     * @param Request $request
     */
    public function  integral_setting_add(Request $request){
        if($request->isPost()){
            $data =input();
            if(empty($data)){
                $this->error('所添加的值不能为空');
            }
                $settings_table= new IntegralDiscountSettings();
                $datas =$settings_table->isUpdate(false)->save($data);
            if(!empty($datas)){
                $this->success('添加成功');
            }else{
                $this->success('添加失败');
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:积分折扣设置删除
     **************************************
     * @param Request $request
     */
    public function  integral_setting_del(Request $request){
        if($request->isPost()){
            $setting_id =$_POST['id'];
            if(!empty($setting_id)){
                $data_bool =Db::name('integral_discount_settings')->where('setting_id',$setting_id)->delete();
                if($data_bool){
                    return ajax_success('删除成功',['status'=>1]);
                }else{
                    return ajax_error('删除失败',['status'=>0]);
                }
            }else{
                return ajax_error('没有这条数据',['status'=>0]);
            }
        }
    }





    /**
     * 上架年限设置
     * 陈绪
     */
    public function putaway(Request $request){

        if($request->isPost()) {
            $data = $request->param();
            halt($data);
            $year = $request->only(["year"])["year"];
            $money = $request->only(["money"])["money"];
            foreach ($year as $key => $value) {
                $bool = db("year")->insert(["year" => $value, "money" => $money[$key]]);
            }
            if ($bool) {
                $this->success("添加成功", url("admin/Home/index"));
            }
        }
        return view("putaway_index");

    }



    /**
     * 充值设置
     * 陈绪
     */
    public function recharge(){

        return view("recharge_index");

    }


    /**
     * 服务显示
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function service_index(){

        return view("service_index");

    }




    /**
     * 服务添加
     * 陈绪
     */
    public function service_add(){

        return view("service_add");

    }




    /**
     * 服务入库
     * 陈绪
     */
    public function service_save(){



    }



    /**
     * 服务编辑
     * 陈绪
     */
    public function service_edit(){

        return view("service_edit");

    }




    /**
     * 服务更新
     * 陈绪
     */
    public function service_updata(){



    }



    /**
     * 服务删除
     * 陈绪
     */
    public function service_del(){



    }



    /**
     * 消息显示
     * 陈绪
     */
    public function message_index(){

        return view("message_index");

    }



    /**
     * 消息添加
     * 陈绪
     */
    public function message_save(){



    }



    /**
     * 消息删除
     * 陈绪
     */
    public function message_del(){



    }

}