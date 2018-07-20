<?php
/**
 * 获取用户信息类
 * User: zhouzeken(1454740914@qq.com)
 * Date: 2018/3/1
 * Time: 15:48
 */
namespace app\common\api\user;

interface IGet{
    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []);

    /**
     * 获取用户详细信息
     * @return mixed
     */
    public function get_info();

    /**
     * 获取用户列表
     * @return mixed
     */
    public function get_list();

}
