<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;


/**
 * [前端路由]
 * 陈绪
 */
Route::group("",[
    /*首页*/
    "/$"=>"index/index/home",
    "index"=>"index/index/index",
    "saoma_callback"=>"index/index/saoma_callback",
    "weixin_notify"=>"index/index/weixin_notify",




    /*我的爱车*/
    "love_car"=>"index/LoveCar/love_car",
    "love_list"=>"index/LoveCar/love_list",                         //我的爱车列表
    "love_save"=>"index/LoveCar/love_save",                         //我的爱车入库
    "love_status"=>"index/LoveCar/love_status",                         //我的爱车状态修改
    "love_del"=>"index/LoveCar/love_del",                         //我的爱车列表删除
    "love_list_save"=>"index/LoveCar/love_list_save",                         //我的车辆信息添加，修改


    /*服务商品*/
    "service_type"=>"index/reservation/service_type",//选择服务类型
    "reservation"=>"index/reservation/reservation",//预约服务 首页
    "reservation_detail"=>"index/reservation/reservation_detail",//预约服务 详情
    "reservation_info"=>"index/reservation/reservation_info",//预约服务 详情


    /*服务订单*/
    "shop_order"=>"index/OrderService/shop_order",//预约服务 详情


    /*TODO:服务商订单开始*/
    "notifyurl"=>"index/Apppay/notifyurl",//异步处理(支付宝IOS对接)
    "ios_api_order_service_button"=>"index/OrderService/ios_api_order_service_button",//os提交订单传过来的参数形成订单存库并返回对应的订单号给IOS
    "ios_api_alipay"=>"index/OrderService/ios_api_alipay",//生成支付宝签名 TODO:支付宝签名
    "ios_return_num"=>"index/OrderService/ios_return_num",//生成订单(未用)
    'index_aliPay'=>"index/Apppay/index_aliPay", //服务商提交支付（付款）（弹窗支付）
    'index_pay_code'=>"index/Apppay/index_pay_code", //状态修改(回调地址修改状态)
    /*TODO:服务商订单结束*/

    /*TODO:配件商订单开始*/
    "parts_notifyurl"=>"index/Apppay/parts_notifyurl",//异步处理(支付宝IOS对接)
    "ios_api_order_parts_button"=>"index/OrderParts/ios_api_order_parts_button",//os提交订单传过来的参数形成订单存库并返回对应的订单号给IOS
//    "ios_api_alipay"=>"index/OrderParts/ios_api_alipay",//生成支付宝签名 TODO:支付宝签名
   "ios_return_parts_num"=>"index/OrderParts/ios_return_parts_num",//生成订单(未用)
    'index_parts_aliPay'=>"index/Apppay/index_parts_aliPay", //配件商支付接口（弹窗支付）
    'index_parts_pay_code'=>"index/Apppay/index_parts_pay_code",//配件商支付回调修改订单状态
    /*TODO:配件商订单结束*/

    /*TODO：登录开始*/
    'Dolog'=>"index/Login/Dolog", /*登录操作*/
    "logout"=>"index/Login/logout",/*退出登录*/
    "my_index"=>"index/My/my_index",  // 我的
    "my_message"=>"index/My/message", //我的个人信息
    "login"=>"index/My/login",//登录
    "isLogin"=>"index/My/isLogin", //是否登录判断
    "phone_edit"=>"index/My/phone_edit",        //手机号码修改
    "true_name"=>"index/My/true_name",          //真实姓名
    "member_information_update"=>"index/My/member_information_update",//个人信息用户个人信息更新（头像,真实姓名，昵称，性别）
    "member_information_data"=>"index/My/member_information_data",//个人信息用户个人信息返回（头像,真实姓名，昵称，性别）
    "member_update_mobiles"=>"index/My/member_update_mobiles",//个人信息修改手机
    "my_integral"=>"index/My/integral",            //我的积分页面(积分记录ajax)
    "setting"=>"index/My/setting",              //设置(所有页面)
    "is_business"=>"index/My/is_business",      //判断是否是商家还是只是车主（ajax）（隐藏切换角色的按钮）
    "select_role_owner"=>"index/My/select_role_owner",      //选择车主）通过判断是否是商家或者是车主（ajax）
    "select_role_business"=>"index/My/select_role_business",      //（选择商家）通过判断是否是商家或者是车主（ajax）
    "select_role_get"=>"index/My/select_role_get",//获取商家的信息，如果存在则是商家角色，不存在则为车主
    "my_nickname"=>"index/My/nickname",         //昵称
    "my_consume"=>"index/My/consume",         //我的消费
    "consume_message"=>"index/My/consume_message",       //消费详情
    /*TODO:登录结束*/

    /*TODO:经纬度开始*/
    "lglt_save"=>"index/LgLt/save",//经纬度刷新保存
    "lglt_read"=>"index/LgLt/read",//经纬度读取

    /*TODO:经纬度结束*/

    /*TODO:卖家我的页面开始*/
    "sell_my_index"=>"index/SellMy/sell_my_index",//卖家我的页面
    "sell_service_order"=>"index/SellMy/sell_service_order",//卖家服务订单
    "sell_parts_order"=>"index/SellMy/sell_parts_order",//卖家商品订单
    "sell_service_order_detail"=>"index/SellMy/sell_service_order_detail",//卖家服务订单详情
    "sell_parts_order_detail"=>"index/SellMy/sell_parts_order_detail",//卖家商品订单详情

    "sell_service_record"=>"index/SellMy/sell_service_record",//卖家服务记录
    "sell_parts_record"=>"index/SellMy/sell_parts_record",//卖家商品记录
    "sell_order_bill"=>"index/SellMy/sell_order_bill",//卖家账单
    "sell_wallet"=>"index/SellMy/sell_wallet",//卖家钱包
    "sell_application"=>"index/SellMy/sell_application",//卖家提现申请
    /*TODO:卖家我的页面结束*/





    /*TODO:钱包开始*/
    "wallet_index"=>"index/wallet/index",           //钱包首页（ajax）
    "wallet_recharge"=>"index/wallet/recharge",     //钱包充值
    "wallet_block"=>"index/wallet/block",           //添加银行卡
    "wallet_verification"=>"index/wallet/verification",   //银行卡验证
    /*TODO:钱包结束*/



    /*TODO:注册开始*/
    "register"=>"index/Register/index",//注册页面
    "sendMobileCode"=>"index/Register/sendMobileCode",//注册验证码获取接口
    "doRegByPhone"=>"index/Register/doRegByPhone",//注册操作
    /*TODO:注册结束*/

    /*TODO:找回密码验证码开始*/
    'sendMobileCodeByPhone'=>'index/findpwd/sendMobileCodeByPhone',//找回密码验证码
    'sendMobileCodeByPh'=>'index/findpwd/sendMobileCodeByPh',//修改手机号
    'find_password_by_phone'=>"index/findpwd/find_password_by_phone",//用于操作手机找回密码
    'update_password'=>"index/findpwd/update_password",//修改密码
    'member_update_mobile'=>"index/findpwd/member_update_mobile",//修改密码
    /*TODO:找回密码验证码结束*/

    /*TODO:我的页面开始*/
    "member_equity"=>"index/member/member_equity",//会员权益
    "member_address"=>"index/member/member_address",//地址管理
    "member_default_address_return"=>"index/member/member_default_address_return",//购买页面默认地址返回或者选择其他地址
    "member_address_information"=>"index/member/member_address_information",//地址管理列表数据返回
    "member_address_add"=>"index/member/member_address_add",//地址管理添加页面
    "member_address_adds"=>"index/member/member_address_adds",//地址管理添加功能(ajax)
    "member_address_del"=>"index/member/member_address_del",//地址管理删除功能(ajax)
    "member_address_status"=>"index/member/member_address_status",//地址管理设置默认功能(ajax)
    "member_save_address_id"=>"index/member/member_save_address_id",//地址编辑地址点击一个id传给后台
    "member_address_edit_information"=>"index/member/member_address_edit_information",//地址管理编辑页面数据返回
    "member_address_edit"=>"index/member/member_address_edit",//地址管理编辑功能(ajax)
    "member_collection"=>"index/member/member_collection",//我的收藏
    /*TODO:我的页面结束*/

    /*TODO:支付密码开始*/
    "sendMobileCodes"=>"index/PayPassword/sendMobileCodes",//支付密码修改验证码
    "pay_password_update"=>"index/PayPassword/pay_password_update",//修改支付密码
    /*TODO:支付密码结束*/


    /*TODO:收藏开始*/
    "collection_index"=>"index/Collection/collection_index",//（收藏数据返回）(ajax)
    "collection_add"=>"index/Collection/collection_add",//（添加收藏）（ajax）
    "collection_del"=>"index/Collection/collection_del",//（删除收藏）（ajax）
    "show_collection"=>"index/Collection/show_collection",//收藏样式返回的数据库状态值（ajax）
    /*TODO:收藏结束*/


    /*TODO:快递开始*/
    "express_wait_for_order"=>"index/Express/express_wait_for_order",//待接单
    "express_wait_for_take"=>"index/Express/express_wait_for_take",//待取货
    "express_distribution"=>"index/Express/express_distribution",//配送中
    "express_completed"=>"index/Express/express_completed",//已完成
    "express_detail"=>"index/Express/express_detail",//快递详情
    "express_info"=>"index/Express/express_info",//快递信息返回

    /*TODO:快递结束*/




    /*TODO:消息开始*/
    "information_index"=>"index/Information/index",//消息页面
    "information_details"=>"index/Information/information_details",//订单助手消息页面详情
    "information_system"=>"index/Information/information_system",//系统消息页面详情
    "about_index"=>"index/About/index",//关于我们
    /*TODO:消息结束*/
    /*TODO:投诉开始*/
    "complaint_index"=>"index/Complaint/index",//投诉中心
    "complaint_detail"=>"index/Complaint/detail",//投诉记录
    /*TODO:投诉结束*/




    /*TODO：店铺开始*/
    "store_index"=>"index/Store/index",             //店铺首页
    "store_league"=>"index/Store/league",           //我要加盟
    "store_verify"=>"index/Store/verify",           //身份验证

    "store_add"=>"index/Store/add",                 //店铺添加(第一页加盟添加)
    "store_save"=>"index/Store/save",                 //店铺编辑(第一页加盟编辑)
    "store_update"=>"index/Store/update",           //店铺编辑更新(也是第二页完善店铺信息)
    "url_img_del"=>"index/Store/url_img_del",           //店铺编辑更新(也是第二页信息多图的删除)
    "return_store_information"=>"index/Store/return_store_information",    //店铺信息
    /*TODO:店铺结束*/

    /*TODO：我要推广开始*/
    "spread_index"=>"index/Extension/spread_index",//我要推广
    /*TODO：我要推广结束*/

    /*TODO：配件商评价开始*/
    "evaluate_index"=>"index/Evaluate/evaluate_index",//评价页面(ajax)
    "evaluate_parts_add"=>"index/Evaluate/evaluate_parts_add",//评价页面(ajax)
    /*TODO：配件商评价结束*/






    // 商品分类   商品品牌分类
    "classify_index"=>"index/Classify/classify_index",
    "classify_recommend"=>"index/Classify/classify_recommend",//分类推荐




    /*配件商品*/
    "goods_list"=>"index/Classify/goods_list",//商品列表
    "goods_detail"=>"index/Classify/goods_detail",//商品详情



    /*配件商城*/
    "parts_index"=>"index/Parts/parts_index",



    // 购物车
    "cart_index"=>"index/Cart/cart_index",


    /*TODO:配件商订单状态开始*/
    "order_parts_save_record"=>"index/OrderParts/order_parts_save_record",//配件商订单进入详情需要存储的订单编号和店铺id
    "ios_api_order_parts_firm_order"=>"index/OrderParts/ios_api_order_parts_firm_order",//确认订单页面

    "order_parts_detail"=>"index/OrderParts/order_parts_detail",//订单详情
    "get_goods_id_save"=>"index/OrderParts/get_goods_id_save",//立即购买存储需要传到订单提交页面信息，方便确定订单提交
    "return_order_buy_information"=>"index/OrderParts/return_order_buy_information",//提交订单页面返回购买页面传过来的数据

    "order_parts_all"=>"index/OrderParts/order_parts_all",//全部订单页面
    "ios_api_order_parts_all"=>"index/OrderParts/ios_api_order_parts_all",//全部订单接口（ajax）
    "order_parts_wait_pay"=>"index/OrderParts/order_parts_wait_pay",//待付款页面
    "ios_api_order_parts_wait_pay"=>"index/OrderParts/ios_api_order_parts_wait_pay",//待付款接口(ajax)
    "order_wait_deliver"=>"index/OrderParts/order_wait_deliver", //待收货页面
    "ios_api_order_wait_deliver"=>"index/OrderParts/ios_api_order_wait_deliver", //待收货接口（ajax）
    "order_wait_evaluate"=>"index/OrderParts/order_wait_evaluate", //待评价页面
    "ios_api_order_wait_evaluate"=>"index/OrderParts/ios_api_order_wait_evaluate", //待评价接口（ajax）
    "order_parts_return_goods"=>"index/OrderParts/order_parts_return_goods",//退货页面
    "ios_api_order_parts_return_goods"=>"index/OrderParts/ios_api_order_parts_return_goods",//退货接口ajax
    "ios_api_order_parts_no_pay_cancel"=>"index/OrderParts/ios_api_order_parts_no_pay_cancel",//买家未付款取消订单接口(ajax)
    "order_parts_detail_cancel"=>"index/OrderParts/order_parts_detail_cancel",//买家未付款超过后台设置的时间未付款，系统取消订单接口(ajax)
    "ios_api_order_parts_del"=>"index/OrderParts/ios_api_order_parts_del",//买家删除订单接口(ajax)
    "ios_api_order_parts_collect_goods"=>"index/OrderParts/ios_api_order_parts_collect_goods",//配件商订单状态修改（买家确认收货）（ajax）
    /*TODO:配件商订单状态结束*/
    /*TODO：查看物流信息开始*/
    "logistics_index"=>"index/Logistics/logistics_index",//查看物流页面
    /*TODO：查看物流信息结束*/





    /*TODO:服务商订单状态开始*/
    "order_service_detail"=>"index/OrderService/order_service_detail",//订单详情
    "order_service_all"=>"index/OrderService/order_service_all",//全部订单页面
    "ios_api_order_service_all"=>"index/OrderService/ios_api_order_service_all",//全部订单接口（ajax）
    "order_service_wait_pay"=>"index/OrderService/order_service_wait_pay",//待付款页面
    "ios_api_order_service_wait_pay"=>"index/OrderService/ios_api_order_service_wait_pay",//待付款接口（ajax）
    "order_service_wait_deliver"=>"index/OrderService/order_service_wait_deliver", //待服务页面
    "ios_api_order_service_wait_deliver"=>"index/OrderService/ios_api_order_service_wait_deliver", //待服务接口（ajax）
    "order_service_wait_evaluate"=>"index/OrderService/order_service_wait_evaluate", //待评价页面
    "ios_api_order_service_wait_evaluate"=>"index/OrderService/ios_api_order_service_wait_evaluate", //待评价接口（ajax）
    "order_service_return_goods"=>"index/OrderService/order_service_return_goods",//退货页面
    "ios_api_order_service_return_goods"=>"index/OrderService/ios_api_order_service_return_goods",//退货页面接口（ajax）
    /*修改状态值*/
    "ios_api_order_service_no_pay_cancel"=>"index/OrderService/ios_api_order_service_no_pay_cancel",//买家未付款取消订单接口(ajax)
    "ios_api_order_service_already_served"=>"index/OrderService/ios_api_order_service_already_served",//买家服务商订单买家确认服务（ajax）

    /*TODO:服务商订单状态结束*/

    /*TODO:前端积分开始*/
    "return_integral_information"=>"index/Integral/return_integral_information", //消费满3元可使用3积分，3积分抵3元（ 返回给前端显示）
    /*TODO:前端积分结束*/


    /*汽车广告管理前端开始*/
    "advertisement_index"=>"index/Advertisement/advertisement_index", //汽车广告显示
    
]);

/**
 * [后台路由]
 * 陈绪
 */
Route::group("admin",[
    /*首页*/
    "/$"=>"admin/index/index",
   /* 后台首页 */
    "home_index"=>"admin/Home/index",

    /*登录页面*/
    "index"=>"admin/Login/index",
    "login"=>"admin/Login/login",
    "logout"=>"admin/Login/logout",


    /*验证码*/
    "login_captcha"=>"admin/Login/captchas",

    /*管理员列表*/
    "admin_index"=>"admin/admin/index",
    "admin_add"=>"admin/admin/add",
    "admin_save"=>"admin/admin/save",
    "admin_del"=>"admin/admin/del",
    "admin_edit"=>"admin/admin/edit",
    "admin_updata"=>"admin/admin/updata",
    "admin_status"=>"admin/admin/status",
    "admin_passwd"=>"admin/admin/passwd",



    /*菜单列表*/
    "menu_index"=>"admin/menu/index",
    "menu_add"=>"admin/menu/add",
    "menu_save"=>"admin/menu/save",
    "menu_del"=>"admin/menu/del",
    "menu_edit"=>"admin/menu/edit",
    "menu_updata"=>"admin/menu/updata",
    "menu_status"=>"admin/menu/status",



    /*角色列表*/
    "role_index"=>"admin/role/index",
    "role_add"=>"admin/role/add",
    "role_save"=>"admin/role/save",
    "role_del"=>"admin/role/del",
    "role_edit"=>"admin/role/edit",
    "role_updata"=>"admin/role/updata",
    "role_status"=>"admin/role/status",



    /*配件商品管理*/
    "goods_index"=>"admin/Goods/index",
    "goods_add"=>"admin/Goods/add",
    "goods_save"=>"admin/Goods/save",
    "goods_edit"=>"admin/Goods/edit",
    "goods_updata"=>"admin/Goods/updata",
    "goods_del"=>"admin/Goods/del",
    "images_del"=>"admin/Goods/images",
    "goods_status"=>"admin/Goods/status",
    "goods_batches"=>"admin/Goods/batches",                                     //批量删除
    "goods_pay"=>"admin/Goods/pay",                                                  //商品付费详情
    "affirm_pay"=>"admin/Goods/affirm",                                                 //商品确认付费
	"goods_look"=>"admin/Goods/look",                                                //商品查看详情
    "goods_name"=>"admin/Goods/name",                                                   //商品规格名添加
    "goods_standard_name"=>"admin/Goods/standard_name",                                       //商品规格名显示
    "goods_role_name"=>"admin/Goods/role_name",                                       //商品角色检测
    "goods_property_name"=>"admin/Goods/property_name",                                       //专用属性入库
    "goods_property_show"=>"admin/Goods/property_show",                                       //专用属性显示
    "goods_property_name_del"=>"admin/Goods/property_name_del",                              //专用属性名称删除
    "goods_alipay"=>"admin/Goods/alipay",                                       //支付
    "goods_pay_code"=>"admin/Goods/pay_code",                                       //支付宝回调地址
    "goods_edit_show"=>"admin/Goods/edit_show",                                       //专用适用车型编辑显示
    "goods_seach"=>"admin/Goods/seach",                                                //商品模糊搜索
    "goods_WeiAlpay"=>"admin/Goods/WeiAlpay",                                             //微信支付
    "goods_qrcode"=>"admin/Goods/qrcode",                                                //微信支付二维码
    "goods_alipay_pay"=>"admin/Goods/alipay_pay",                                                //微信支付回调地址
    "goods_get_weixin_pay_url"=>"admin/Goods/get_weixin_status",                              //微信是否上架检测




    /*服务商品管理*/
    "serve_index"=>"admin/Serve/index",
    "serve_add"=>"admin/Serve/add",
	"serve_look"=>"admin/Serve/look",
    "serve_save"=>"admin/Serve/save",
    "serve_edit"=>"admin/Serve/edit",
    "serve_updata"=>"admin/Serve/updata",
    "serve_del"=>"admin/Serve/del",
    "serve_batches"=>"admin/Serve/batches",                                     //服务商品批量删除



    /*商品分类*/
    "category_index"=>"admin/Category/index",
    "category_add"=>"admin/Category/add",
    "category_save"=>"admin/Category/save",
    "category_edit"=>"admin/Category/edit",
    "category_del"=>"admin/Category/del",
    "category_updata"=>"admin/Category/updata",
    "category_ajax"=>"admin/Category/ajax_add",
    "category_images"=>"admin/Category/images",
    "category_status"=>"admin/Category/status",



    /*会员管理 ：TODO*/
    "user_index"=>"admin/User/index", //会员概况
    "user_edit"=>"admin/User/edit",     //会员编辑
    "user_grade"=>"admin/User/grade",  //会员等级
    "user_static"=>"admin/User/status", //会员页面的账户状态修改
    "user_del"=>"admin/User/del", //会员删除
    "user_dels"=>"admin/User/dels", //会员批量删除
    "user_search"=>"admin/User/search", //会员搜索


    /**
     * 汽车品牌
     * 陈绪
     */
    "car_index"=>"admin/Car/index",
    "car_add"=>"admin/Car/add",
    "car_save"=>"admin/Car/save",
    "car_edit"=>"admin/Car/edit",
    "car_del"=>"admin/Car/del",
    "car_updata"=>"admin/Car/updata",
    "car_search"=>"admin/Car/search",
    "car_images"=>"admin/Car/images",


    /*充值和提现*/
    "recharge_list"=>"admin/Recharge/index", //充值和提现首页
    "recharge_edit"=>"admin/Recharge/edit",   //充值和提现编辑
    "recharge_del"=>"admin/Recharge/del",   //充值和提现删除
    "recharge_dels"=>"admin/Recharge/dels",   //充值和提现批量删除
    "recharge_search"=>"admin/Recharge/search",   //充值和提现搜索功能

    /*资金管理*/
    "capital_index"=>"admin/Capital/index",  //资金管理界面
    "capital_search"=>"admin/Capital/search",  //资金管理搜索功能


    /*积分中心*/
    "integral_center"=>"admin/Integral/index", //积分中心
    "integral_detail"=>"admin/Integral/detail", //积分详情
    "integral_add"=>"admin/Integral/add", //积分添加
    "integral_del"=>"admin/Integral/del", //积分列表删除
    "integral_dels"=>"admin/Integral/dels", //积分列表批量删除
    "integral_search"=>"admin/Integral/search", //积分列表搜索功能



    /*配件商广告*/
    "accessories_business_advertising"=>"admin/Advertisement/accessories_business_advertising", //汽车配件商广告显示
    "accessories_business_add"=>"admin/Advertisement/accessories_business_add",                 //汽车配件商广告添加
    "accessories_business_edit"=>"admin/Advertisement/accessories_business_edit",               //汽车配件商广告编辑
    "accessories_business_save"=>"admin/Advertisement/accessories_business_save",               //汽车配件商广告保存
    "accessories_business_updata"=>"admin/Advertisement/accessories_business_updata",           //汽车配件商广告更新
    "accessories_business_del"=>"admin/Advertisement/accessories_business_del",                 //汽车配件商广告删除
    "accessories_business_search"=>"admin/Advertisement/accessories_business_search",           //汽车配件商广告模糊搜索



    /*服务商广告*/
    "service_business_advertising"=>"admin/service_advertisement/service_business_advertising", //汽车服务商广告显示
    "service_business_add"=>"admin/service_advertisement/service_business_add",                 //汽车服务商广告增加
    "service_business_save"=>"admin/service_advertisement/service_business_save",               //汽车服务商广告保存
    "service_business_edit"=>"admin/service_advertisement/service_business_edit",               //汽车服务商广告编辑
    "service_business_updata"=>"admin/service_advertisement/service_business_updata",           //汽车服务商广告更新
    "service_business_del"=>"admin/service_advertisement/service_business_del",                                  //汽车服务商广告删除
    "service_business_search"=>"admin/service_advertisement/service_business_search",           //汽车服务商广告搜索



    /*平台广告*/
    "platform_business_index"=>"admin/platform_advertisement/platform_business_index",  //汽车平台广告显示
    "platform_business_add"=>"admin/platform_advertisement/platform_business_add",      //汽车平台广告添加
    "platform_business_save"=>"admin/platform_advertisement/platform_business_save",    //汽车平台广告保存
    "platform_business_edit"=>"admin/platform_advertisement/platform_business_edit",    //汽车平台广告编辑
    "platform_business_updata"=>"admin/platform_advertisement/platform_business_updata",//汽车平台广告更新
    "platform_business_del"=>"admin/platform_advertisement/platform_business_del",      //汽车平台广告删除  
    "platform_business_search"=>"admin/platform_advertisement/platform_business_search",//汽车平台广告模糊搜索




    /*订单管理：TODO:配件商订单开始*/
    "order_index"=>"admin/Order/index", //配件商订单列表
    "order_processing"=>"admin/Order/order_processing", //配件商订单列表弹窗接口（ajax）
    "order_update_status"=>"admin/Order/order_update_status", //配件商订单列表弹窗修改状态值（ajax）
    "order_search"=>"admin/Order/search", //配件商订单列表模糊搜索
    "order_dels"=>"admin/Order/dels", //配件商订单列表批量删除
    "order_edit"=>"admin/Order/edit", //*********配件商订单设置（未做）

    "order_evaluate"=>"admin/Order/evaluate",   //配件商订单评价
    "order_evaluate_details"=>"admin/Order/evaluate_details", //******配件商订单评价详情
    "order_evaluate_status"=>"admin/Order/evaluate_status", //******配件商订单评价状态值修改
    "order_evaluate_del"=>"admin/Order/evaluate_del", //******配件商订单评价删除
    "order_evaluate_dels"=>"admin/Order/evaluate_dels", //******配件商订单评价批量删除
    "order_evaluate_search"=>"admin/Order/evaluate_search", //******配件商订单评价列表模糊查询
    "order_evaluate_repay"=>"admin/Order/evaluate_repay", //******配件商订单评价回复操作

    "order_after_sale"=>"admin/Order/after_sale", //配件商订单维修售后

    "order_invoice"=>"admin/Order/invoice", //配件商发票列表
    "order_invoice_edit"=>"admin/Order/invoice_edit", //****配件商发票信息
    /*订单管理：TODO:配件商订单结束*/


    /*订单管理：TODO:平台商订单开始*/
    "platform_order_service_index"=>"admin/Order/platform_order_service_index", //平台商服务商订单列表
    "platform_order_service_processing"=>"admin/Order/platform_order_service_processing", //平台商服务商订单列表弹框详情（ajax）
    "platform_order_parts_index"=>"admin/Order/platform_order_parts_index", //平台商配件商订单列表
    "platform_order_processing"=>"admin/Order/platform_order_processing", //平台商配件商订单列表弹框详情（ajax）
    "platform_order_parts_search"=>"admin/Order/platform_order_parts_search", //平台商配件商订单列表模糊搜索
    "platform_order_parts_dels"=>"admin/Order/platform_order_parts_dels", //平台商配件商订单列表批量删除


    "platform_after_sale"=>"admin/Order/platform_after_sale", //平台商售后服务
    "platform_invoice_index"=>"admin/Order/platform_invoice_index", //平台商发票列表
    "platform_invoice_details"=>"admin/Order/platform_invoice_details", //平台商发票详情

    "platform_order_evaluate"=>"admin/Order/platform_order_evaluate", //平台配件商订单评价
    "platform_order_evaluate_edit"=>"admin/Order/platform_order_evaluate_edit", //平台商配件商订单评价编辑

    "platform_order_service_evaluate"=>"admin/Order/platform_order_service_evaluate", //平台服务商订单评价
    "platform_order_service_evaluate_edit"=>"admin/Order/platform_order_service_evaluate_edit", //平台服务商订单评价编辑

    "platform_order_set_up"=>"admin/Order/platform_order_set_up", //平台商配件商订单设置
    /*订单管理：TODO:平台订单结束*/

    
    /*订单管理：TODO:服务商商订单开始*/
    'service_order_index'=>"admin/Order/service_order_index", //服务商界面服务商订单列表
    "service_order_processing"=>"admin/Order/service_order_processing", //服务商界面服务商订单列表弹窗接口（ajax）
    'service_order_parts_dels'=>"admin/Order/service_order_parts_dels", //服务商界面服务商订单列表批量删除
    "service_order_evaluate"=>"admin/Order/service_order_evaluate", //服务商界面订单评价
    "service_order_evaluate_edit"=>"admin/Order/service_order_evaluate_edit", //服务商界面订单评价
    /*订单管理：TODO:服务商订单结束*/







    /*聊天管理*/
    "chat_index"=>"admin/Chat/index",
    /*后台获取用户发送过来的聊天信息*/
    "all_information"=>"admin/Chat/all_information",
    /*后台获取用户发送过来的聊天信息(已读)*/
    "read_all_information"=>"admin/Chat/read_all_information",
    /*后台获取用户发送过来的聊天信息（未读）*/
    "unread_all_information"=>"admin/Chat/unread_all_information",
    /*后台聊天信息的删除*/
    "chat_information_del"=>"admin/Chat/chat_information_del",
    /*批量删除*/
    "chat_information_deletes"=>"admin/Chat/chat_deletes",
    /*未读中按下回复按钮进入回复页面把状态值改变为已读*/
    "reading_information"=>"admin/Chat/reading_information",
    /*客服回复信息*/
    "admin_chat_push"=>"admin/Chat/admin_chat_push",



    /*客户中心*/
    "client_index"=>"admin/Client/index",



    /*设置*/
    "install_index"=>"admin/Install/index",
    "recommend_index"=>"admin/Install/recommend",//推荐奖励积分设置
    "recommend_update"=>"admin/Install/recommend_update",//推荐奖励积分设置更新

    "integral_index"=>"admin/Install/integral",//积分折扣设置
    "integral_setting_add"=>"admin/Install/integral_setting_add",//积分折扣设置添加功能
    "integral_setting_del"=>"admin/Install/integral_setting_del",//积分折扣设置删除功能

    "putaway_index"=>"admin/Install/putaway",

    "recharge_index"=>"admin/Install/recharge",//设置之充值设置
    "recharge_setting_add"=>"admin/Install/recharge_setting_add",//设置之充值设置添加数据
    "recharge_setting_del"=>"admin/Install/recharge_setting_del",//设置之充值设置删除数据

    "service_index"=>"admin/Install/service_index",//服务设置之列表
    "service_add"=>"admin/Install/service_add",
    "service_save"=>"admin/Install/service_save",
    "service_edit"=>"admin/Install/service_edit",//服务设置之编辑添加
    "service_image_del"=>"admin/Install/service_image_del",//服务设置之编辑里面图片删除
    "service_updata"=>"admin/Install/service_updata",
    "service_del"=>"admin/Install/service_del", //服务设置之删除

    "invoice_setting"=>"admin/Install/invoice_setting",//设置之发票手续费设置


    "message_index"=>"admin/Install/message_index",
    "message_del"=>"admin/Install/message_del",
    "message_save"=>"admin/Install/message_save",


     /*快递员列表*/
    "express_index"=>"admin/Install/express_index",   //快递员列表显示
    "express_add"=>"admin/Install/express_add",       //添加快递员列表
    "express_edit"=>"admin/Install/express_edit",     //快递员列表组编辑
    "express_save"=>"admin/Install/express_save",     //快递员组保存入库
    "express_updata"=>"admin/Install/express_updata", //快递员列表组更新
    "express_delete"=>"admin/Install/express_delete", //快递员列表组删除
    "express_search"=>"admin/Install/express_search", //快递员列表组搜索
    "express_dels"=>"admin/Install/express_dels",     //快递员列表组批量删除
    "order_setting_update"=>"admin/Install/order_setting_update",//订单设置

    
    /*商品品牌*/
    "brand_index"=>"admin/Brand/index",
    "brand_add"=>"admin/Brand/add",
    "brand_save"=>"admin/Brand/save",
    "brand_edit"=>"admin/Brand/edit",
    "brand_updata"=>"admin/Brand/updata",
    "brand_del"=>"admin/Brand/del",
    "brand_status"=>"admin/Brand/status",
    "brand_images"=>"admin/Brand/images",




    /*TODO:店铺管理开始*/
    "shop_index"=>"admin/Shop/index",//店铺列表
    "shop_add"=>"admin/Shop/add",//店铺详情
    "shop_update"=>"admin/Shop/update",//店铺详情更新
    "store_hot_status"=>"admin/Shop/store_hot_status",//店铺热门店铺修改（-1为正常，1为热门店铺）
    "shop_del"=>"admin/Shop/del",//店铺列表删除
    "shop_dels"=>"admin/Shop/dels",//店铺列表批量删除
    "shop_search"=>"admin/Shop/search",//店铺列表模糊查询
    /*TODO:店铺管理结束*/


    /**
     * 运行管理
     * 陈绪
     */
    "complaint_index"=>"admin/Operation/complaint_index",
    "urgency_index"=>"admin/Operation/urgency_index",

]);

Route::miss("public/miss");


