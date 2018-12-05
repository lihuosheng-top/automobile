<?php


//上架年限
function year($arr){
    echo substr($arr,0,1);
}



//付费金额
function pay_money($arr){
    echo substr($arr,4,2);
}

//通过后台登录获取admin表id进行判断属于什么
function get_user_id_by_session(){
    $session_user_id =session('user_id');
    dump($session_user_id);
}


