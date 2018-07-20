<?php
/**
 * 批量操作-验证规则定义类
 */
namespace app\user\validate;
class BatchVal 
{
    //验证导入信息
    public static function importCheck(){
        $rule =   [
            'username'  => 'require|min:6|max:15|checkUserInfo',
            'email'  => 'email|checkEmailInfo',
            'phone'  => 'checkPhone|checkPhoneInfo',
        ];

        $message  =   [
            'username.require'     => 10015,#账号不能为空
            'username.min'         => 10016,#账号不能小于6位
            'username.max'         => 10017,#账号不能大于15位
            'username.checkUserInfo'=> 10046,#账号已存在
            'email.email'          => 10024,#邮箱格式错误
            'email.checkEmailInfo' => 10036,#邮箱已注册
            'phone.checkPhone'     => 10023,#手机格式错误
            'phone.checkPhoneInfo' => 10037,#手机号码已注册
        ];

        return ['rule'=>$rule,'message'=>$message];
    }
}