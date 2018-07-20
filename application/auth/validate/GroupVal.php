<?php
/**
 * 角色-验证规则定义类
 */
namespace app\auth\validate;
class GroupVal
{
    public static function add(){
        $rule =   [
            'title'  => 'require|checkTitleinfo',
        ];

        $message  =   [
            'title.require'        => 10058,#名称不能为空
            'title.checkTitleinfo' => 10063,#名称已存在
        ];

        return ['rule'=>$rule,'message'=>$message];
    }


    public static function edit(){
        $rule =   [
            'id'     => 'require',
            'title'  => 'checkGroupEdit',
        ];

        $message  =   [
            'id.require'           => 10062,#ID不能为空
            'title.checkGroupEdit' => 10063,#名称已存在
        ];

        return ['rule'=>$rule,'message'=>$message];
    }
}