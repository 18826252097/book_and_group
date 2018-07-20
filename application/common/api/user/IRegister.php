<?php
/**
 * 用户注册接口类
 * User: zhouzeken(1454740914@qq.com)
 * Date: 2018/3/1
 * Time: 15:45
 */
namespace app\common\api\user;


interface IRegister{
    /**
     * 初始化配置
     * @param $config
     */

    public function __construct(array $config = []);

    /**
     * 注册
     * @return mixed
     */
    public function register();
}

