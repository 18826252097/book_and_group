<?php
/**
 * 权限-验证规则定义类
 */
namespace app\auth\validate;
class RuleVal
{

    public static function add(){
        $rule =   [
            'title'  => 'require',
            'name'   => 'require|checkRuleName',
            'pid'    => 'require'
        ];

        $message  =   [
            'title.require'     => 10058,#名称不能为空
            'name.require'      => 10059,#权限规则不能为空
            'name.checkRuleName'=> 10060,#该权限已存在
            'pid.require'       => 10061,#请选择上级
        ];

        return ['rule'=>$rule,'message'=>$message];
    }

    public static function edit(){
        $rule =   [
            'id'     => 'require|checkRuleId',
            'name'   => 'checkRuleEdit',
        ];

        $message  =   [
            'id.require'        => 10062,#ID不能为空
            'id.checkRuleId'    => 10005,#数据不存在
            'name.checkRuleEdit'=> 10060,#该权限已存在
        ];

        return ['rule'=>$rule,'message'=>$message];
    }


}