<?
       $keywords =input('parts_order_number'); //订单编号
       $goods_name =input('goods_name');            //商品名称
       $phone_num =input('phone_num');         //用户账号
       $order_status =input('order_status');   // 订单状态
       $timemin =input('date_min');           //开始时间
       /*添加一天（23：59：59）*/
       $time_max_data =strtotime(input('date_max'));
       $t=date('Y-m-d H:i:s',$time_max_data+1*24*60*60);
       $timemax  =strtotime($t);                       //结束时间

       $order_parts_data =Db::table('tb_order_parts')
           ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
           ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
           ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
           ->order('tb_order_parts.order_create_time','desc')
           ->paginate(3 ,false, [
               'query' => request()->param(),
           ]);

           $order_parts_data =Db::table('tb_order_parts')
           ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
           ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
           ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
           ->where("tb_goods.goods_name","like","%". $goods_name . "%")
           ->where("tb_user.phone_num","like","%". $phone_num . "%")
           ->where("tb_order_parts.status","like","%". $order_status . "%")
           ->where("tb_order_parts.order_create_time","order_create_time>{$timemin} and order_create_time< {$timemax}")
           ->order('tb_order_parts.order_create_time','desc')
           ->paginate(3 ,false, [
               'query' => request()->param(),
           ]);

            if(empty($keywords)){ //空
                
                if ((!empty($goods_name))&& empty($phone_num) && empty($order_status) && empty($timemin) && empty($timemax)) {                               
                    $order_parts_data =Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                    ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                    ->where("tb_goods.goods_name","like","%". $goods_name . "%")
                    ->order('tb_order_parts.order_create_time','desc')
                    ->paginate(3 ,false, [
                        'query' => request()->param(),
                    ]);
                }elseif(empty($goods_name) && (!empty($phone_num))&& empty($order_status) && empty($timemin) && empty($timemax)){
                    $order_parts_data =Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                    ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                    ->where("tb_user.phone_num","like","%". $phone_num . "%")
                    ->order('tb_order_parts.order_create_time','desc')
                    ->paginate(3 ,false, [
                        'query' => request()->param(),
                    ]);
                }elseif(empty($goods_name) && empty($phone_num)&& (!empty($order_status)) && empty($timemin) && empty($timemax)){
                    $order_parts_data =Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                    ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                    ->where("tb_order_parts.status","like","%". $order_status . "%")
                    ->order('tb_order_parts.order_create_time','desc')
                    ->paginate(3 ,false, [
                        'query' => request()->param(),
                    ]);  
                }elseif(empty($goods_name) && empty($phone_num)&& empty($order_status) && (!empty($timemin)) && empty($timemax)){
                    $order_parts_data =Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                    ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                    ->where("tb_order_parts.order_create_time","order_create_time>{$timemin} ")
                    ->order('tb_order_parts.order_create_time','desc')
                    ->paginate(3 ,false, [
                        'query' => request()->param(),
                    ]);
                }elseif(empty($goods_name) && empty($phone_num)&& empty($order_status) && empty($timemin) && (!empty($timemax))){
                    $order_parts_data =Db::table('tb_order_parts')
                    ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                    ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                    ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                    ->where("tb_order_parts.order_create_time","order_create_time< {$timemax}")
                    ->order('tb_order_parts.order_create_time','desc')
                    ->paginate(3 ,false, [
                        'query' => request()->param(),
                    ]);
                }
            }


















                if(!empty($keywords){

                    if (!empty($goods_name) || !empty($phone_num) || !empty($order_status) || !empty($timemin) || !empty($timemax)) {                               
                        $order_parts_data =Db::table('tb_order_parts')
                        ->field("tb_order_parts.*,tb_user.phone_num phone_num,tb_goods.goods_name gname,tb_goods.goods_show_images gimages")
                        ->join("tb_user","tb_order_parts.user_id=tb_user.id",'left')
                        ->join("tb_goods","tb_order_parts.goods_id=tb_goods.id","left")
                        ->where("tb_order_parts.parts_order_number","like","%". $keywords . "%")
                        ->where("tb_goods.goods_name","like","%". $goods_name . "%")
                        ->where("tb_user.phone_num","like","%". $phone_num . "%")
                        ->where("tb_order_parts.status","like","%". $order_status . "%")
                        ->where("tb_order_parts.order_create_time","order_create_time>{$timemin} and order_create_time< {$timemax}")
                        ->order('tb_order_parts.order_create_time','desc')
                        ->paginate(3 ,false, [
                            'query' => request()->param(),
                        ]);
                    }
                }
                        $user = db("user")->paginate(5,false,['query' => request()->param()]);
                        $role_type=Db::name("user")
                            ->where("upcoming_status","like","%". $intere_ste . "%")
                            ->paginate(5 ,false, [
                                'query' => request()->param(),
                            ]);
                    }
                    if($intere_ste==2){
                        $user = db("user")->paginate(5,false,['query' => request()->param()]);
                        $role_type=Db::name("user")
                            ->where("quit_expire_status","like","%". $intere_ste . "%")
                            ->paginate(5 ,false, [
                                'query' => request()->param(),
                            ]);
                    }

                }else if(!empty($intere_st) && !empty($intere_ste)){
                    if($intere_ste==1){
                        $user = db("user")->paginate(5,false,['query' => request()->param()]);
                        $role_type=Db::name("user")
                            ->where("department","like","%". $intere_st . "%")
                            ->where("upcoming_status","like","%". $intere_ste . "%")
                            ->paginate(5 ,false, [
                                'query' => request()->param(),
                            ]);
                    }
                    if($intere_ste==2){
                        $user = db("user")->paginate(5,false,['query' => request()->param()]);
                        $role_type=Db::name("user")
                            ->where("department","like","%". $intere_st . "%")
                            ->where("upcoming_status","like","%". $intere_ste . "%")
                            ->where("quit_expire_status","like","%". $intere_ste . "%")
                            ->paginate(5 ,false, [
                                'query' => request()->param(),
                            ]);
                    }
                }else{
                    $user = db("user")->paginate(5, false,['query' => request()->param()]);
                    $role_type = db("role_type")->select();
                }
                return view("staff_index",["user"=>$user,"role_type"=>$role_type]);
            }
            if (!empty($search_keys)) {
                if (!empty($intere_st)&& empty($interes_ste)) {
                    $user = db("user")->where("name", "like", "%" . $search_keys . "%")->paginate(5, false,['query' => request()->param()]);
                    $role_type=Db::name("user")
                        ->where("department","like","%". $intere_st . "%")
                        ->paginate(5 ,false, [
                            'query' => request()->param(),
                        ]);
                }else if(empty($intere_st) && !empty($intere_ste)){
                    if($intere_ste==1){
                        $user = db("user")->where("name", "like", "%" . $search_keys . "%")->paginate(5, false,['query' => request()->param()]);
                        $role_type=Db::name("user")
                            ->where("upcoming_status","like","%". $intere_ste . "%")
                            ->paginate(5 ,false, [
                                'query' => request()->param(),
                            ]);
                    }
                    if($intere_ste==2){
                        $user = db("user")->where("name", "like", "%" . $search_keys . "%")->paginate(5, false,['query' => request()->param()]);
                        $role_type=Db::name("user")
                            ->where("quit_expire_status","like","%". $intere_ste . "%")
                            ->paginate(5 ,false, [
                                'query' => request()->param(),
                            ]);
                    }
                }else if(!empty($intere_st) && !empty($intere_ste)){
                    if($intere_ste==1){
                        $user = db("user")->where("name", "like", "%" . $search_keys . "%")->paginate(5, false,['query' => request()->param()]);
                        $role_type=Db::name("user")
                            ->where("department","like","%". $intere_st . "%")
                            ->where("upcoming_status","like","%". $intere_ste . "%")
                            ->paginate(5 ,false, [
                                'query' => request()->param(),
                            ]);
                    }
                    if($intere_ste==2){
                        $user = db("user")->where("name", "like", "%" . $search_keys . "%")->paginate(5, false,['query' => request()->param()]);
                        $role_type=Db::name("user")
                            ->where("department","like","%". $intere_st . "%")
                            ->where("quit_expire_status","like","%". $intere_ste . "%")
                            ->paginate(5 ,false, [
                                'query' => request()->param(),
                            ]);
                    }
                }else{
                    $user = db("user")->where("name", "like", "%" . $search_keys . "%")->paginate(5, false,['query' => request()->param()]);
                   //var_dump($user);
                   $role_type = db("role_type")->select();
                   //var_dump ($role_type);
        }

                return view("staff_index",["user"=>$user,"role_type"=>$role_type]);
            }

        