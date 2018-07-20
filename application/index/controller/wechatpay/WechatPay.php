<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 16:03
 */
namespace app\index\controller\wechatpay;

use think\Controller;
use think\Request;

class WechatPay extends Controller
{
    /**
     * 微信native支付demo
     */
    public function index()
    {
        $data = array(
            'type'=>'wxpay',
            'weixin_config' => [
                'trade_type' => 'NATIVE',

                'appid' => '',
                'mch_id' => '',//商户ID
                'key' => '',//商户支付密钥
                'app_secert' => '',//公众帐号secert
                'notify_url' => '',//微信支付成功回调地址
            ],
            'goods_config' => [
                'out_trade_no' => time().getCode(6),//订单号
                'real_pay' => 1,//支付价格 单位分  默认1  不为零
                'remark' => '测试接口2',//货物描述
                'attach' => '外语通支付接口',//备注
                'good_tags' => 'iexue',//商品标记
            ],
        );
        $res = getapi($data,'/pay/Api/order');//调用邮箱注册接口
        $res = decodeData($res);//解析返回参数
        if (isset($res['data']['data']['pay_url'])){
            $res['data']['data']['pay_url'] = 'http://127.0.0.1:1314'.url('set_qrcode','','').'/url_code/'.base64_encode($res['data']['data']['pay_url']);
        }
        echo '<pre>'.print_r($res,true).'</pre>';
    }

    /**
     * 微信jsapi支付demo
     */
    public function jsapi_pay()
    {
        $data = array(
            'type'=>'wxpay',
            'weixin_config' => [
                'trade_type' => 'JSAPI',
                'appid' => '',
                'openid' => '',

                'mch_id' => '',//商户ID
                'key' => '',//商户支付密钥
                'app_secert' => '',//公众帐号secert
                'notify_url' => '',//微信支付成功回调地址
                'time_expire' => 1800,//订单失效时间  单位：秒  默认30分钟
            ],
            'goods_config' => [
                'out_trade_no' => time().getCode(6),//订单号
                'real_pay' => 2,//支付价格 单位分  默认1  不为零
                'remark' => '测试接口2',//货物描述
                'attach' => '外语通支付接口',//备注
                'good_tags' => 'iexue',//商品标记
            ],
        );
        $res = getapi($data,'/pay/Api/order');//调用邮箱注册接口
        $res = decodeData($res);//解析返回参数
        echo '<pre>'.print_r($res,true).'</pre>';
    }

    /**
     * 查询订单demo
     */
    public function query_order()
    {
        $data = [
            'type' => 'wxpay',
            'out_trade_no' => '15289437069kDTpR',
            'weixin_config' =>[
                'appid' => '',
                'mch_id' => '',//商户ID
                'key' => '',//商户支付密钥
                'app_secert' => '',//公众帐号secert
            ]
        ];
        $res = getapi($data,'/pay/Api/query');//调用邮箱注册接口
        $res = decodeData($res);//解析返回参数
        echo '<pre>'.print_r($res,true).'</pre>';
    }

    /**
     * 微信关闭订单demo
     */
    public function close_order()
    {
        $data = [
            'type' => 'wxpay',
            'out_trade_no' => '15268872607etBQi'
        ];
        $res = getapi($data,'/pay/Api/close');//调用邮箱注册接口
        $res = decodeData($res);//解析返回参数
        echo '<pre>'.print_r($res,true).'</pre>';
    }

    /**
     * PHP url生成二维码 crp
     */
    public function set_qrcode(Request $request)
    {
        vendor('Phpqrcode.phpqrcode');
        $url_code = $request->param('url_code','');
        $url = base64_decode($url_code);
        ob_end_clean();
        \QRcode::png($url);
    }

    /**
     * 申请退款demo
     */
    public function refund_order()
    {
        $data = [
            'type' => 'wxpay',
            'out_trade_no' => '1528965067DyhK6E',
            'weixin_config' => [
                'ssl_cert_path' => '',//微信cert证书
                'ssl_key_path' => '',//微信key证书
                'appid' => '',
                'mch_id' => '',//商户ID
                'key' => '',//商户支付密钥
                'app_secert' => '',//公众帐号secert
                'notify_url' => '',//微信支付成功回调地址
            ]
        ];

        $res = getapi($data,'/pay/Api/refund');//调用邮箱注册接口
        $res = decodeData($res);//解析返回参数
        echo '<pre>'.print_r($res,true).'</pre>';
    }

    /**
     * 查询退款信息
     */
    public function query_redund()
    {
        $data = [
            'type' => 'wxpay',
            'out_trade_no' => '1526871135R9yrzA'
        ];
        $res = getapi($data,'/pay/Api/refund_query');
        $res = decodeData($res);
        echo '<pre>'.print_r($res,true).'</pre>';
    }

    public function app_order()
    {
        $data = array(
            'type'=>'wxpay',
            'weixin_config' => [
                'trade_type' => 'APP',
                'appid' => '',
                'mch_id' => '',//商户ID
                'key' => '',//商户支付密钥
                'app_secert' => '',//公众帐号secert
                'notify_url' => '',//微信支付成功回调地址
            ],
            'goods_config' => [
                'out_trade_no' => time().getCode(6),//订单号
                'real_pay' => 1,//支付价格 单位分  默认1  不为零
                'remark' => '测试接口2',//货物描述
                'attach' => '外语通支付接口',//备注
                'good_tags' => 'iexue',//商品标记
            ],
        );
        $res = getapi($data,'/pay/Api/order');//调用邮箱注册接口
        $res = decodeData($res);//解析返回参数
        if (isset($res['data']['data']['pay_url'])){
            $res['data']['data']['pay_url'] = 'http://127.0.0.1:1314'.url('set_qrcode','','').'/url_code/'.base64_encode($res['data']['data']['pay_url']);
        }
        echo '<pre>'.print_r($res,true).'</pre>';
    }
}