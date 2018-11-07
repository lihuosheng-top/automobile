<?php
namespace app\index\controller;

use think\Controller;

class Cart extends Controller
{
    public function Cart_index()
    {
        return view("cart_index");
    }
}
