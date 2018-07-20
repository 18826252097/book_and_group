<?php

/**
 * Created by phpstorm.
 * User: crp
 * Date: 2018/5/25
 * Time: 9:41
 */
namespace app\pay\controller;

class Notify
{
    /**
     * 支付宝回调
     */
    public function alipay()
    {
        $trade_status = input('trade_status','');
        switch ($trade_status){
            case 'TRADE_SUCCESS':case 'TRADE_FINISHED':
            $out_trade_no = input('out_trade_no','');
            $receipt_amount = input('receipt_amount','');
            # TODO 检验订单金额是否正确 根据实际数据库修改
            $real_pay = db('order')->getFieldByOutTradeNo($out_trade_no,'real_pay');
            if (($real_pay/100) == $receipt_amount){
                 self::check_status($out_trade_no,'ali');
            }
            break;
            default:
                break;
        }
        echo 'success';
    }

    /**
     * 微信支付回调
     */
    public function wechat()
    {
        $xml = file_get_contents("php://input");
        if (!empty($xml)){
            $param = [];
            $xmlObj = simplexml_load_string($xml);
            foreach ($xmlObj->children() as $key => $child) {
                $param[$child->getName()] = trim($child);
            }

            $return_code = 'FAIL';
            if ($param['result_code'] == 'SUCCESS' && $param['return_code'] == 'SUCCESS'){
                $out_trade_no = isset($param['out_trade_no'])?$param['out_trade_no']:0;
                # TODO 检验订单金额是否正确 根据实际数据库修改
                $real_pay = db('order')->getFieldByOutTradeNo($out_trade_no,'real_pay');
                $total_fee = isset($param['total_fee'])?$param['total_fee']:0;
                if ($total_fee == $real_pay){
                    self::check_status($out_trade_no,'wechat');
                    $return_code = 'SUCCESS';
                }
            }
            self::text_handle($return_code);
        }
    }

    /**
     * 检查订单正确性
     * @param $out_trade_no
     * @param $type
     */
    private function check_status($out_trade_no,$type){
        $config = [
            'out_trade_no' => $out_trade_no
        ];
        
        switch ($type){
            case 'wechat':
                $query_obj = new query\Wxpay($config);
                break;
            default:
                $query_obj = new query\Alipay($config);
                break;
        }
        $query_obj->query();
    }

    /**
     * 回复微信
     */
    private function text_handle($return_code)
    {
        $return_msg = 'OK';
        $textTpl = "<xml>
  <return_code><![CDATA[%s]]></return_code>
  <return_msg><![CDATA[%s]]></return_msg>
</xml>";
        echo sprintf($textTpl,$return_code,$return_msg);
    }
}