<?php
/**
 * 支付宝公共支付
 * User: renqichun(605785215@qq.com)
 * Date: 2018/2/8
 */
namespace app\common\controller;
use think\Controller;


vendor('alipay.pagepay.service.AlipayTradeService');

/**
 * 公共alipay支付
 * Class Alipay
 * @package app\common\controller\pay
 */
class Alipaycommon extends Controller
{
    /**
     * 执行支付宝支付操作
     * @param $arr
     * @return bool|mixed|\SimpleXMLElement|string|\提交表单HTML文本
     */
    public function alipay_handle($arr)
    {
        //构造参数
        vendor('alipay.pagepay.buildermodel.AlipayTradePagePayContentBuilder');
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($arr['body']);
        $payRequestBuilder->setSubject($arr['subject']);
        $payRequestBuilder->setTotalAmount($arr['total_amount']);
        $payRequestBuilder->setOutTradeNo($arr['out_trade_no']);


        $config = config('alipay_config.config');
        $aop = new \AlipayTradeService($config);

        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
         */
        $response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

        //输出表单
        return $response;
    }

    /**
     * 查询订单信息
     * @param $arr
     * @return object| \查询的结果对象
     */
    public function search_order($arr){

        vendor('alipay.pagepay.buildermodel.AlipayTradeQueryContentBuilder');
        //构造参数
        $RequestBuilder = new \AlipayTradeQueryContentBuilder();
        $RequestBuilder->setOutTradeNo($arr['out_trade_no']);
        $RequestBuilder->setTradeNo($arr['trade_no']);

        $config = config('alipay_config.config');

        $aop = new \AlipayTradeService($config);

        /**
         * alipay.trade.query (统一收单线下交易查询)
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @return $response 支付宝返回的信息
         */
        return  $aop->Query($RequestBuilder);

    }


    /**
     * 初始化配置
     * @param array $config
     */
    public function __construct(array $config = []){

    }

    /**
     * 下单
     * @return mixed
     */
    public function create_order(){

    }

    /**
     * 补单
     * @return mixed
     */
    public function supplement_order(){

    }



    /**
     * 支付回调
     * @return mixed
     */
    public function notify(){

    }
}