<?php
/**
 * 短信发送
 * User: linweixin(502510773@qq.com)
 * Date: 2018/2/3
 * Time: 10:54
 */
namespace app\communication\controller\send;
use \app\communication\validate\ValidateFactory;
use app\common\api\communication\ISend;
use app\communication\model\CommCheck;
use think\Config;

class Phone implements ISend {
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
//        $default_config = \config('sms');
        $default_config = config('communication.sms');
        self::$config = array_merge(self::$config, $default_config,$config);
    }

    /**
     * 实现短信发送方法
     * @return array
     */
    public function send(){
        $sms=self::$config;
        $validate = ValidateFactory::send($sms['type']);
        if (!$validate->check($sms)) {
            //发送失败
            $json = [
                'code'  =>400,
                'msg'   => config('msg.400').'!错误信息为：'.$validate->getError(),
            ];
            return $json;
        }
        $time=time();
        $code=getCode($sms['len'],2);
        $expire=isset($sms['expire']) && !empty($sms['expire'])?$sms['expire']:300;
        $config=[
            'apiAccount'    =>  $sms['apiAccount'],                                         //开发者帐号
            'appId'         =>  $sms['appId'],                                              //应用Id
            'sign'          =>  md5($sms['apiAccount'] . $sms['apikey'] . $time),           //签名
            'timeStamp'     =>  $time,                                                      //当前时间戳(精度ms)
            'mobile'        =>  $sms['mobile'],                                                    //接收手机号
            'content'       =>  "【外语通】您好，您的验证码是{$code}，".ceil($expire/60)."分钟内有效，请尽快验证。",  //验证信息内容
            'smsType'       =>  '9',                //短信类型(8:通知、9:验证码、11:营销)
            'userData'      =>  $time,              //流水号(最长64字节)
            'url'           =>  $sms['uri'],        //请求URL
            'code'          =>  $code,              //验证码
            'expire'        =>  $expire                // 验证码过期时间（s）5分钟
        ];
        $phone = new \PhoneCode($config);
        $re_json = $phone->send();
        $re_json = json_decode($re_json, true);

        if ($re_json['status'] === '0') {
            $key = $phone->__get('key');

            //数据入库
            $obj = new CommCheck();
            $result = $obj -> _addRecord($code,$time+$expire,$key);
            if($result['code']){
                //发送成功
                $json = [
                    'code'  =>200,
                    'msg'   => config('msg.200'),
                    'data'  =>[
                        'verify_code'  =>   $code,
                        'mobile'       =>   $sms['mobile'],
                        //'key'          =>   $key
                    ]
                ];
            }else{
                $json = [
                    'code'  =>10006,
                    'msg'   => config('msg.10006').'!!错误信息为：'.$result['msg'],
                    'content'   => $config['content'],
                ];
            }
        }else{
            //发送失败
            $json = [
                'code'  =>400,
                'msg'   => config('msg.400').'!!错误信息为：'.$re_json['desc'],
                'content'   => $config['content'],
            ];
        }
        return $json;
    }

    /**
     * 自定义 发送短信
     * @return array
     */
    public function customSend(){
        $sms=self::$config;
        $validate = ValidateFactory::send('CustomSend');
        if (!$validate->check($sms)) {
            //发送失败
            $json = [
                'code'  =>401,
                'msg'   => config('msg.400').'!错误信息为：'.$validate->getError(),
            ];
            return $json;
        }
        $time=time();
        $config=[
            'apiAccount'    =>  $sms['apiAccount'],                                         //开发者帐号
            'appId'         =>  $sms['appId'],                                              //应用Id
            'sign'          =>  md5($sms['apiAccount'] . $sms['apikey'] . $time),           //签名
            'timeStamp'     =>  $time,                                                      //当前时间戳(精度ms)
            'mobile'        =>  $sms['mobile'],                                                    //接收手机号
            'content'       =>  $sms['content'],    //验证信息内容
            'smsType'       =>  $sms['smsType'],    //短信类型(8:通知、9:验证码、11:营销)
            'userData'      =>  $time,              //流水号(最长64字节)
            'url'           =>  $sms['uri'],        //请求URL
        ];
        $phone = new \PhoneCode($config);
        $re_json = $phone->send();
        $re_json = json_decode($re_json, true);

        if ($re_json['status'] === '0') {
            $json = [
                'code'  =>200,
                'msg'   => config('msg.200'),
                'data'  =>[
                    //'verify_code'  =>   $code,
                    //'mobile'       =>   $sms['mobile'],
                    //'key'          =>   $key
                ]
            ];
        }else{
            //发送失败
            $json = [
                'code'  =>400,
                'msg'   => config('msg.400').'!!错误信息为：'.$re_json['desc'],
                'content'   => $config['content'],
            ];
        }
        return $json;
    }
}