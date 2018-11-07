<?php
namespace app\index\controller;

use think\Controller;

class My extends Controller
{
    public function My_index()
    {
        return view("my_index");
    }
}
