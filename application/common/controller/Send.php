<?php
/**
 * 发送出口
 * User: linweixin(502510773@qq.com)
 * Date: 2018/2/3
 * Time: 11:19
 */
namespace app\common\controller;
use app\common\api\common\ISend;

class Send implements ISend {
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
    ];
    // 静态对象
    static public $obj;
    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []){
        self::$config = array_merge(self::$config, array_change_key_case($config));
        //实例化对象
        self::getInstance();
    }
    /**
     * 设置参数
     * @param $name
     * @param $value
     * @return mixed
     */
    public function setParam($name='',$value=null){
        self::$config[$name] = $value;
        self::getInstance();
        return 1;
    }
    /**
     * 实例化对象
     * @return send\Email|send\Phone|send\Email
     */
    static function getInstance()
    {
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
        return self::$obj;
    }
    /**
     * 调用发送
     * @return mixed
     */
    public function send(){
        $obj = self::$obj;
        $result = $obj ->send();
        return $result;
    }
    /**
     * 验证
     * @param $code
     * @return mixed
     */
    public function check($code){
        $obj = self::$obj;
        $result = $obj ->check($code);
        return $result;
    }
}