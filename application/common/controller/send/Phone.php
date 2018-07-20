<?php
/**
 * 短信发送
 * User: linweixin(502510773@qq.com)
 * Date: 2018/2/3
 * Time: 10:56
 */
namespace app\common\controller\send;
use app\common\api\common\ISend;
use think\Config;

class Phone implements ISend{
    // 配置参数
    protected static $config = [
        'mobile'      => null,    //短信发送的号码
        'len'         => 0,       //字符串长度
        'code'        => 0,       //验证码
    ];
    /**
     * 初始化配置
     * @param array $config
     */
    public function __construct(array $config = []){
        $email_config = Config::get('sms');
        self::$config = array_merge(self::$config, $email_config,$config);
    }
    /**
     * 发送
     * @return mixed
     */
    public function send(){
        $sms=self::$config;
        $time=time();
        $code=getCode($sms['len'],2);
        $config=[
            'apiAccount'    =>  $sms['apiAccount'],                                         //开发者帐号
            'appId'         =>  $sms['appId'],                                              //应用Id
            'sign'          =>  md5($sms['apiAccount'] . $sms['apikey'] . $time),           //签名
            'timeStamp'     =>  $time,                                                      //当前时间戳(精度ms)
            'mobile'        =>  $sms['mobile'],                                                    //接收手机号
            'content'       =>  "【外语通】您好，您的验证码是{$code}，5分钟内有效，请尽快验证。",  //验证信息内容
            'smsType'       =>  '9',                //短信类型(8:通知、9:验证码、11:营销)
            'userData'      =>  $time,              //流水号(最长64字节)
            'url'           =>  $sms['uri'],        //请求URL
            'code'          =>  $code,              //验证码
            'expire'        =>  1800                // 验证码过期时间（s）5分钟
        ];

        $phone = new \PhoneCode($config);
        $re_json = $phone->send();
        $re_json = json_decode($re_json, true);
        if ($re_json['status'] === '0') {
            //发送成功
            $json = [
                'code'  =>200,
                'msg'   => config('msg.200'),
                'data'  =>[
                    'verify_code'   =>  $code,
                    'mobile'        => $sms['mobile'],
                    'time'          =>  date('Y-m-d H:i:s')
                ]
            ];
        }else{
            //发送失败
            $json = [
                'code'  =>400,
                'msg'   => config('msg.400'),
            ];
        }
        return $json;
    }
    /**
     * 发送
     * @param $code
     * @return mixed
     */
    public function check($code){
        $sms=self::$config;
        $time=time();
        //$code=getCode($sms['len'],2);
        $config=[
            'apiAccount'    =>  $sms['apiAccount'],                                         //开发者帐号
            'appId'         =>  $sms['appId'],                                              //应用Id
            'sign'          =>  md5($sms['apiAccount'] . $sms['apikey'] . $time),           //签名
            'timeStamp'     =>  $time,                                                      //当前时间戳(精度ms)
            'mobile'        =>  $sms['mobile'],                                                    //接收手机号
            'content'       =>  "【外语通】您好，您的验证码是{$code}，5分钟内有效，请尽快验证。",  //验证信息内容
            'smsType'       =>  '9',                //短信类型(8:通知、9:验证码、11:营销)
            'userData'      =>  $time,              //流水号(最长64字节)
            'url'           =>  $sms['uri'],        //请求URL
            'code'          =>  $code,              //验证码
            'expire'        =>  1800                // 验证码过期时间（s）5分钟
        ];

        $phone = new \PhoneCode($config);
        $re_json = $phone->check($code);
        $re_json = json_decode($re_json, true);
        //dump($re_json);
        return $re_json;
    }
}