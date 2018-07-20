<?php
/**
 * cookie中的phpsession 保存与取出
 * User: zhouzeken(1454740914@qq.com)
 * Date: 2018/2/24
 */
namespace app\common\api\opening;

interface IPhpsession{

    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []);

    /**
     * 保存session
     * @return mixed
     */
    public function phpsession_set();

    /**
     * 获取session
     * @return mixed
     */
    public function phpsession_get();

    /**
     * 删除某个session
     * @return mixed
     */
    public function phpsession_delete();

    /**
     * 清空所有session
     * @return mixed
     */
    public function phpsession_clear();

}