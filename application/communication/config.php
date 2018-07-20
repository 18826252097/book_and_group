<?php
/**
 * 用户模块配置
 * User: lwx(502510773@qq.com)
 * Date: 2018/2/8
 * Time: 14:37
 */
return [
    //短信配置
    'sms'=>[
        'isp'=>[
            134 ,135, 136 ,137 ,138, 139 ,147 ,148, 150, 151, 152, 157, 158 ,159, 172 ,178, 182, 183 ,184, 187, 188, 198, //移动号段
            130 ,131, 132, 145 ,146 ,155 ,156, 166, 171, 175, 176, 185, 186, //联通
            133, 149 ,153, 173, 174 ,177, 180, 181, 189, 199,   //电信
            170 //虚拟isp
        ], //version:2017年12月1日
        'apiAccount'    =>  'ACCaedc912274f74ba0aa8eaac6d044e909', //uri 的account参数
        'appId'         =>  'APP6ecd0e2dcecc4d3cbc98aa425db087eb',
        'apikey'        =>  'APIe0ffe44b5b0249d4b8e5616d1ad56377',
        'uri'=>'http://www.zypaas.com:9988/V1/Account/ACCaedc912274f74ba0aa8eaac6d044e909/sms/matchTemplateSend',
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
        'SMTP_HOST'   => 'smtp.163.com', //SMTP服务器
        'SMTP_PORT'   => '465', //SMTP服务器端口
        'SMTP_USER'   => '18819493724@163.com', //SMTP服务器用户名
        'SMTP_PASS'   => '123QAZ000', //SMTP服务器密码
        'FROM_EMAIL'  => '18819493724@163.com', //发件人EMAIL
        'FROM_NAME'   => '爱易学', //发件人名称
        'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
        'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）
    ),
];