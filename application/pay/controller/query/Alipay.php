<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/22
 * Time: 14:26
 */

namespace app\pay\controller\query;
use app\pay\controller\AlipayBase;

class Alipay extends AlipayBase
{
    private $config = [
        'out_trade_no' => '',
    ];

    public function __construct(array $config = [])
    {
        parent::__construct();

        if (!isset($config['out_trade_no'])){
            return ['code' => 10010,'msg' => config('msg.10010'),'data'=>''];
        }
        if (isset($config['alipay_config'])){
            $this->sel_all_config($config['alipay_config']);
        }

        $this->config['out_trade_no'] = $config['out_trade_no'];
    }

    /**
     * 查询订单是否支付
     * @return array
     */
    public function query()
    {
        Vendor('Alipay.aop.request.AlipayTradeQueryRequest');
        $request = new \AlipayTradeQueryRequest();
        $request->setBizContent(json_encode($this->config));
        $query_result = $this->aop->execute($request);
        $response = self::manage_response($query_result,'alipay_trade_query_response');
        if (!empty($response) && $response['code'] == '10000'
            && ($response['trade_status'] == 'TRADE_SUCCESS' || $response['trade_status'] == 'TRADE_FINISHED')){
            # TODO 支付成功后操作 根据实际情况修改
            return model('order')->after_payment($this->config['out_trade_no']);
        }else{
            return ['code'=>10069,config('msg.10069'),'data'=>''];
        }
    }
}