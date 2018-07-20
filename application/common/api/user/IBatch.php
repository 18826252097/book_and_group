<?php
/**
 * 批量公共接口类
 * User: zhouzeken(1454740914@qq.com)
 * Date: 2018/3/1
 * Time: 15:48
 */
namespace app\common\api\user;

interface IBatch{
    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []);

    /**
     * 批量导入
     * @param array $data
     * @return mixed
     */
    public function import();

    /**
     * 批量导出
     * @return mixed
     */
    public function export();
}
