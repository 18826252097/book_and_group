<?php
/**
 * 验证器-通信模块-短信发送功能
 * @Author lwx（502510773@qq.com）
 * @Date 2018/02/10
 */
namespace app\communication\validate\send;
use think\Validate;
class Phone extends Validate{
    protected $rule = [
        'mobile'      => 'require|mobile',    //短信发送的号码
        'len'         => 'number|between:4,6',       //字符串长度
        'code'        => 'require',       //验证码
    ];
    protected $message = [
        'mobile.require'  => '手机号不能为空',
        'mobile.mobile'   => '请输入有效的手机号码',
        'len.number'      => '验证码长度不能为空',
        'len.between'     => '验证码长度4到6位',
        'code.require'    => '验证码不能为空',

    ];
    protected $regex = [
        'mobile'    => '/^1[2|3|4|5|6|7|8|9]\d{9}$/',
    ];
}