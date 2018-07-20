<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/22
 * Time: 14:32
 */

namespace app\pay\controller;


class AlipayBase
{
    protected $aop = '';

    /**
     * 实例化 AopClient 对象
     * AlipayBase constructor.
     */
    protected function __construct()
    {
        Vendor('alipay.aop.AopClient');
        $config = config('alipay_config.WYT_ALI_PAY_CONFIG');
        $aop = new \AopClient();
        $aop->gatewayUrl = $config['gatewayUrl'];
        $aop->appId = $config["app_id"];
        $aop->rsaPrivateKey = $config['merchant_private_key'];
        $aop->format = "json";
        $aop->charset = $config['charset'];
        $aop->signType = $config['sign_type'];
        $aop->alipayrsaPublicKey = $config['alipay_public_key'];
        $this->aop = $aop;
    }

    /**
     * 整理支付宝返回数据
     * @param $response
     * @param string $key 支付宝返回对象名称
     * @return array
     */
    protected function manage_response($response,$key = 'alipay_trade_precreate_response')
    {

        $response = json_decode(json_encode($response),true);
        if (isset($response[$key])){
            return $response[$key];
        }else{
            return ['code'=>'0','msg'=>'FAIL'];
        }
    }

    protected function sel_all_config($ali_config)
    {
        $this->aop->gatewayUrl = !empty($ali_config['gatewayUrl'])?$ali_config['gatewayUrl']:$this->aop->gatewayUrl;
        $this->aop->appId = !empty($ali_config['app_id'])?$ali_config['app_id']:$this->aop->appId;
        $this->aop->rsaPrivateKey = !empty($ali_config['merchant_private_key'])?$ali_config['merchant_private_key']:$this->aop->rsaPrivateKey;
        $this->aop->format = "json";
        $this->aop->charset = !empty($ali_config['charset'])?$ali_config['charset']:$this->aop->charset;
        $this->aop->signType = !empty($ali_config['sign_type'])?$ali_config['sign_type']:$this->aop->signType;
        $this->aop->alipayrsaPublicKey = !empty($ali_config['alipay_public_key'])?$ali_config['alipay_public_key']:$this->aop->alipayrsaPublicKey;
    }
}