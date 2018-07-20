<?php
/**
 * 获取地区信息
 * User: zhouzeken(1454740914@qq.com)
 * Date: 2018/3/7
 * Time: 15:48
 */
namespace app\common\api\area;

interface IGet{
    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []);

    /**
     * 根据id获取详细信息
     * @return mixed
     */
    public function get_info();

    /**
     * 根据父级id获取地区子列表
     * @return mixed
     */
    public function get_list();
}
