<?php
/**
 * Created by crp.
 * User: Administrator
 * Date: 2018/5/19
 * Time: 9:34
 */

namespace app\pay\controller\query;


class Wxpay
{
    private $config = [
        'out_trade_no' => '',//订单号
    ];

    public function __construct(array $config = [])
    {
        vendor('WxpayAPI_php_v3_0_1.lib.WxPay','.Data.php');//微信支付函数包
        vendor('WxpayAPI_php_v3_0_1.lib.WxPayApi','.php');//微信支付Api

        if (!isset($config['out_trade_no'])){
            return ['code' => 10010,'msg' => config('msg.10010'),'data'=>''];
        }

        $this->config = $config;
    }

    /**
     * 查询微信订单是否支付
     * @return array
     */
    public function query()
    {
        $out_trade_no = $this->config['out_trade_no'];
        $input = new \WxPayOrderQuery();
        $input->SetOut_trade_no($out_trade_no);
        self::set_all_input($input);
        $result = \WxPayApi::orderQuery($input);

        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS"
            && $result["trade_state"] == "SUCCESS")
        {
            # TODO 支付成功后操作 根据实际情况修改
            return model('order')->after_payment($out_trade_no);
        }else{
            return ['code'=>'10069', 'data'=>'','msg'=>config('msg.10069')];
        }
    }

    private function set_all_input(\WxPayOrderQuery &$input){
        //设置APP ID
        if (!empty($this->config['weixin_config']['appid'])){
            $input->SetAppid($this->config['weixin_config']['appid']);
        }
        //设置商户号
        if (!empty($this->config['weixin_config']['mch_id'])){
            $input->SetMch_id($this->config['weixin_config']['mch_id']);
        }
        //设置支付密钥
        if (!empty($this->config['weixin_config']['key'])){
            $input->Set_key($this->config['weixin_config']['key']);
        }
        //设置公众账号
        if (!empty($this->config['weixin_config']['app_secert'])){
            $input->Set_app_secert($this->config['weixin_config']['app_secert']);
        }
    }
}