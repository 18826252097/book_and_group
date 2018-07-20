<?php
/**
 * 通信-基站类
 * User: linweixin(502510773@qq.com)
 * Date: 2018/2/5
 * Time: 10:50
 */
namespace app\communication\controller;
use app\common\api\AApi;
use app\common\api\communication\ICheck;
use app\common\api\communication\ISend;
use app\communication\controller\check;
use app\communication\controller\send;

class Api extends AApi implements ICheck,ISend {
    // 配置参数
    protected static $config = [
        'to'          => '',      //发送地址
        'name'        => '',      //发送名称
        'subject'     => '',      //邮箱主题
        'body'        => '',      //邮寄主要内容
        'attachment'  => null,    //附件
        'type'        => '',      //发送类型
        'mobile'      => null,    //短信发送的号码
        'len'         => 0,       //字符串长度
        'code'        => 0,       //验证码
        'codes'       => 0,       //被验证码
    ];
    // 静态对象
    static public $obj;
    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []){
        parent::__construct();
        $data = self::$post_data;
        self::$config = array_merge(self::$config, array_change_key_case($config),$data);
    }

    /**
     * 发送方法
     * @return array|string
     */
    public function send(){
        //实例化对象
        switch(self::$config['type']){
            case 'phone':
                self::$obj = new send\Phone(self::$config);
                break;
            case 'email':
            default:
                self::$obj = new send\Email(self::$config);
                break;
        }
        return create_callback(self::$obj->send());
    }

    /**
     * 验证方法
     * @return array|string
     */
    public function check(){
        //实例化对象
        switch(self::$config['type']){
            case 'phone':
                self::$obj = new check\Phone(self::$config);
                break;
            case 'email':
            default:
                self::$obj = new check\Email(self::$config);
                break;
        }
        return create_callback(self::$obj->check());
    }

    /**
     * 自定义发送的内容的
     * @return array|string
     */
    public function customSend(){
        //实例化对象
        switch(self::$config['type']){
            case 'phone':
                self::$obj = new send\Phone(self::$config);
                break;
            default:
                return create_callback([
                    'code'  =>110,
                    'msg'   => config('msg.110')]);
                break;
        }
        return create_callback(self::$obj->customSend());
    }
}