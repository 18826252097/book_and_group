<?php
/**
 * 验证器-通信模块-短信验证功能
 * @Author lwx（502510773@qq.com）
 * @Date 2018/02/10
 */
namespace app\communication\validate\check;
use think\Validate;
class Phone extends Validate{
    protected $rule = [
        'mobile'      => 'require|mobile',    //短信发送的号码
        'codes'        => 'require',       //验证码
    ];
    protected $message = [
        'mobile.require'  => '手机号不能为空',
        'mobile.mobile'   => '请输入有效的手机号码',
        'codes.require'   => '验证码不能为空',

    ];
    protected $regex = [
        'mobile'    => '/^1[2|4|5|8]\d{9}$/',
    ];
}