<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/3
 * Time: 10:50
 */
namespace app\index\controller;
use think\Controller;

class Wallet extends Controller{


    /**
     * 钱包首页
     * 陈绪
     */
    public function index(){
        
        return view("wallet_index");
        
    }
    
    
    
    /**
     * 钱包充值
     * 陈绪
     */
    public function recharge(){
        
        return view("wallet_recharge");
        
    }
}