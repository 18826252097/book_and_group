<?php
/**
 * Created by PhpStorm.
 * User: Administrator- mazefeng<1220441774@qq.com>
 * Date: 2017/12/9 0009
 * Time: 下午 2:12
短信接口
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
    ];