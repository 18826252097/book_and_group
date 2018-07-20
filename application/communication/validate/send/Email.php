<?php
/**
 * 验证器-通信模块-邮箱发送功能
 * @Author lwx（502510773@qq.com）
 * @Date 2018/02/10
 */
namespace app\communication\validate\send;
use think\Validate;
class Email extends Validate{
    protected $rule = [
        'to'          => 'require|email',     //发送地址
        //'name'        => 'require',     //发送名称
        'subject'     => 'require',      //邮箱主题
        'body'        => 'require',      //邮寄主要内容
    ];
    protected $message = [
        'to.require'        => '收件人的地址不能为空',
        'to.email'          => '邮箱格式错误',
        //'name.require'      => '邮件名称不能为空',
        'subject.require'   => '邮件主题不能为空',
        'body.require'      => '邮件主要内容不能为空',

    ];
}