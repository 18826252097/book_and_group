<?php
/**
 * 验证器-通信模块-自定义短信发送功能
 * @Author lwx（502510773@qq.com）
 * @Date 2018/05/28
 */
namespace app\communication\validate\send;
use think\Validate;
class CustomSend extends Validate{
    protected $rule = [
        'mobile'      => 'require|mobile',    //短信发送的号码
        'content'      => 'require',    //短信发送的号码
        'smsType'      => 'require',    //短信类型
    ];
    protected $message = [
        'mobile.require'  => '手机号不能为空',
        'mobile.mobile'   => '请输入有效的手机号码',
        'content.require'   => '内容不能为空',
        'smsType.require'   => '类型不能为空',

    ];
    protected $regex = [
        'mobile'    => '/^1[2|3|4|5|8]\d{9}$/',
    ];
}