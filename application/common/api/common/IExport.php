<?php
/**
 * 批量导入导出接口类
 * User: lixiaoming(251603964@qq.com)
 * Date: 2018/1/27
 * Time: 14:58
 */
namespace app\common\api\common;


interface IExport{
    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []);

    /**
     * 批量导入
     * @return mixed
     */
    public function import();

    /**
     * 批量导出
     * @return mixed
     */
    public function export();
}