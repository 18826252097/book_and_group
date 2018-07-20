<?php
/**
 * 邮件发送
 * User: linweixin(502510773@qq.com)
 * Date: 2018/2/3
 * Time: 10:54
 */
namespace app\communication\controller\check;
use app\common\api\communication\ICheck;
use think\Config;

class Email implements ICheck{
    // 配置参数
    protected static $config = [
        'to'          => '',     //发送地址
        'name'        => '',     //发送名称
        'subject'     => '',      //邮箱主题
        'body'        => '',      //邮寄主要内容
        'attachment'  => null,    //附件
    ];
    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []){
        $default_config = Config::get('communication.mail');
        self::$config = array_merge(self::$config, $default_config,$config);
    }
    /**
     * 发送
     * @return mixed
     */
    public function check(){
        $result = '暂无邮箱验证';
        return $result;
    }
}