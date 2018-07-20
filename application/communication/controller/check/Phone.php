<?php
/**
 * 短信发送
 * User: linweixin(502510773@qq.com)
 * Date: 2018/2/3
 * Time: 10:54
 */
namespace app\communication\controller\check;
use \app\communication\validate\ValidateFactory;
use app\common\api\communication\ICheck;
use app\communication\model\CommCheck;
use think\Config;

class Phone implements ICheck{
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
        $default_config = \config('communication.sms');
        self::$config = array_merge(self::$config, $default_config,$config);
    }
    /**
     * 发送
     * @param $code
     * @return mixed
     */
    public function check(){
        $sms=self::$config;
        $validate = ValidateFactory::check($sms['type']);
        if (!$validate->check($sms)) {
            //发送失败
            $json = [
                'code'  =>400,
                'msg'   => config('msg.400').'!错误信息为：'.$validate->getError(),
            ];
            return $json;
        }
        $time=time();
        $expire=isset($sms['expire']) && !empty($sms['expire'])?$sms['expire']:300;
        $config=[
            'apiAccount'    =>  $sms['apiAccount'],                                         //开发者帐号
            'appId'         =>  $sms['appId'],                                              //应用Id
            'sign'          =>  md5($sms['apiAccount'] . $sms['apikey'] . $time),           //签名
            'timeStamp'     =>  $time,                                                      //当前时间戳(精度ms)
            'mobile'        =>  $sms['mobile'],                                                    //接收手机号
            'content'       =>  "【外语通】您好，您的验证码是{$sms['codes']}，".date('I',$expire)."分钟内有效，请尽快验证。",  //验证信息内容
            'smsType'       =>  '9',                //短信类型(8:通知、9:验证码、11:营销)
            'userData'      =>  $time,              //流水号(最长64字节)
            'url'           =>  $sms['uri'],        //请求URL
            'code'          =>  $sms['codes'],              //验证码
            'expire'        =>  $expire                // 验证码过期时间（s）5分钟
        ];
        $phone = new \PhoneCode($config);
        $key = $phone->__get('key');
        $commCheckObj = new CommCheck();
        $data = $commCheckObj -> where('key', $key)->find();
        //判断验证码是否过期或者不正确
        if ($time>$data['verify_time']) {
            //失败
            $json = [
                'code'  =>400,
                'msg'   => config('msg.400').'！错误：时间已过期',
            ];
        }else if ($sms['codes']!=$data['code']) {
            //失败
            $json = [
                'code'  =>10004,
                'msg'   => config('msg.10004'),
            ];
        } else {
            $json = [
                'code'  =>200,
                'msg'   => config('msg.200'),
                'data'  =>[
                    'verify_code'   => $sms['codes'],
                    'mobile'        => $sms['mobile'],
                    //'key'          =>  $key
                ]
            ];
        }
        return $json;
    }
}