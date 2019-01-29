<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/29 0022
 * Time: 17:20
 */
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;
class Xgcontent extends Controller
{


    /**
     * 使用默认设置推送消息给单个android版账户
     */
    public function push_Accountp($title, $content, $account)
    {  
        $accessId = "2100324571";
        $secretKey = "531896c8300cebf831d2e447de5df791";
//        include('../extend/Xg/xinge-api-php/src/XingeApp.php');
        include EXTEND_PATH."Xg/xinge-api-php/src/XingeApp.php";
        $push = new \XingeApp($accessId, $secretKey);
        $mess = new \Message();
        $mess->setTitle($title);
        $mess->setContent($content);
        $mess->setType(Message::TYPE_NOTIFICATION);
        $mess->setStyle(new \Style(0, 1, 1, 1, 0));
        $action = new \ClickAction();
        $action->setActionType(ClickAction::TYPE_ACTIVITY);
        $mess->setAction($action);
        $ret = $push->PushSingleAccount(0, $account, $mess);
        return $ret;
    }

}