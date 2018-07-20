<?php
/**
 * +--------------------------------------------------------
 * | 支付宝支付配置参数
 * User: renqichun
 * Date: 2018/02/06
 * +--------------------------------------------------------
 */
return [
    /*外语通支付宝支付*/
    'WYT_ALI_PAY_CONFIG' => [
        //签名方式,默认为RSA2(RSA2048)
        'sign_type' => "RSA2",

        //支付宝公钥
        'alipay_public_key' => "",

        //商户私钥
        'merchant_private_key' => "",

        //编码格式
        'charset' => "utf-8",

        //支付宝网关
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

        //应用ID
        'app_id' => "",

        //异步通知地址,只有扫码支付预下单可用
        'notify_url' => "",

        //最大查询重试次数
        'MaxQueryRetry' => "10",

        //查询间隔
        'QueryDuration' => "5",

        //显示公司信息
        'company'   => '',

        //日志文件
        // 'log_file' => './log/'.date('Ym').'/'.date('d').'.log',
        'log_file' => ROOT_PATH.'runtime/log/'.date('Ym').'/'.date('d').'.log',
    ],
];
