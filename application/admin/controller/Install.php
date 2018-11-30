<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/22
 * Time: 11:25
 */
namespace app\admin\controller;
use app\admin\model\IntegralDiscountSettings;
use app\admin\model\RechargeSetting;
use think\Controller;
use think\Db;
use think\Request;

class Install extends Controller{

    /**
     * 价格调整设置
     * 陈绪
     * @return
     */
    public function index(Request $request){

        if($request->isPost()){
            $min_money = $request->only(["min_money"])["min_money"];
            $max_money = $request->only(["max_money"])["max_money"];
            $rati = $request->only(["ratio"])["ratio"];
            foreach ($min_money as $key=>$value){
                $ratio = (float)$rati[$key]/100;
                $bool = db("goods_ratio")->insert(["min_money"=>$value,"max_money"=>$max_money[$key],"ratio"=>$ratio]);
            }
            if($bool){
                $this->success("添加成功",url("admin/Install/service_index"));
            }
        }
        $goods_ratio = db("goods_ratio")->select();
        return view("install_index",["goods_ratio"=>$goods_ratio]);

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
//            $consumption_full =$request->only(['consumption_full'])['consumption_full'];
//            $integral_can_be_used =$request->only(['integral_can_be_used'])['integral_can_be_used'];
//            $integral_full =$request->only(['integral_full'])['integral_full'];
//            $deductible_money =$request->only(['deductible_money'])['deductible_money'];
//            $setting_describe ='消费满'.$consumption_full.'元可使用'.$integral_can_be_used.'积分，'.$integral_full.'积分抵'.$deductible_money.'元';

            if(empty($consumption_full) ||empty($integral_can_be_used) ||empty($integral_full)||empty($deductible_money) ){
                $this->error('所添加的值不能为空');
            }
                $settings_table= new IntegralDiscountSettings();
                $datas =$settings_table->isUpdate(false)->insert($data);

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
            $year = $request->only(["year"])["year"];
            $money = $request->only(["money"])["money"];
            foreach ($year as $key => $value) {
                $bool = db("year")->insert(["year" => $value, "money" => $money[$key]]);
            }
            if ($bool) {
                $this->success("添加成功", url("admin/Home/index"));
            }
        }
        $year = db("year")->select();
        return view("putaway_index",["year"=>$year]);

    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:设置之充值设置
     **************************************
     * @return \think\response\View
     */
    public function recharge(){
        $recharge_data =Db::name('recharge_setting')->select();
        return view("recharge_index",['recharge_data'=>$recharge_data]);
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:充值设置添加数据
     **************************************
     * @param Request $request
     */
    public function  recharge_setting_add(Request $request){
        if($request->isPost()){
            $data =input();
            if(empty($data)){
                $this->error('所添加的值不能为空');
            }
            $settings_table= new RechargeSetting();
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
     * Notes:设置充值设置删除功能
     **************************************
     * @param Request $request
     */
    public function recharge_setting_del(Request $request){
        if($request->isPost()){
            $setting_id =$_POST['id'];
            if(!empty($setting_id)){
                $data_bool =Db::name('recharge_setting')->where('setting_id',$setting_id)->delete();
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
     **************李火生*******************
     * @param Request $request
     * Notes:设置之项目服务项目设置
     **************************************
     * @return \think\response\View
     */
    public function service_index(){
        $service_data =Db::name('service_setting')->order('service_setting_id')->select();
        return view("service_index",['service_data'=>$service_data]);

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
//    public function service_save(){
//
//
//
//    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:设置之服务项目设置编辑功能
     **************************************
     * @return \think\response\View
     */
    public function service_edit($id =null){
        if($id > 0){
            $info =Db::name('service_setting')->where("service_setting_id",$id)->find();
            $this->assign('info',$info);
        }
        if($this->request->isPost()){
            $data =$this->request->post();
            $data['service_setting_time'] =time();
            $file =$this->request->file("service_setting_calss_img");
            if($file){
                $datas = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $images_url = str_replace("\\","/",$datas->getSaveName());
                $data["service_setting_calss_img"] =$images_url;
            }
            if($id>0){
                $res =Db::name('service_setting')->where('service_setting_id',$id)->update($data);
            }else{
                $res =Db::name('service_setting')->insertGetId($data);
            }
            if($res>0){
                 $this->success('编辑成功','admin/Install/service_index');
            }else{
                 $this->error('编辑失败');
            }
        }
        return view("service_edit");

    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:设置之服务项目设置(图片删除)
     **************************************
     * @return \think\response\View
     */
    public function  service_image_del(Request $request){
        if ($request->isPost()) {
            $id = $request->only(['id'])['id'];
            $image_url = db('service_setting')->where("service_setting_id", $id)->field("service_setting_calss_img")->find();
            if ($image_url["service_setting_calss_img"] != null) {
                unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $image_url["service_setting_calss_img"]);
            }
            $bool = Db::name('service_setting')->where("service_setting_id", $id)->field("service_setting_calss_img")->update(["service_setting_calss_img" => null]);
            if ($bool) {
                return ajax_success("删除成功");
            } else {
                return ajax_error("删除失败");
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:设置之服务项目设置列表删除
     **************************************
     * @param $id
     */
    public function service_del($id){
        $bool = Db::name("service_setting")->where("service_setting_id", $id)->delete();
        if ($bool) {
            $this->success("删除成功", 'admin/Install/service_index');
        } else {
            $this->error("删除失败", 'admin/Install/service_index');
        }
    }



    /**
     * 服务更新
     * 陈绪
     */
    public function service_updata(){



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



    /**
     * 快递员列表
     * 陈绪
     */
    public function express_index(){

        return view("express_index");

    }




    /**
     * 快递员添加
     * 陈绪
     */
    public function express_add(){

        return view("express_add");

    }




    /**
     * 快递员入库
     * 陈绪
     */
    public function express_save(){



    }



    /**
     * 快递员添加
     * 陈绪
     */
    public function express_edit(){

        return view("express_edit");

    }
}