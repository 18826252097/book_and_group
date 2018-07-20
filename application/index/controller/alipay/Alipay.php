<?php
/**
 * Created by crp.
 * User: Administrator
 * Date: 2018/5/21
 * Time: 16:30
 */

namespace app\index\controller\alipay;
use think\Model;
use think\Request;

class Alipay extends Model
{
    /**
     * 支付宝下单demo
     */
    public function index()
    {
        $data = array(
            'type'=>'alipay',
            'alipay_config' => [
                'trade_type' => 'SCAN',#‘SCAN’：扫码支付 ‘APP’：APP支付  ‘PC’：PC网页端支付
                'gatewayUrl' => 'https://openapi.alipay.com/gateway.do',
                'app_id' => '201607112345355',
            ],
            'goods_config' => [
                'out_trade_no' => time().getCode(6),//订单号
                'real_pay' => 5,//支付价格 单位分  默认1  不为零
                'remark' => '测试接口2',//货物描述
                'attach' => '外语通支付接口',//备注
                'good_tags' => 'iexue',//商品标记
            ],
        );
        $res = getapi($data,'/pay/Api/order');//调用邮箱注册接口
        $res = decodeData($res);//解析返回参数
        if (isset($res['data']['data']['pay_url'])){
            $res['data']['data']['pay_url'] = 'http://127.0.0.1:1314'.url('set_qrcode','','').'/url_code/'.base64_encode($res['data']['data']['pay_url']);
        }
        echo '<pre>'.print_r($res,true).'</pre>';
    }

    /**
     * PHP url生成二维码 crp
     */
    public function set_qrcode(Request $request)
    {
        vendor('Phpqrcode.phpqrcode');
        $url_code = $request->param('url_code','');
        $url = base64_decode($url_code);
        ob_end_clean();
        \QRcode::png($url);
    }

    /**
     * 支付宝查询demo
     */
    public function query_order()
    {
        $data = [
            'type' => 'alipay',
            'out_trade_no' => '1526969562RUmldo',
        ];
        $res = getapi($data,'/pay/Api/query');//调用邮箱注册接口
        $res = decodeData($res);//解析返回参数
        echo '<pre>'.print_r($res,true).'</pre>';
    }

    /**
     * 支付宝退款demo
     */
    public function refund_order()
    {
        $data = [
            'type' => 'alipay',
            'out_trade_no' => '1527056415fyOvXA',
            //'refund_amount' => 7,
            //'out_request_no' => time(),
        ];
        //echo $data['out_request_no'].'<br>';
        $res = getapi($data,'/pay/Api/refund');//调用邮箱注册接口
        $res = decodeData($res);//解析返回参数
        echo '<pre>'.print_r($res,true).'</pre>';
    }

    /**
     * 支付宝退款查询 demo
     */
    public function query_refund()
    {
        $data = [
            'type' => 'alipay',
            'out_trade_no' => '1526980682hYSKdA',
            'out_request_no' => '1526980682hYSKdA',
        ];
        $res = getapi($data,'/pay/Api/refund_query');//调用邮箱注册接口
        $res = decodeData($res);//解析返回参数
        echo '<pre>'.print_r($res,true).'</pre>';
    }

    /**
     * 支付宝关闭订单demo
     */
    public function close_order()
    {
        $data = [
            'type' => 'alipay',
            'out_trade_no' => '15270394933mgjoa',
        ];
        $res = getapi($data,'/pay/Api/close');//调用邮箱注册接口
        $res = decodeData($res);//解析返回参数
        echo '<pre>'.print_r($res,true).'</pre>';
    }
}