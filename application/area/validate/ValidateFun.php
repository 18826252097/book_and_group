<?php
/**
 * 自定义验证规则方法存放类
 * @Author zhouzeken（1454740914@qq.com）
 * @Date 2018/02/09
 */

namespace app\area\validate;
use think\Validate;
use think\Db;

class ValidateFun extends Validate
{
    public function __construct(array $rules = [], $message = [], $field = [])
    {
        parent::__construct($rules, $message, $field);
    }

    

}