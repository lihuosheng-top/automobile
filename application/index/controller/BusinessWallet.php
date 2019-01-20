<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/20 0020
 * Time: 10:23
 */
namespace app\index\controller;

use think\Controller;

class BusinessWallet extends  Controller{

    public function test(){
        echo date('Y-m-d H:i:s');
        //2011-12-26 00:00:00
        $two_weekds_ago = mktime(0,0,0,date("m"),date("d")-14,date("Y"));
        $two_weekds_ago = strftime("%Y-%m-%d %X", $two_weekds_ago);
        echo $two_weekds_ago;
    }


}