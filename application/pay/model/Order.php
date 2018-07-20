<?php
/**
 * Created by crp.
 * User: Administrator
 * Date: 2018/5/22
 * Time: 14:32
 */
namespace app\pay\Model;
use think\Model;

class Order extends Model{

    private $param_arr = ['out_trade_no','out_pay_no','price','real_pay'];

	/**
     * 保存订单数据
     * @param  $orderInfo  订单信息
     * @return bool
     */
	# TODO 根据数据库实际修改
    public function _addOrder($orderInfo,$pay_type = 1)
	{
		$info = [
		    'pay_type' => $pay_type
        ];

        foreach ($orderInfo as $key => $item) {
            if (in_array($key,$this->param_arr)){
                $info[$key] = $item;
            }
		}

        return db('order')->insertGetId($info);
	}

    /**
     * 支付成功后操作
     * @param $out_trade_no 订单号
     * @return array
     */
    # TODO 补完支付成功后逻辑操作
    public function after_payment($out_trade_no)
    {
        /**
         * 支付成功后逻辑操作
         */
        return ['code'=>200,'msg'=>config('msg.200'),'data'=>''];
	}
}