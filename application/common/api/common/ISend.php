<?php
/**
 * 发送接口类
 * User: linweixin(502510773@qq.com)
 * Date: 2018/2/3
 * Time: 11:06
 */
namespace app\common\api\common;

interface ISend{
    /**
     * 初始化配置
     * @param array $config
     */
    public function __construct(array $config = []);

    /**
     * 发送
     * @return mixed
     */
    public function send();
    /**
     * 验证
     * @return mixed
     */
    public function check($code);

}