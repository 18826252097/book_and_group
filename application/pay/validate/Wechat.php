<?php
/**
 * Created by crp.
 * User: Administrator
 * Date: 2018/5/19
 * Time: 11:30
 */

namespace app\pay\validate;
use think\Validate;

class Wechat extends Validate
{
    private $trade_type = ['NATIVE','JSAPI','APP'];

    protected $rule = [
        'trade_type' => 'require|checkTradeType',
        'openid' => 'checkAboutid',
        'time_expire' => 'number|between:1,7200',
    ];

    /**
     * 检查微信支付类型
     * @param $value
     * @return bool|string
     */
    public function checkTradeType($value)
    {
        return in_array($value,$this->trade_type)?true:'微信支付类型错误';
    }

    /**
     * 检查微信appid和openid
     * @param $value
     * @param $rule
     * @param $data
     */
    public function checkAboutid($value,$rule,$data)
    {
        if ($data['trade_type'] == 'JSAPI'){
            if (empty($value)){
                return '支付方式为JSAPI时，openid必填';
            }
        }
        return true;
    }
}