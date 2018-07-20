<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/19
 * Time: 15:15
 */

namespace app\pay\controller\close;


class Wxpay
{
    private $config = [
        'out_trade_no' => '',//订单号
    ];

    /**
     * 初始化数据
     * Wxpay constructor.
     * @param array $config ['out_trade_no'=>订单号]
     */
    public function __construct(array $config = [])
    {
        vendor('WxpayAPI_php_v3_0_1.lib.WxPay','.Data.php');//微信支付函数包
        vendor('WxpayAPI_php_v3_0_1.lib.WxPayApi','.php');//微信支付Api

        if (isset($config['out_trade_no'])){
            $this->config['out_trade_no'] = $config['out_trade_no'];
        }
    }

    /**
     * 微信关闭订单
     * @return array
     */
    public function close()
    {
        $out_trade_no = $this->config['out_trade_no'];

        if (!db('order')->where(['out_trade_no'=>$out_trade_no])->find()){
            return ['code'=>'10071', 'data'=>'','msg'=>config('msg.10071')];
        }

        $input = new \WxPayCloseOrder();
        $input->SetOut_trade_no($out_trade_no);
        self::set_all_input($input);
        $result = \WxPayApi::closeOrder($input);
        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return ['code' => 200,'msg' => config('msg.200'),'data' => ''];
        }else{
            return ['code'=>'10067', 'data'=>'','msg'=>config('msg.10067')];
        }
    }

    private function set_all_input(\WxPayCloseOrder &$input){
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