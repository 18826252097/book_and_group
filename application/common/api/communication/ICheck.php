<?php
/**
 * 通信-验证-接口类
 * User: linweixin(502510773@qq.com)
 * Date: 2018/2/9
 * Time: 11:06
 */
namespace app\common\api\communication;

interface ICheck{
    /**
     * 初始化配置
     * @param array $config
     */
    public function __construct(array $config = []);

    /**
     * 发送
     * @return mixed
     */
    public function check();

}