<?php
/**
 * 角色表接口
 * User: zhouzeken(1454740914@qq.com)
 * Date: 2018/3/23
 * Time: 15:48
 */
namespace app\common\api\auth;
interface IGroup
{
    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []);

    /**
     * 获取列表
     * @return mixed
     */
    public function index();

    /**
     * 获取单个
     * @return mixed
     */
    public function info();

    /**
     * 增加
     * @return mixed
     */
    public function add();

    /**
     * 修改
     * @return mixed
     */
    public function edit();

    /**
     * 删除
     * @return mixed
     */
    public function delete();

}