<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/19
 * Time: 14:50
 */

namespace app\pay\controller\refund;


class Wxpay
{
    private $config = [
        'out_refund_no' => 0,//退款单号
        'out_trade_no' => '',//订单号
        'total_fee' => 1,//订单总金额
        'refund_fee' => 0,//退款金额
        'weixin_config' => []
    ];

    public function __construct(array $config = [])
    {
        vendor('WxpayAPI_php_v3_0_1.lib.WxPay','.Data.php');//微信支付函数包
        vendor('WxpayAPI_php_v3_0_1.lib.WxPayApi','.php');//微信支付Api

        if (!isset($config['out_trade_no'])){
            return ['code' => 10010,'msg' => config('msg.10010'),'data'=>''];
        }

        if (isset($config['refund_fee'])){
            $this->config['refund_fee'] = $config['refund_fee'];
        }

        if (isset($config['out_refund_no'])){
            $this->config['out_refund_no'] = $config['out_refund_no'];
        }

        $this->config['out_trade_no'] = $config['out_trade_no'];
        if (isset($config['weixin_config'])){
            $this->config['weixin_config'] = array_merge($this->config['weixin_config'],$config['weixin_config']);
        }
    }

    /**
     * 执行退款
     * @return array
     */
    public function refund()
    {
        $check_result = self::check_order();
        if ($check_result['code'] != 200){
            return $check_result;
        }
        $out_refund_no = !empty($this->config['out_refund_no'])?$this->config['out_refund_no']:(empty($this->config['weixin_config']['mch_id'])?\WxPayConfig::MCHID:$this->config['weixin_config']['mch_id']).date("YmdHis");
        $input = new \WxPayRefund();
        $input->SetOut_trade_no($this->config['out_trade_no']);
        $input->SetOut_refund_no($out_refund_no);
        $input->SetOp_user_id(\WxPayConfig::MCHID);
        $input->SetTotal_fee($this->config['total_fee']);
        $input->SetRefund_fee($this->config['refund_fee']);
        self::set_all_input($input);
        $result = \WxPayApi::refund($input);

        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return [
                'code' => 200,
                'msg' => config('msg.200'),
                'data' => [
                    'refund_fee' => $result['refund_fee'],//退款金额
                    'total_fee	' => $result['total_fee'],//订单总金额
                    'out_refund_no' => $result['out_refund_no'],//商户退款单号
                ]
            ];
        }else{
            return [
                'code' => '10070',
                'data' => '',
                'msg' => isset($result['err_code_des'])?$result['err_code_des']:config('msg.10070')
            ];
        }
    }

    /**
     * 检查订单信息
     * @return array
     */
    public function check_order()
    {
        $order = db('order')->field('real_pay')->where(['out_trade_no'=>$this->config['out_trade_no']])->find();
        $flag = true;
        switch ($flag){
            case empty($order):
                return ['code'=>10071,'msg'=>config('msg.10071'),'data'=>''];
                break;
            case $order['real_pay'] < $this->config['refund_fee']:
                return ['code'=>10072,'msg'=>config('msg.10072'),'data'=>''];
            case $this->config['refund_fee'] <= 0:
                $this->config['refund_fee'] = $this->config['total_fee'] = $order['real_pay'];
                break;
            default:
                break;
        }

        return ['code'=>200];
    }

    /**
     * 设置微信配置
     * @param \WxPayRefund $input
     */
    private function set_all_input(\WxPayRefund &$input){
        //设置APP ID
        if (!empty($this->config['weixin_config']['appid'])){
            $input->SetAppid($this->config['weixin_config']['appid']);
        }
        //设置商户号
        if (!empty($this->config['weixin_config']['mch_id'])){
            $input->SetMch_id($this->config['weixin_config']['mch_id']);
        }
        //设置cert证书路径
        if (!empty($this->config['weixin_config']['ssl_cert_path'])){
            self::set_ssl_cert_path();
            $input->Set_ssl_cert_path(str_replace('/','\\',$_SERVER['DOCUMENT_ROOT']).'\php\vendor\WxpayAPI_php_v3_0_1\cert_bank\apiclient_cert.pem');

        }
        //设置key证书路径
        if (!empty($this->config['weixin_config']['ssl_key_path'])){
            self::set_ssl_key_path();
            $input->Set_ssl_key_path(str_replace('/','\\',$_SERVER['DOCUMENT_ROOT']).'\php\vendor\WxpayAPI_php_v3_0_1\cert_bank\apiclient_key.pem');

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

    /**
     * 根据证书内容生成apiclient_cert.pem文件
     */
    private function set_ssl_cert_path(){
        $file_cert = fopen('../php/vendor/WxpayAPI_php_v3_0_1/cert_bank/apiclient_cert.pem','w');
        $txt_cert = $this->config['weixin_config']['ssl_cert_path'];
        fwrite($file_cert, $txt_cert);
        fclose($file_cert);
    }

    /**
     * 根据证书内容生成apiclient_key.pem文件
     */
    private function set_ssl_key_path(){
        $file_key = fopen('../php/vendor/WxpayAPI_php_v3_0_1/cert_bank/apiclient_key.pem','w');
        $txt_key = $this->config['weixin_config']['ssl_key_path'];
        fwrite($file_key, $txt_key);
        fclose($file_key);
    }
}