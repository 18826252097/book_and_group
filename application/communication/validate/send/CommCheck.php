<?php
/**
 * 验证器-通信模块-添加记录
 * User: linweixin(502510773@qq.com)
 * Date: 2018/2/12
 * Time: 16:05
 */
namespace app\communication\validate\send;
use think\Validate;
class CommCheck extends Validate
{
    protected $rule = [
        'code'          => 'require',     //验证码
        'verify_time'   => 'require',     //过期时间
        'key'           => 'require',     //唯一key
    ];
    protected $message = [
        'code.require'          => '验证码不能为空',
        'verify_time.require'   => '过期时间不能为空',     //过期时间
        'key.require'           => '唯一key不能为空',     //唯一key

    ];

}