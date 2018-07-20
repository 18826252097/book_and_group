<?php
/**
 * Created by crp.
 * User: Administrator
 * Date: 2018/5/22
 * Time: 16:06
 */

namespace app\pay\controller\refund;
use app\pay\controller\AlipayBase;

class Alipay extends AlipayBase
{
    private $config = [
        'out_refund_no' => '',//退款单号 部分退款时必填，多次退款使用不同单号，退款失败时退款单号不变动
        'out_trade_no' => '',//订单号
        'refund_amount' => 0,//退款金额 单位：分
    ];

    private $total_fee = 0;//订单总金额 单位：分

    public function __construct(array $config = [])
    {
        parent::__construct();

        if (!isset($this->config['out_trade_no'])){
            return ['code' => 10010,'msg' => config('msg.10010'),'data'=>''];
        }

        if (isset($config['alipay_config'])){
            $this->sel_all_config($config['alipay_config']);
        }

        $this->config['out_trade_no'] = $config['out_trade_no'];
        //退款单号为空时 默认为订单号
        $this->config['out_refund_no'] = isset($config['out_refund_no'])?$config['out_refund_no']:$this->config['out_trade_no'];
        $this->config['refund_amount'] = isset($config['refund_fee'])?$config['refund_fee']:0;
    }

    /**
     * 执行退款
     * @return array
     */
    public function refund()
    {
        $check_result = self::check_refund($this->config['out_trade_no'],$this->config['refund_amount'],$this->total_fee);
        if ($check_result['code'] != 200){
            return $check_result;
        }
        vendor('alipay.aop.request.AlipayTradeRefundRequest');
        $request = new \AlipayTradeRefundRequest();
        $this->config['refund_amount'] = $this->config['refund_amount']/100;
        $request->setBizContent(json_encode($this->config));
        $result = $this->aop->execute($request);
        $response = self::manage_response($result,'alipay_trade_refund_response');
        if (!empty($response) && $response['code'] == '10000' && $response['fund_change'] == 'Y'){
            return [
                'code' => 200,
                'msg' => config('msg.200'),
                'data' => [
                    'refund_fee' => $this->config['refund_amount']*100,//退款金额 单位：分
                    'out_refund_no' => $this->config['out_refund_no'],//退款单号
                    'total_fee' => $this->total_fee,//订单总金额
                ]
            ];
        }else{
            return [
                'code' => '10070',
                'data' => '',
                'msg' => config('msg.10070')];
        }
    }

    /**
     * 检查订单信息
     * @return array
     */
    private function check_refund($out_trade_no,&$refund_fee,&$total_fee)
    {
        # TODO 检查订单相关信息 根据数据库实际情况修改
        $order = db('order')->field('real_pay')->where(['out_trade_no'=>$out_trade_no])->find();
        $flag = true;
        switch ($flag){
            case empty($order)://判断订单是否存在
                return ['code'=>10071,'msg'=>config('msg.10071'),'data'=>''];
                break;
            case $order['real_pay'] < $refund_fee://对比订单总额和退款金额
                return ['code'=>10072,'msg'=>config('msg.10072'),'data'=>''];
            case $refund_fee <= 0://验证退款金额 为0是默认全部退款
                $refund_fee = $total_fee = $order['real_pay'];
                break;
            default:
                break;
        }

        return ['code'=>200];
    }
}