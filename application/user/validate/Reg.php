<?php
/**
 * Created by PhpStorm.
 * User: Administrator- mazefeng<1220441774@qq.com>
 * Date: 2018/2/9 0009
 * Time: 下午 4:02
 */
namespace app\user\validate;
class Reg {

    //验证手机参数
    static function phone(){
        $rule =   [
            'aga_group_id'=> 'require|checkGroupId',
            'mc_phone'     => 'require|max:11|checkPhone|checkPhoneInfo',
            'm_password'  => 'require|min:6|max:20',
            'm_username'  => 'min:6|max:15|checkUsername|checkUserInfo',
            'mc_email'     => 'email|checkEmailInfo',
        ];
        $message  =  [
            'aga_group_id.require'       => '10066', #角色身份ID不能为空
            'aga_group_id.checkGroupId'  => '10065', #角色身份不存在
            'mc_phone.require'            => '10040', #手机号不能为空
            'mc_phone.max'                => '10022', #手机号码为11位
            'mc_phone.checkPhone'         => '10023', #手机格式错误
            'mc_phone.checkPhoneInfo'     => '10037', #手机号码已注册
            'm_password.require'         => '10018', #密码不能为空
            'm_password.min'             => '10019', #密码不能小于6位
            'm_password.max'             => '10020', #密码不能大于20位
            'm_username.min'             => '10016', #账号不能小于6位
            'm_username.max'             => '10017', #账号不能大于15位
            'm_username.checkUsername'   => '10043', #用户名开头不能为数字,不能包含@等特殊字符
            'm_username.checkUserInfo'   => '10046', #账号已存在
            'mc_email.email'              => '10024', #邮箱格式错误
            'mc_email.checkEmailInfo'     => '10036', #邮箱已注册
        ];
        return ['rule'=>$rule,'message'=>$message];
    }

    //验证邮箱参数
    static function email(){
        $rule =   [
            'aga_group_id'=> 'require|checkGroupId',
            'mc_email'     => 'require|email|checkEmailInfo',
            'm_password'  => 'require|min:6|max:20',
            'mc_phone'     => 'max:11|checkPhone|checkPhoneInfo',
            'm_username'  => 'min:6|max:15|checkUsername|checkUserInfo',
        ];
        $message  =  [
            'aga_group_id.require'       => '10066', #角色身份ID不能为空
            'aga_group_id.checkGroupId'  => '10065', #角色身份不存在
            'mc_email.require'            => '10039', #邮箱不能为空
            'mc_email.email'              => '10024', #邮箱格式错误
            'mc_email.checkEmailInfo'     => '10036', #邮箱已注册
            'm_password.require'         => '10018', #密码不能为空
            'm_password.min'             => '10019', #密码不能小于6位
            'm_password.max'             => '10020', #密码不能大于20位
            'mc_phone.max'                => '10022', #手机号码为11位
            'mc_phone.checkPhone'         => '10023', #手机格式错误
            'mc_phone.checkPhoneInfo'     => '10037', #手机号码已注册
            'm_username.min'             => '10016', #账号不能小于6位
            'm_username.max'             => '10017', #账号不能大于15位
            'm_username.checkUsername'   => '10043', #用户名开头不能为数字,不能包含@等特殊字符
            'm_username.checkUserInfo'   => '10046', #账号已存在
        ];
        return ['rule'=>$rule,'message'=>$message];
    }

    //验证用户名参数
    static function username(){
        $rule =   [
            'aga_group_id'=> 'require|checkGroupId',
            'm_username'  => 'require|min:6|max:15|checkUsername|checkUserInfo',
            'm_password'  => 'require|min:6|max:20',
            'mc_email'     => 'email|checkEmailInfo',
            'mc_phone'     => 'max:11|checkPhone|checkPhoneInfo',
        ];
        $message  =  [
            'aga_group_id.require'       => '10066', #角色身份ID不能为空
            'aga_group_id.checkGroupId'  => '10065', #角色身份不存在
            'm_username.require'         => '10015', #账号不能为空
            'm_username.min'             => '10016', #账号不能小于6位
            'm_username.max'             => '10017', #账号不能大于15位
            'm_username.checkUsername'   => '10043', #用户名开头不能为数字,不能包含@等特殊字符
            'm_username.checkUserInfo'   => '10046', #账号已存在
            'm_password.require'         => '10018', #密码不能为空
            'm_password.min'             => '10019', #密码不能小于6位
            'm_password.max'             => '10020', #密码不能大于20位
            'mc_email.email'              => '10024', #邮箱格式错误
            'mc_email.checkEmailInfo'     => '10036', #邮箱已注册
            'mc_phone.max'                => '10022', #手机号码为11位
            'mc_phone.checkPhone'         => '10023', #手机格式错误
            'mc_phone.checkPhoneInfo'     => '10037', #手机号码已注册
        ];
        return ['rule'=>$rule,'message'=>$message];
    }

    //验证qq注册参数
    static function qq(){
        $rule =   [
            'aga_group_id'=> 'require|checkGroupId',
            'mtp_openid'  => 'require|checkOpenidInfo',
            'mtp_content' => 'require',
            'mtp_third_id'=> 'require',
            'm_username'  => 'min:6|max:15|checkUsername|checkUserInfo',
            'm_password'  => 'min:6|max:20',
            'mc_email'     => 'email|checkEmailInfo',
            'mc_phone'     => 'max:11|checkPhone|checkPhoneInfo',
        ];
        $message  =  [
            'aga_group_id.require'       => '10066', #角色身份ID不能为空
            'aga_group_id.checkGroupId'  => '10065', #角色身份不存在
            'mtp_openid.require'           => '10042', #第三方平台openid不能为空
            'mtp_openid.checkOpenidInfo'   => '10055', #openid已注册
            'mtp_content.require'          => '10044', #第三方平台信息 json格式不能为空
            'mtp_third_id.require'         => '10041', #第三方平台ID不能为空
            'm_username.min'             => '10016', #账号不能小于6位
            'm_username.max'             => '10017', #账号不能大于15位
            'm_username.checkUsername'   => '10043', #用户名开头不能为数字,不能包含@等特殊字符
            'm_username.checkUserInfo'   => '10046', #账号已存在
            'm_password.min'             => '10019', #密码不能小于6位
            'm_password.max'             => '10020', #密码不能大于20位
            'mc_email.email'              => '10024', #邮箱格式错误
            'mc_email.checkEmailInfo'     => '10036', #邮箱已注册
            'mc_phone.max'                => '10022', #手机号码为11位
            'mc_phone.checkPhone'         => '10023', #手机格式错误
            'mc_phone.checkPhoneInfo'     => '10037', #手机号码已注册
        ];
        return ['rule'=>$rule,'message'=>$message];
    }

    //验证微信注册参数
    static function weChat(){
        $rule =   [
            'aga_group_id'=> 'require|checkGroupId',
            'mtp_openid'  => 'require|checkOpenidInfo',
            'mtp_content' => 'require',
            'mtp_third_id'=> 'require',
            'm_username'  => 'min:6|max:15|checkUsername|checkUserInfo',
            'm_password'  => 'min:6|max:20',
            'mc_email'     => 'email|checkEmailInfo',
            'mc_phone'     => 'max:11|checkPhone|checkPhoneInfo',
        ];
        $message  =  [
            'aga_group_id.require'       => '10066', #角色身份ID不能为空
            'aga_group_id.checkGroupId'  => '10065', #角色身份不存在
            'mtp_openid.require'           => '10042', #第三方平台openid不能为空
            'mtp_openid.checkOpenidInfo'   => '10055', #openid已注册
            'mtp_content.require'          => '10044', #第三方平台信息 json格式不能为空
            'mtp_third_id.require'         => '10041', #第三方平台ID不能为空
            'm_username.min'             => '10016', #账号不能小于6位
            'm_username.max'             => '10017', #账号不能大于15位
            'm_username.checkUsername'   => '10043', #用户名开头不能为数字,不能包含@等特殊字符
            'm_username.checkUserInfo'   => '10046', #账号已存在
            'm_password.min'             => '10019', #密码不能小于6位
            'm_password.max'             => '10020', #密码不能大于20位
            'mc_email.email'              => '10024', #邮箱格式错误
            'mc_email.checkEmailInfo'     => '10036', #邮箱已注册
            'mc_phone.max'                => '10022', #手机号码为11位
            'mc_phone.checkPhone'         => '10023', #手机格式错误
            'mc_phone.checkPhoneInfo'     => '10037', #手机号码已注册
        ];
        return ['rule'=>$rule,'message'=>$message];
    }

    //验证新增用户
    static function add(){
        $rule =   [
            'aga_group_id'=> 'require|checkGroupId',
            'm_username'  => 'min:6|max:15|checkUsername|checkUserInfo',
            'm_password'  => 'min:6|max:20',
            'mc_email'     => 'email|checkEmailInfo',
            'mc_phone'     => 'max:11|checkPhone|checkPhoneInfo',
        ];
        $message  =  [
            'aga_group_id.require'       => '10066', #角色身份ID不能为空
            'aga_group_id.checkGroupId'  => '10065', #角色身份不存在
            'm_username.min'             => '10016', #账号不能小于6位
            'm_username.max'             => '10017', #账号不能大于15位
            'm_username.checkUsername'   => '10043', #用户名开头不能为数字,不能包含@等特殊字符
            'm_username.checkUserInfo'   => '10046', #账号已存在
            'm_password.min'             => '10019', #密码不能小于6位
            'm_password.max'             => '10020', #密码不能大于20位
            'mc_email.email'             => '10024', #邮箱格式错误
            'mc_email.checkEmailInfo'    => '10036', #邮箱已注册
            'mc_phone.max'               => '10022', #手机号码为11位
            'mc_phone.checkPhone'        => '10023', #手机格式错误
            'mc_phone.checkPhoneInfo'    => '10037', #手机号码已注册
        ];
        return ['rule'=>$rule,'message'=>$message];
    }
}