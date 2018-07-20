<?php
/**
 * Created by crp.
 * User: Administrator
 * Date: 2018/5/23
 * Time: 9:22
 */

namespace app\pay\controller\close;
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
     * 关闭订单
     * @return array
     */
    public function close()
    {
        Vendor('alipay.aop.request.AlipayTradeCloseRequest');
        $request = new \AlipayTradeCloseRequest ();
        $request->setBizContent(json_encode($this->config));
        $result = $this->aop->execute($request);
        $response = self::manage_response($result,'alipay_trade_close_response');

        if ($response['code'] == 10000 && $response['msg'] == 'Success'){
            return ['code'=>200,'msg'=>config('msg.200'),'data'=>''];
        }else{
            return ['code'=>10071,'msg'=>config('msg.10071'),'data'=>''];
        }
    }
}