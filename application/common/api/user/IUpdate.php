<?php
/**
 * 用户更新接口类
 * User: zhouzeken(1454740914@qq.com)
 * Date: 2018/3/1
 * Time: 15:47
 */
namespace app\common\api\user;


interface IUpdate{

    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []);

    /**
     * 修改密码
     * @param array $data
     * @return mixed
     */
    public function update_pwd();

    /**
     * 找回密码
     * @param array $data
     * @return mixed
     */
    public function back_pwd();


    /**
     * 修改基本信息
     * @param array $data
     * @return mixed
     */
    public function update_info();

    /**
     * 删除1个或多个用户
     * @return mixed
     */
    public function delete();

}