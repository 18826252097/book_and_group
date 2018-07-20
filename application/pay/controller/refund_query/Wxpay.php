<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/19
 * Time: 15:17
 */

namespace app\pay\controller\refund_query;


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

        $this->config['out_trade_no'] = $config['out_trade_no'];
    }

    /**
     * 退款信息查询
     */
    public function refund_query()
    {
        $input = new \WxPayRefundQuery();

        $input->SetOut_trade_no($this->config['out_trade_no']);
        self::set_all_input($input);
        $result = \WxPayApi::refundQuery($input);

        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS"
        ) {
            return self::manage_data($result);
        } else {
            return [
                'code' => '10067',
                'data' => '',
                'msg' => isset($result['err_code_des']) ? $result['err_code_des'] : config('msg.10067')];
        }
    }

    /**
     * 整理查询到的数据
     * @param $result 微信返回数据
     * @return array
     */
    public function manage_data($result)
    {
        $temp_arr = [];
        $flag = true;

        foreach ($result as $key => $item) {
            switch ($flag){
                case strpos($key,'ut_refund_no_')://退款单号
                    $_key = explode('out_refund_no_',$key)[1];
                    $temp_arr[$_key]['out_refund_no'] = $item;
                    break;
                case strpos($key,'efund_account_')://退款资金来源
                    $_key = explode('refund_account_',$key)[1];
                    $temp_arr[$_key]['refund_account'] = $item;
                    break;
                case strpos($key,'efund_channel_')://退款渠道
                    $_key = explode('refund_channel_',$key)[1];
                    $temp_arr[$_key]['refund_channel'] = $item;
                    break;
                case strpos($key,'efund_fee_')://退款金额 单位：分
                    $_key = explode('refund_fee_',$key)[1];
                    $temp_arr[$_key]['refund_fee'] = $item;
                    break;
                case strpos($key,'efund_recv_accout_')://退款入账方
                    $_key = explode('refund_recv_accout_',$key)[1];
                    $temp_arr[$_key]['refund_recv_accout'] = $item;
                    break;
                case strpos($key,'efund_status_')://退款渠道
                    $_key = explode('refund_status_',$key)[1];
                    $temp_arr[$_key]['refund_status'] = $item;
                    break;
                case strpos($key,'efund_success_time_')://退款渠道
                    $_key = explode('refund_success_time_',$key)[1];
                    $temp_arr[$_key]['refund_success_time'] = $item;
                    break;
                default:
                    break;
            }
        }

        return [
            'out_trade_no' => $result['out_trade_no'],//订单号
            'total_fee' => $result['cash_fee'],//订单总金额
            'refund_count' => $result['refund_count'],//退款次数
            'refund_fee' => $result['refund_fee'],//退款总金额
            'refund_arr' => $temp_arr,
        ];
    }

    private function set_all_input(\WxPayRefundQuery &$input){
        //设置商户号
        if (!empty($this->config['weixin_config']['mch_id'])){
            $input->SetMch_id($this->config['weixin_config']['mch_id']);
        }
        //设置cert证书路径
        if (!empty($this->config['weixin_config']['ssl_cert_path'])){
            $input->Set_ssl_cert_path($this->config['weixin_config']['ssl_cert_path']);
        }
        //设置key证书路径
        if (!empty($this->config['weixin_config']['ssl_key_path'])){
            $input->Set_ssl_key_path($this->config['weixin_config']['ssl_key_path']);
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