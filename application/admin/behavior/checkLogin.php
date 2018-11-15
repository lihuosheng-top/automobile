<?php
/**
 * Created by PhpStorm.
 * User: CHEN
 * Date: 2018/7/15
 * Time: 18:52
 */

namespace app\admin\behavior;
use think\Controller;
use think\Request;
use think\Session;
use think\Config;

class checkLogin extends Controller {
    use \traits\controller\Jump;
    public function run(){
        $arr = request()->routeInfo();
        if(!preg_match("/admin\/Login/",$arr["route"])) {
            $data = Session::get("user_id");
            if (empty($data)) {
                $this->error("请登录", url("/admin/index", "", false));
                exit();
            }
            $user_info = Session::get("user_info");
            $menu_list = db("menu")->where('status', '<>', 0)->select();
            $role = db("role")->where("id", $user_info[0]['role_id'])->field("menu_role_id")->select();
            $role = explode(",", $role[0]["menu_role_id"]);
            //在控制台获取当前的url地址
            $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['REQUEST_URI'];
            //halt($url);

            $explode = explode("/", $url);
            if (count($explode) > 3) {
                $url = "/" . $explode[1] . "/" . $explode[2];
            }
            $if_url = 0;
            foreach ($menu_list as $key => $values) {
                if (!in_array($values['id'], $role)) {
                    unset($menu_list[$key]);
                } else {
                    if ($values['url'] == $url) {
                        $if_url = 1;
                    }
                }
            }


            //权限检测
            $arr_data = Request::instance()->routeInfo();
            if(!empty($arr_data["rule"][1])){
                $controller = $arr_data['rule'][0];
                $action = $arr_data['rule'][1];
                $url = "/".$controller."/".$action;
                $menu_id = db("menu")->where("url",$url)->field("id")->find();
                if(!empty($menu_id)) {
                    if (in_array($menu_id["id"], $role)) {
                        return 1;
                    } else {
                        echo "<script>alert('无权限')</script>";
                        echo "<script>window.history.back(-1)</script>";
                        exit();
                    }
                }
            }

            $user_id = Session::get("user_id");
            $admin = db("admin")->where("id",$user_id)->find();
            config("admin",$admin);

            $menu_list = _tree_hTree(_tree_sort($menu_list, "sort_number"));
            config("menu_list", $menu_list);
            //halt(Config::get("menu_list"));
        }
    }

}