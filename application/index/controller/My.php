<?php
namespace app\index\controller;

use think\Controller;

class My extends Controller
{
    public function My_index()
    {
        return view("my_index");
    }
    // 登录
    public function Login()
    {
        return view("login");
    }

    // 设置
    public function Setting()
    {
        return view("setting");
    }
}
