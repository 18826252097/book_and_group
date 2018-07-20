<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 9:39
 */
namespace app\common\api\pay;

interface IPay
{
    /**
     * 构造函数初始化
     * IPay constructor.
     * @param array $config
     */
    public function __construct(array $config = []);

    /**
     * 生成订单
     * @return mixed
     */
    public function order();

    /**
     * 回调处理
     * @return mixed
     */
    public function notify();

    /**
     * 查询订单状态
     * @return mixed
     */
    public function query();

    /**
     * 订单退款
     * @return mixed
     */
    public function refund();

    /**
     * 退款查询
     * @return mixed
     */
    public function refund_query();

    /**
     * 关闭订单
     * @return mixed
     */
    public function close();
}