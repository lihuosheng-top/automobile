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
    "/$"=>"index/index/index",


    /*商品列表*/
    "goods_index"=>"index/Goods/index",
    "goods_detail"=>"index/Goods/detail",
    "goods_id"=>"index/Goods/ajax_id",
    "particulars_id"=>"index/Goods/goods_id",
    "goods_big_images"=>"index/Goods/big_images",



    /*登录页面*/
    "login_index"=>"index/Login/index",
    /*退出登录*/
    "logout"=>"index/Login/logout",
    /*验证码*/
    "login_captcha"=>"index/Login/captchas",



    /*注册页面*/
    "register"=>"index/Register/index",
    "register_code"=>"index/Register/code",
    "register_index"=>"index/Register/register",



    /*安全中心*/
    "security_index"=>"index/Security/index",



    /*模板商城*/
    "template_index"=>"index/Template/index",
    "template_goods_show"=>"index/Template/goods_show",
    "template_goods_buy"=>"index/Template/goods_buy",


    /*定制开发*/
    "exploit_index"=>"index/Exploit/index",



    /*安全中心*/
    "center_index"=>"index/Center/index",



]);

/**
 * [后台路由]
 * 陈绪
 */
Route::group("admin",[
    /*首页*/
    "/$"=>"admin/index/index",


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
    "images_dels"=>"admin/Goods/image",
    "goods_status"=>"admin/Goods/status",
    "goods_batches"=>"admin/Goods/batches",
    "goods_putaway"=>"admin/Goods/putaway",                                    //商品上架
    "goods_pay"=>"admin/Goods/pay",                                            //商品付费详情
    "affirm_pay"=>"admin/Goods/affirm",


    /*服务商品管理*/
    "serve_index"=>"admin/Serve/index",
    "serve_add"=>"admin/Serve/add",



    /*商品分类*/
    "category_index"=>"admin/Category/index",
    "category_add"=>"admin/Category/add",
    "category_save"=>"admin/Category/save",
    "category_edit"=>"admin/Category/edit",
    "category_del"=>"admin/Category/del",
    "category_updata"=>"admin/Category/updata",
    "category_ajax"=>"admin/Category/ajax_add",
    "category_images"=>"admin/Category/images",


    /*会员管理*/
    "user_index"=>"admin/User/index", //会员概况
    "user_search"=>"admin/User/search", //会员搜索
    "user_add"=>"admin/User/add",     //会员增加
    "user_save"=>"admin/User/save",     //会员增加(逻辑处理)
    "user_edit"=>"admin/User/edit",     //会员编辑
    "user_edits"=>"admin/User/edits",     //会员编辑
    "user_update"=>"admin/User/update",     //会员编辑更新
    "user_del"=>"admin/User/del",     //会员删除
    "user_dels"=>"admin/User/dels",     //会员批量删除
    "user_status"=>"admin/User/status",     //会员软删除禁用
    "user_statu"=>"admin/User/statu",     //会员软删除启用
    "user_show"=>"admin/User/show",     //会员查看
    "user_shows"=>"admin/User/shows",     //会员查看
    "getRegion"=>"admin/User/getRegion",     //三级地区
    "pass_edit"=>"admin/User/pass_edit",     //会员密码编辑



    /*优惠券*/
    "discount_index"=>"admin/Discount/index",
    "discount_add"=>"admin/Discount/add",
    "discount_save"=>"admin/Discount/save",
    "discount_edit"=>"admin/Discount/edit",
    "discount_updata"=>"admin/Discount/updata",
    "discount_del"=>"admin/Discount/del",
    "discount_batches"=>"admin/Discount/batches",


    /*代理*/
    "agency_index"=>"admin/Agency/index",
    "agency_add"=>"admin/Agency/add",
    "agency_save"=>"admin/Agency/save",
    "agency_edit"=>"admin/Agency/edit",
    "agency_updata"=>"admin/Agency/updata",
    "agency_del"=>"admin/Agency/del",



    /*订单管理*/
    "order_index"=>"admin/Order/index",
    "order_search"=>"admin/Order/search",//模糊查询
    "batch_delivery"=>"admin/Order/batch_delivery",//批量发货
    "pending_payment"=>"admin/Order/pending_payment",//代发货
    'order_refuse'=>"admin/Order/refuse", //商家取消买家订单
    "express_number"=>"admin/Order/express_number",//商家手动填写快递单号
    "order_deliver"=>"admin/Order/order_deliver", //已发货点击弹出的快递信息



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


    /*内容管理*/
    "content_index"=>"admin/Content/index",
    "content_add"=>"admin/Content/add",
    "content_save"=>"admin/Content/save",
    "content_edit"=>"admin/Content/edit",
    "content_del"=>"admin/Content/del",
    "content_updata"=>"admin/Content/updata",


    /*常见问题*/
    "issue_index"=>"admin/Issue/index",
    "issue_add"=>"admin/Issue/add",
    "issue_save"=>"admin/Issue/save",
    "issue_edit"=>"admin/Issue/edit",
    "issue_del"=>"admin/Issue/del",
    "issue_updata"=>"admin/Issue/updata",
    "issue_status"=>"admin/Issue/status",
    "issue_putaway"=>"admin/Issue/putaway",


    /*客户中心*/
    "client_index"=>"admin/Client/index",



    /*广告管理*/
    "advertising_index"=>"admin/Advertising/index",
    "advertising_add"=>"admin/Advertising/add",
    "advertising_save"=>"admin/Advertising/save",
    "advertising_del"=>"admin/Advertising/del",
    "advertising_edit"=>"admin/Advertising/edit",
    "advertising_updata"=>"admin/Advertising/updata",
    "advertising_images"=>"admin/Advertising/images",

]);

Route::miss("public/miss");


