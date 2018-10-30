<?php
namespace app\index\controller;

use think\Controller;

class Classify extends Controller
{
    // 分类
    public function Classify_index()
    {
        return view("classify_index");
    }

    // 分类推荐
    public function Classify_recommend()
    {
        return view("classify_recommend");
    }
}
