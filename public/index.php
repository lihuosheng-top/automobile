<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('EXTEND_PATH', '../extend/');
define('VENDOR_PATH', '../vendor/');
define('APP_PATH', __DIR__ . '/../application/');
//程序目录
define('S_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('WWW_URL','http://automobile.com');

define('D_BUG', 1);
define('OPEN_SHOP_MONEY',0.01);
D_BUG ? error_reporting(E_ALL ^ E_NOTICE) : error_reporting(0);

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
