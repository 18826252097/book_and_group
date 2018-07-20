<?php
/**
 * 支付接口
 * User: lixiaoming(251603964@qq.com)
 * Date: 2018/2/8
 * Time: 10:50
 */
namespace app\pay\controller;
use app\common\api\AApi;
use app\common\api\pay\IPay;
use think\exception\ErrorException;

class Api extends AApi implements IPay{

    function __construct(array $config = []){
        parent::__construct();
    }

    /**
     * 支付
     * @return mixed
     */
    public function order(){
        try{
            $data = self::$post_data;
            $type = isset($data['type'])?$data['type']:'';//支付类型 alipay or wxpay

            switch($type){
                case 'wxpay'://微信
                    $o_order = new order\Wxpay($data);
                    break;
                case 'alipay'://支付宝
                    $o_order = new order\Alipay($data);
                    break;
                default://支付宝
                    $o_order = new order\Alipay($data);
            }

            $json = $o_order->order();
            return create_callback($json);
        }catch (ErrorException $e){
            return create_callback(['code' => 201,'msg' => config('msg.201'),'data' => '']);
        }
    }

    /**
     * 回调处理 checksign and decode
     * @return mixed
     */
    public function notify(){
        $data = $this->post_data;

        //支付类型 alipay or wxpay
        $type = isset($data['type'])?$data['type']:'';

        //检测调用类型
        switch($type){
            case 'wxpay':
                //微信
                $o_notify = new notify\Wxpay($data);
                break;
            case 'alipay':
                //支付宝
                $o_notify = new notify\Alipay($data);
                break;
            default:
                //支付宝
                $o_notify = new notify\Alipay($data);
        }
        //调用方法
        $json = $o_notify->notify();
        return create_callback($json);
    }

    /**
     * 查询
     * @return mixed
     */
    public function query(){
        try{
            $data = self::$post_data;
            $type = isset($data['type'])?$data['type']:'';//支付类型 alipay or wxpay

            switch($type){
                case 'wxpay'://微信
                    $o_query = new query\Wxpay($data);
                    break;
                case 'alipay'://支付宝
                    $o_query = new query\Alipay($data);
                    break;
                default://默认支付宝
                    $o_query = new query\Alipay($data);
            }

            $json = $o_query->query();
            return create_callback($json);
        }catch (ErrorException $e){
            return create_callback(['code' => 201,'msg' => config('msg.201'),'data' => '']);
        }
    }

    /**
     * 退款
     * @return mixed
     */
    public function refund(){
        //try{
            $data = self::$post_data;
            $type = isset($data['type'])?$data['type']:'';//支付类型 alipay or wxpay

            switch($type){
                case 'wxpay'://微信
                    $o_refund = new refund\Wxpay($data);
                    break;
                case 'alipay'://支付宝
                    $o_refund = new refund\Alipay($data);
                    break;
                default://默认支付宝
                    $o_refund = new refund\Alipay($data);
            }

            $json = $o_refund->refund();
            return create_callback($json);
        /*}catch (ErrorException $e){
            return create_callback(['code' => 201,'msg' => config('msg.201'),'data' => '']);
        }*/
    }

    /**
     * 退款查询
     * @return mixed
     */
    public function refund_query(){
        try{
            $data = self::$post_data;
            $type = isset($data['type'])?$data['type']:'';//支付类型 alipay or wxpay

            switch($type){
                case 'wxpay'://微信
                    $o_refund_query = new refund_query\Wxpay($data);
                    break;
                case 'alipay'://支付宝
                    $o_refund_query = new refund_query\Alipay($data);
                    break;
                default://默认支付宝
                    $o_refund_query = new refund_query\Alipay($data);
            }

            $json = $o_refund_query->refund_query();
            return create_callback($json);
        }catch (ErrorException $e){
            return create_callback(['code' => 201,'msg' => config('msg.201'),'data' => '']);
        }
    }

    /**
     * 关闭
     * @return mixed
     */
    public function close(){
        try{
            $data = self::$post_data;
            $type = isset($data['type'])?$data['type']:'';//支付类型 alipay or wxpay

            switch($type){
                case 'wxpay'://微信
                    $o_close = new close\Wxpay($data);
                    break;
                case 'alipay'://支付宝
                    $o_close = new close\Alipay($data);
                    break;
                default://默认支付宝
                    $o_close = new close\Alipay($data);
            }

            $json = $o_close->close();
            return create_callback($json);
        }catch (ErrorException $e){
            return create_callback(['code' => 201,'msg' => config('msg.201'),'data' => '']);
        }
    }
}

