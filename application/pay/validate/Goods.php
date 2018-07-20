<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/19
 * Time: 10:26
 */
namespace app\pay\validate;
use think\Validate;

class Goods extends Validate
{
    protected $rule = [
        'out_trade_no' => 'require|length:12,32',
        'price' => 'number|gt:0',
        'real_pay' => 'number|gt:0',
    ];
}