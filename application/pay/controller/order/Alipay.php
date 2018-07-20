<?php
/**
 * Created by crp.
 * User: Administrator
 * Date: 2018/5/21
 * Time: 16:31
 */

namespace app\pay\controller\order;
use app\pay\controller\AlipayBase;
use think\Loader;

class Alipay extends AlipayBase
{
    private $config = [
        /**
         * 支付宝相关配置
         */
        'alipay_config' => [
            'trade_type' => 'SCAN',#支付方式 默认：SCAN:扫码支付   PC:PC网页支付   APP:APP支付
            'notify_url' => '',#回调地址
            'return_url' => '',#PC网页支付成功后，跳转地址
            'time_expire' => 1800,#订单失效时间
        ],

        /**
         * 货物价格配置
         */
        'goods_config' => [
            'out_trade_no' => '',//订单号
            'price' => 0,//原支付价格 单位分 默认1 不为零
            'real_pay' => 1,//实际支付价格 单位分  默认1  不为零
            'remark' => '',//货物描述
            'attach' => '外语通支付接口',//备注
            'good_tags' => '',//商品标记
        ],
    ];

    private $bizcontent_arr = [];//支付宝模型生成参数

    private $order_id = 0;//数据库订单ID
    
    public function __construct(array $config = [])
    {
        parent::__construct();
        if (isset($config['alipay_config'])){
            $this->config['alipay_config'] = array_merge($this->config['alipay_config'],$config['alipay_config']);
            $this->sel_all_config($config['alipay_config']);
        }

        if (isset($config['goods_config'])){
            $this->config['goods_config'] = array_merge($this->config['goods_config'],$config['goods_config']);
        }

        # TODO 订单总金额，默认为实际支付金额
        $this->config['goods_config']['price'] = empty($this->config['goods_config']['price'])?$this->config['goods_config']['real_pay']:$this->config['goods_config']['price'];
        self::create_bizcontent();
    }

    /**
     * 生成模型参数
     */
    private function create_bizcontent(){
        # 支付宝金额定位为元，下单前换算单位
        $real_pay = $this->config['goods_config']['real_pay']/100;
        # 支付宝订单失效时间，取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天，不接受小数点
        $timeout_express = ceil($this->config['alipay_config']['time_expire']/60).'m';
        $this->bizcontent_arr = [
            'body' => $this->config['goods_config']['good_tags'],
            'subject' => $this->config['goods_config']['remark'],
            'out_trade_no' => $this->config['goods_config']['out_trade_no'],//订单号
            'timeout_express' => $timeout_express,
            'total_amount' => $real_pay,//支付金额
        ];
    }

    /**
     * 检查参数配置
     * @return array
     */
    public function check_config()
    {
        $this->config['goods_config']['out_trade_no'] = empty($this->config['goods_config']['out_trade_no'])?ceate_out_trade_no():$this->config['goods_config']['out_trade_no'];

        $validate_goods = Loader::validate('Goods');
        if (!$validate_goods->check($this->config['goods_config'])) {
            return ['code' => 10001,'msg' => $validate_goods->getError(),'data' => ''];
        }
        return ['code' => 200];
    }

    /**
     * 生成支付宝订单
     * @return array
     */
    public function order()
    {
        $check_result = self::check_config();
        if ($check_result['code'] != 200){
            return $check_result;
        }

        # TODO 数据库订单添加，根据数据库实际情况修改
        $order_id = model('Order')->_addOrder($this->config['goods_config']);
        if (!$order_id){
            return ['code' => 10006,'msg' => config('msg.10006'),'data' => ''];
        }

        switch ($this->config['alipay_config']['trade_type']){
            case 'APP':
                return self::app_pay();
                break;
            case 'PC':
                return self::pc_pay();
                break;
            default:
                return self::scan_pay();
                break;
        }
    }

    /**
     * 生成APP支付
     * @return array
     */
    private function app_pay()
    {
        Vendor('alipay.aop.request.AlipayTradeAppPayRequest');
        $request = new \AlipayTradeAppPayRequest();
        $this->bizcontent_arr['product_code'] = 'QUICK_MSECURITY_PAY';
        $bizcontent = json_encode($this->bizcontent_arr);
        $request->setNotifyUrl($this->config['alipay_config']['notify_url']);
        $request->setBizContent($bizcontent);
        $response = $this->aop->sdkExecute($request);
        return [
            'code' => 200,
            'msg' => config('msg.200'),
            'data' => [
                'order_info' => $response,#APP下单使用参数
                'out_trade_no' => $this->config['goods_config']['out_trade_no'],
            ]
        ];
    }

    /**
     * 生成PC网页支付
     * @return array
     */
    # TODO ISV权限不足
    private function pc_pay()
    {
        Vendor('alipay.aop.request.AlipayTradePagePayRequest');
        $request = new \AlipayTradePagePayRequest ();
        $request->setReturnUrl($this->config['alipay_config']['return_url']);
        $request->setNotifyUrl($this->config['alipay_config']['notify_url']);
        $this->bizcontent_arr['product_code'] = "FAST_INSTANT_TRADE_PAY";
        $request->setBizContent(json_encode($this->bizcontent_arr));
        $pagePayResult = $this->aop->pageExecute($request);
        echo $pagePayResult;
    }

    /**
     * 生成扫码支付
     * @return array
     */
    private function scan_pay()
    {
        vendor('alipay.aop.request.AlipayTradePrecreateRequest');
        $request = new \AlipayTradePrecreateRequest ();
        $request->setNotifyUrl($this->config['alipay_config']['notify_url']);
        $request->setBizContent(json_encode($this->bizcontent_arr));
        $result = $this->aop->execute($request);
        $response = self::manage_response($result);
        if (!empty($response) && isset($response['qr_code']) && $response['code'] == '10000'){
            return ['code' => '200','data' => ['pay_url'=>$response['qr_code'],'out_trade_no'=>$this->config['goods_config']['out_trade_no']],'msg' => config('msg.200')];
        }else{
            # TODO 删除失败订单，根据数据库实际情况修改
            db('order')->delete($this->order_id);//支付宝下单失败删除数据库订单
            return ['code' => '10068','data' => '','msg' => config('msg.10068')];
        }
    }
}