<?php
namespace app\user\validate;
class Update{
    //修改密码
    public static function pwd(){
        $rule =   [
            'uid'        => 'require',
            'old_passwd'  => 'require|min:6|max:20',
            'new_passwd'  => 'require|min:6|max:20',
        ];

        $message  =   [
            'uid.require'            => '10014',
            'old_passwd.require'     => '10026',
            'old_passwd.min'         => '10027',
            'old_passwd.max'         => '10028',
            'new_passwd.require'     => '10029',
            'new_passwd.min'         => '10030',
            'new_passwd.max'         => '10031',
        ];

        return ['rule'=>$rule,'message'=>$message];
    }

    //修改基本信息
    public static function info(){
        $rule =   [
            'mc_email'     => 'email',
            'mc_phone'     => 'max:11|checkPhone',
        ];

        $message  =   [
            'mc_email.email'          => '10024',
            'mc_phone.max'            => '10022',
            'mc_phone.checkPhone'     => '10023',
        ];

        return ['rule'=>$rule,'message'=>$message];
    }

    //找回密码
    public static function back_pwd(){
        $rule =   [
            'phone'     => 'require|max:11|checkPhone',
            'password'  => 'require|min:6|max:20',
        ];

        $message  =   [
            'phone.require'        => '10040',
            'phone.max'            => '10022',
            'phone.checkPhone'     => '10023',
            'password.require'     => '10029',
            'password.min'         => '10030',
            'password.max'         => '10031',
        ];

        return ['rule'=>$rule,'message'=>$message];
    }

}