<?php
/**
 * 通信-验证器-工厂类
 * User: linweixin(502510773@qq.com)
 * Date: 2018/2/5
 * Time: 10:50
 */
namespace app\communication\validate;
use app\communication\validate\check;
use app\communication\validate\send;
class ValidateFactory {
    //定义个静态工厂方法
    public static function send($type)
    {
        switch ($type)
        {
            case 'phone':
                $obj = new send\Phone();
                break;
            case 'commCheck':
                $obj = new send\CommCheck();
                break;
            case 'CustomSend':
                $obj = new send\CustomSend();
                break;
            case 'email':
            default:
                $obj = new send\Email();
                break;
        }
        return $obj;
    }
    //定义个静态工厂方法
    public static function check($type)
    {
        switch ($type)
        {
            case 'phone':
                $obj = new check\Phone();
                break;
            case 'email':
            default:
                $obj = new check\Email();
                break;
        }
        return $obj;
    }

}