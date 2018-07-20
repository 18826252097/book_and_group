<?php
/**
 * 订单查询类
 * User: lizhengyu
 * Date: 2018/1/23
 * Time: 16:00
 */
namespace app\common\controller;

class OrderQuery{
	
	/**
     * 微信订单查询
	 * @param   $infor 查询条件
	 * @return mixed
    */
	public function wx_order_query($infor)
	{
		
		vendor('WxpayAPI_php_v3_0_1.lib.WxPay.Api');
		vendor('WxpayAPI_php_v3_0_1.example.log');
		
		//初始化日志
		$logHandler= new \CLogFileHandler("./logs/".date('Y-m-d').'.log');
		$log = Log::Init($logHandler, 15);

		function printf_info($data)
		{
			foreach($data as $key=>$value){
				echo "<font color='#f00;'>$key</font> : $value <br/>";
			}
		}

		if(isset($infor["transaction_id"]) && $infor["transaction_id"] != ""){
			$transaction_id = $infor["transaction_id"];
			$input = new WxPayOrderQuery();
			$input->SetTransaction_id($transaction_id);
			return WxPayApi::orderQuery($input);
		}

		if(isset($infor["out_trade_no"]) && $infor["out_trade_no"] != ""){
			$out_trade_no = $infor["out_trade_no"];
			$input = new WxPayOrderQuery();
			$input->SetOut_trade_no($out_trade_no);
			return WxPayApi::orderQuery($input);
		}
	}
	
	
	/**
     * 支付宝订单查询
	 * @param   $infor 查询条件
	 * @return mixed
    */
	public function ali_order_query($infor)
	{
		vendor('alipay.lotusphp_runtime.config');
		vendor('alipay.pagepay.service.AlipayTradeService');
		vendor('alipay.pagepay.buildermodel.AlipayTradeQueryContentBuilder');

		//商户订单号，商户网站订单系统中唯一订单号
		$out_trade_no = trim($infor['WIDTQout_trade_no']);

		//支付宝交易号
		$trade_no = trim($infor['WIDTQtrade_no']);
		//请二选一设置
		//构造参数
		$RequestBuilder = new \AlipayTradeQueryContentBuilder();
		$RequestBuilder->setOutTradeNo($out_trade_no);
		$RequestBuilder->setTradeNo($trade_no);

		$aop = new \AlipayTradeService($config);
			
		/**
		 * alipay.trade.query (统一收单线下交易查询)
		 * @param $builder 业务参数，使用buildmodel中的对象生成。
		 * @return $response 支付宝返回的信息
		 */
		$response = $aop->Query($RequestBuilder);
			
		return $response;
	
	}
	
	
	
}

?>