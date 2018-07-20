<?php
/**
 * Created by PhpStorm.
 * User: Administrator- mazefeng<1220441774@qq.com>
 * Date: 2017/12/9 0009
 * Time: 下午 5:06
 */
return [
    'sms'=>[
        'isp'=>[
            134 ,135, 136 ,137 ,138, 139 ,147 ,148, 150, 151, 152, 157, 158 ,159, 172 ,178, 182, 183 ,184, 187, 188, 198, //移动号段
            130 ,131, 132, 145 ,146 ,155 ,156, 166, 171, 175, 176, 185, 186, //联通
            133, 149 ,153, 173, 174 ,177, 180, 181, 189, 199,   //电信
            170 //虚拟isp
        ], //version:2017年12月1日
        'apiAccount'    =>  '', //uri 的account参数
        'appId'         =>  '',
        'apikey'        =>  '',
        'uri'           =>  'http://www.zypaas.com:9988/V1/Account/ACCaedc912274f74ba0aa8eaac6d044e909/sms/matchTemplateSend',
        'config' => [
            'apiAccount'    =>  '',         //开发者帐号
            'appId'         =>  '',         //应用Id
            'sign'          =>  '',         //签名
            'timeStamp'     =>  '',         //当前时间戳(精度ms)
            'mobile'        =>  '',         //接收手机号
            'content'       =>  '',         //验证信息内容
            'smsType'       =>  '8',        //短信类型(8:通知、9:验证码、11:营销)
            'userData'      =>  '',         //流水号(最长64字节)
            'url'           =>  '',         //请求URL
            'code'          =>  '',         //验证码
            'expire'        =>  300      // 验证码过期时间（s）5分钟
        ],
    ],
    'mail' => array(
        //163网易配置
        'SMTP_HOST'   => 'smtp.163.com', //SMTP服务器
        'SMTP_PORT'   => '465', //SMTP服务器端口
        'SMTP_USER'   => '', //SMTP服务器用户名
        'SMTP_PASS'   => '', //SMTP服务器密码
        'FROM_EMAIL'  => '', //发件人EMAIL
        'FROM_NAME'   => '', //发件人名称
        'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
        'REPLY_NAME'  => '', //回复名称（留空则为发件人名称)
        // qq腾讯配置
        /*'SMTP_HOST'   => 'smtp.qq.com', //SMTP服务器
        'SMTP_PORT'   => '465', //SMTP服务器端口
        'SMTP_USER'   => '3501684837@qq.com', //SMTP服务器用户名
        'SMTP_PASS'   => 'dkiyuljxdpcpchbf', //SMTP服务器密码
        'FROM_EMAIL'  => '3501684837@qq.com', //发件人EMAIL
        'FROM_NAME'   => '格灵国际在线', //发件人名称
        'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
        'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）*/
    ),
];