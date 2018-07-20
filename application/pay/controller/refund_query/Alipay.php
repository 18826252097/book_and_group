<?php
/**
 * Created by crp.
 * User: Administrator
 * Date: 2018/5/22
 * Time: 16:43
 */

namespace app\pay\controller\refund_query;
use app\pay\controller\AlipayBase;

class Alipay extends AlipayBase
{
    private $config = [
        'out_trade_no' => '',//订单号
        'out_request_no' => '',//退款单号
    ];
    
    public function __construct(array $config = [])
    {
        parent::__construct();

        if (!isset($config['out_trade_no']) || !isset($config['out_request_no'])){
            return ['code' => 10010,'msg' => config('msg.10010'),'data'=>''];
        }

        if (isset($config['alipay_config'])){
            $this->sel_all_config($config['alipay_config']);
        }

        $this->config['out_trade_no'] = $config['out_trade_no'];
        $this->config['out_request_no'] = $config['out_request_no'];
    }

    /**
     * 退款查询
     * @return array
     */
    public function refund_query()
    {
        vendor('alipay.aop.request.AlipayTradeFastpayRefundQueryRequest');
        $request = new \AlipayTradeFastpayRefundQueryRequest ();
        $request->setBizContent(json_encode($this->config));
        $result = $this->aop->execute($request);
        $response = self::manage_response($result,'alipay_trade_fastpay_refund_query_response');
        if ($response['code'] == 10000 && $response['msg'] == 'Success'){
            return [
                'code' => 200,
                'msg' => config('msg.200'),
                'data' => [
                    'out_request_no' => $response['out_request_no'],
                    'refund_fee' => $response['refund_amount']*100,
                    'total_fee' => $response['total_amount']*100,
                ]
            ];
        }else{
            return [
                'code' => '10067',
                'data' => '',
                'msg' => config('msg.10067')];
        }
    }
}