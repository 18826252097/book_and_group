<?php
/**
 * 支付接口类
 * User: lixiaoming(251603964@qq.com)
 * Date: 2018/1/29
 * Time: 9:50
 */
namespace app\common\api\common;

interface IPay{

    /**
     * 初始化配置
     * @param array $config
     */
    public function __construct(array $config = []);

    /**
     * 下单
     * @return mixed
     */
    public function create_order();

    /**
     * 补单
     * @return mixed
     */
    public function supplement_order();

    /**
     * 查订单
     * @return mixed
     */
    public function search_order();

    /**
     * 支付回调
     * @return mixed
     */
    public function notify();
}