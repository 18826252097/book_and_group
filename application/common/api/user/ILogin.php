<?php
/**
 * 登录接口
 * User: zhouzeken(1454740914@qq.com)
 * Date: 2018/3/1
 * Time: 15:43
 */
namespace app\common\api\user;


interface ILogin{

    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []);

    /**
     * 登录
     * @return mixed
     */
    public function login();
}