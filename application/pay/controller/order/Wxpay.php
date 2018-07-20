<?php
/**
 * Created by crp.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 11:09
 */

namespace app\pay\controller\order;


use think\Loader;

class Wxpay
{
    private $config = [
        /**
         * 微信相关配置
         */
        'weixin_config' => [
            'trade_type' => 'NATIVE',//微信支付方式 默认：NATIVE
            'appid' => '',//支付APPID  使用JSAPI支付方式时使用，为空默认使用微信WxPay.Config中配置的appid
            'openid' => '',//对应APPID下 微信用户的唯一标识openid  使用JSAPI支付方式时必填
            'mch_id' => '',//商户ID
            'key' => '',//商户支付密钥
            'app_secert' => '',//公众帐号secert
            'ssl_cert_path' => '',//微信cert证书
            'ssl_key_path' => '',//微信key证书
            'notify_url' => '',//微信支付成功回调地址
            'time_expire' => 1800,//订单失效时间  单位：秒  默认30分钟
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

    private $order_id = 0;//数据库订单ID

    /**
     * 构造函数
     * Wxpay constructor.
     * @param array $config 微信配置 货物配置
     */
    public function __construct(array $config = [])
    {
        vendor('WxpayAPI_php_v3_0_1.lib.WxPay','.Data.php');//微信支付函数包
        vendor('WxpayAPI_php_v3_0_1.lib.WxPayApi','.php');//微信支付Api

        if (isset($config['weixin_config'])){
            $this->config['weixin_config'] = array_merge($this->config['weixin_config'],$config['weixin_config']);
        }

        if (isset($config['goods_config'])){
            $this->config['goods_config'] = array_merge($this->config['goods_config'],$config['goods_config']);
        }

        $this->config['goods_config']['price'] = empty($this->config['goods_config']['price'])?$this->config['goods_config']['real_pay']:$this->config['goods_config']['price'];
    }

    /**
     * 验证配置信息
     * @return array
     */
    public function check_config()
    {
        $this->config['goods_config']['out_trade_no'] = empty($this->config['goods_config']['out_trade_no'])?ceate_out_trade_no():$this->config['goods_config']['out_trade_no'];

        $validate_wechat = Loader::validate('Wechat');
        if (!$validate_wechat->check($this->config['weixin_config'])){
            return ['code' => 10001,'msg' => $validate_wechat->getError(),'data' => ''];
        }
        $validate_goods = Loader::validate('Goods');
        if (!$validate_goods->check($this->config['goods_config'])) {
            return ['code' => 10001,'msg' => $validate_goods->getError(),'data' => ''];
        }
        return ['code' => 200];
    }

    /**
     * 生成订单
     */
    public function order()
    {
        $check_result = $this->check_config();
        if ($check_result['code'] !== 200){
            return $check_result;
        }

        $order_id = model('Order')->_addOrder($this->config['goods_config']);

        if (!$order_id){
            return ['code' => 10006,'msg' => config('msg.10006'),'data' => ''];
        }

        switch ($this->config['weixin_config']['trade_type']){
            case 'APP':
                $result = $this->app_pay();
                break;
            case 'JSAPI':
                $result = $this->jsapi_pay();
                break;
            default:
                $result = $this->native_pay();
                break;
        }

        return $result;
    }

    /**
     * 微信NATIVE支付
     * @return array
     */
    private function native_pay(){
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($this->config['goods_config']['remark']);//设置商品或支付单简要描述
        $input->SetAttach($this->config['goods_config']['attach']);//设置附加数据，在查询API和支付通知中原样返回
        $input->SetOut_trade_no($this->config['goods_config']['out_trade_no']);//设置商户系统内部的订单号 生成唯一订单号
        $input->SetTotal_fee($this->config['goods_config']['real_pay']);//设置订单总金额，只能为整数，详见支付金额  单位为：分$pay_info['real_pay']
        $input->SetTime_start(date("YmdHis"));//设置订单生成时间
        $input->SetTime_expire(date("YmdHis", time() + $this->config['weixin_config']['time_expire']));//设置订单失效时间
        $input->SetGoods_tag($this->config['goods_config']['remark']);//设置商品标记
        $input->SetTrade_type("NATIVE");//NATIVE支付
        $input->SetProduct_id($this->config['goods_config']['out_trade_no']);//数据库订单号  即用户自己保存的订单号

        self::set_all_input($input);
        //dump($input);die;
        $result = $this->GetPayUrl($input);
        if (!isset($result['code_url'])){
            db('order')->delete($this->order_id);//微信下单失败删除数据库订单
            return ['code' => '10068','data' => '','msg' => isset($result['err_code_des'])?$result['err_code_des']:$result['return_msg']];
        }else{
            return ['code' => '200','data' => ['pay_url'=>$result['code_url'],'out_trade_no'=>$this->config['goods_config']['out_trade_no']],'msg' => config('msg.200')];
        }
    }

    /**
     * 生成直接支付url，支付url有效期为2小时
     * @param $input
     * @return \成功时返回
     */
    private function GetPayUrl(\WxPayUnifiedOrder $input)
    {
        if($input->GetTrade_type() == "NATIVE")
        {
            $result = \WxPayApi::unifiedOrder($input);
            return $result;
        }
    }

    /**
     * 微信JSAPI支付
     * @return array
     */
    public function jsapi_pay()
    {
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($this->config['goods_config']['remark']);//设置商品或支付单简要描述
        $input->SetOut_trade_no($this->config['goods_config']['out_trade_no']);//设置商户系统内部的订单号 生成唯一订单号
        $input->SetTotal_fee($this->config['goods_config']['real_pay']);//设置订单总金额，只能为整数，详见支付金额  单位为：分$pay_info['real_pay']
        $input->SetTrade_type("JSAPI");//JSAPI支付
        $input->SetOpenid($this->config['weixin_config']['openid']);//小程序下的用户openid
        self::set_all_input($input);

        $order = \WxPayApi::unifiedOrder($input);//向微信统一下单
        $resu = self::getJsApiParameters($order);//获取小程序支付需要参数
        if (!isset($resu['paySign'])){
            db('order')->delete($this->order_id);//微信下单失败删除数据库订单
            return $resu;
        }else{
            return [
                'code' => '200',
                'data' => [
                    'parameters'=> $resu,
                    'out_trade_no'=>$this->config['goods_config']['out_trade_no'],
                ],
                'msg' => config('msg.200')
            ];
        }
    }

    /**
     * 获取JSAPI支付 必需参数
     * @param $UnifiedOrderResult 统一下单接口返回对象
     * @return array
     */
    private function getJsApiParameters($UnifiedOrderResult)
    {
        if(!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == "")
        {
            return ['code' => '10068','data' => '','msg' => isset($UnifiedOrderResult['err_code_des'])?$UnifiedOrderResult['err_code_des']:$UnifiedOrderResult['return_msg']];
        }
        $jsapi = new \WxPayJsApiPay();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(\WxPayApi::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
        $jsapi->SetSignType("MD5");
        if (!empty($this->config['weixin_config']['key'])){
            $jsapi->Set_key($this->config['weixin_config']['key']);
        }
        $jsapi->SetPaySign($jsapi->MakeSign($jsapi));
        return $jsapi->GetValues();
    }

    /**
     * 微信自定义配置
     * @param \WxPayUnifiedOrder $input
     */
    private function set_all_input(\WxPayUnifiedOrder &$input){
        //设置APPID
        if (!empty($this->config['weixin_config']['appid'])){
            $input->SetAppid($this->config['weixin_config']['appid']);
        }
        //设置商户号
        if (!empty($this->config['weixin_config']['mch_id'])){
            $input->SetMch_id($this->config['weixin_config']['mch_id']);
        }
        //设置回调地址
        if (!empty($this->config['weixin_config']['notify_url'])){
            $input->SetNotify_url($this->config['weixin_config']['notify_url']);
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
     * 微信APP支付
     * @return array
     */
    private function app_pay(){
        $input = new \WxPayUnifiedOrder();
        $input->SetAppid($this->config['weixin_config']['appid']);
        $input->SetBody($this->config['goods_config']['remark']);//设置商品或支付单简要描述
        $input->SetAttach($this->config['goods_config']['attach']);//设置附加数据，在查询API和支付通知中原样返回
        $input->SetOut_trade_no($this->config['goods_config']['out_trade_no']);//设置商户系统内部的订单号 生成唯一订单号
        $input->SetTotal_fee($this->config['goods_config']['real_pay']);//设置订单总金额，只能为整数，详见支付金额  单位为：分$pay_info['real_pay']
        $input->SetTrade_type("APP");//APP支付
        self::set_all_input($input);
        $order = \WxPayApi::unifiedOrder($input);//向微信统一下单
        if(!array_key_exists("appid", $order)
            || !array_key_exists("prepay_id", $order)
            || $order['prepay_id'] == "")
        {
            return ['code' => '10004','data' => '','msg' => isset($order['err_code_des'])?$order['err_code_des']:$order['return_msg']];
        }
        $pay_app = new \WxPayAppPay();
        $pay_app->SetAppid($order["appid"]);
        $timeStamp = time();
        $pay_app->SetTimeStamp("$timeStamp");
        $pay_app->SetNonceStr(\WxPayApi::getNonceStr());
        $pay_app->SetPackage("Sign=WXPay");
        $pay_app->SetPrepayid($order['prepay_id']);

        if (!empty($this->config['weixin_config']['mch_id'])){
            $pay_app->SetPartnerid($this->config['weixin_config']['mch_id']);
        }
        if (!empty($this->config['weixin_config']['key'])){
            $pay_app->Set_key($this->config['weixin_config']['key']);
        }

        $pay_app->SetSign($pay_app);
        $resu = $pay_app->GetValues();
        return [
            'code' => '200',
            'data' => [
                'parameters'=> $resu,
                'out_trade_no'=>$this->config['goods_config']['out_trade_no'],
            ],
            'msg' => config('lang.200')
        ];
    }
}