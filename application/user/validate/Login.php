<?php
/**
 * 登录验证规则-返回提示定义
 * @Author zhouzeken（1454740914@qq.com）
 * @Date 2018/02/08
 */
namespace app\user\validate;
class Login {

    //账号登录验证
    public static function username(){
        $rule =   [
            'username'  => 'require|min:6|max:15',
            'password'  => 'require|min:6|max:20',
        ];

        $message  =   [
            'username.require'     => 10015,
            'username.min'         => 10016,
            'username.max'         => 10017,
            'password.require'     => 10018,
            'password.min'         => 10019,
            'password.max'         => 10020,
        ];

        return ['rule'=>$rule,'message'=>$message];
    }

    //手机登录验证
    public static function phone(){
        $rule =   [
            'phone'     => 'require|max:11|checkPhone',
            'password'  => 'require|min:6|max:20',
        ];

        $message  =   [
            'phone.require'        => 10040,
            'phone.max'            => 10022,
            'phone.checkPhone'     => 10023,
            'password.require'     => 10018,
            'password.min'         => 10019,
            'password.max'         => 10020,
        ];

        return ['rule'=>$rule,'message'=>$message];
    }

    //邮箱登录验证
    public static function email(){
        $rule =   [
            'email'     => 'require|email',
            'password'  => 'require|min:6|max:20',
        ];

        $message  =   [
            'email.require'        => 10039,
            'email.email'          => 10024,
            'password.require'     => 10018,
            'password.min'         => 10019,
            'password.max'         => 10020,
        ];

        return ['rule'=>$rule,'message'=>$message];
    }

    //第三方登录验证
    public static function party(){
        $rule =   [
            'openid'    => 'require',
        ];

        $message  =   [
            'openid.require'       => 10042,
        ];

        return ['rule'=>$rule,'message'=>$message];
    }
}