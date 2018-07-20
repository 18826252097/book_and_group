<?php
/**
 * 支付类
 * User: lizhengyu
 * Date: 2018/1/23
 * Time: 16:00
 */
namespace app\common\controller;

class Pay{
	
	//订单信息
	private $orInfo;
	
	public function __construct($orInfo = []){
		$this->orInfo = $orInfo;
	}
	
	/**
     * 支付宝扫码调用支付
	 * @return mixed
    */
	public function alipay()
	{
		if (!empty($this->orInfo['WIDout_trade_no'])&& trim($this->orInfo['WIDout_trade_no'])!=""){
			
			vendor('alipay.pagepay.service.AlipayTradeService');
			vendor('alipay.pagepay.model.builder.AlipayTradeWapPayContentBuilder');
			//商户订单号，商户网站订单系统中唯一订单号，必填
			$out_trade_no = $this->orInfo['WIDout_trade_no'];

			//订单名称，必填
			$subject = $this->orInfo['WIDsubject'];

			//付款金额，必填
			$total_amount = $this->orInfo['WIDtotal_amount'];

			//商品描述，可空
			$body = $this->orInfo['WIDbody'];

			//超时时间
			$timeout_express="1m";

			$payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
			$payRequestBuilder->setBody($body);
			$payRequestBuilder->setSubject($subject);
			$payRequestBuilder->setOutTradeNo($out_trade_no);
			$payRequestBuilder->setTotalAmount($total_amount);
			$payRequestBuilder->setTimeExpress($timeout_express);

			$payResponse = new \AlipayTradeService($config);
			$result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);

			return ;
		}
	}
	
	
	
	/**
     * 支付宝网页调用支付
	 * @return mixed
    */
	public function qrpay(){
		vendor('alipay.pagepay.service.AlipayTradeService');
		vendor('alipay.pagepay.model.builder.AlipayTradePrecreateContentBuilder');
		
		//商户订单号，商户网站订单系统中唯一订单号，必填
		$out_trade_no = trim($this->orInfo['WIDout_trade_no']);

		//订单名称，必填
		$subject = trim($this->orInfo['WIDsubject']);

		//付款金额，必填
		$total_amount = trim($this->orInfo['WIDtotal_amount']);

		//商品描述，可空
		$body = trim($this->orInfo['WIDbody']);

		//构造参数
		$payRequestBuilder = new \AlipayTradePagePayContentBuilder();
		$payRequestBuilder->setBody($body);
		$payRequestBuilder->setSubject($subject);
		$payRequestBuilder->setTotalAmount($total_amount);
		$payRequestBuilder->setOutTradeNo($out_trade_no);

		$aop = new \AlipayTradeService($config);

		/**
		 * pagePay 电脑网站支付请求
		 * @param $builder 业务参数，使用buildmodel中的对象生成。
		 * @param $return_url 同步跳转地址，公网可以访问
		 * @param $notify_url 异步通知地址，公网可以访问
		 * @return $response 支付宝返回的信息
		*/
		$response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

		//输出表单
		return $response;
			
	}
	
	
	/**
     * 微信扫码支付
	 * @param   $orInfo 订单信息
	 * @return mixed
    */
	public function wxqrpay()
	{
		vendor('WxpayAPI_php_v3_0_1.lib.WxPay.Api');
		vendor('WxpayAPI_php_v3_0_1.example.WxPay.NativePay');
		vendor('WxpayAPI_php_v3_0_1.example.log');
		
		$input = new \WxPayUnifiedOrder();
		$input->SetBody("test");
		$input->SetAttach("test");
		$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
		$input->SetTotal_fee("1");
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag("test");
		$input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
		$input->SetTrade_type("NATIVE");
		$input->SetProduct_id("123456789");
		$result = $notify->GetPayUrl($input);
		$url2 = $result["code_url"];
		
		return $url2;
	}
	
	
	/**
     * 微信公众号支付
	 * @return mixed
    */
	public function wx_jsapi_pay()
	{
		vendor('WxpayAPI_php_v3_0_1.lib.WxPay.Api');
		vendor('WxpayAPI_php_v3_0_1.example.WxPay.NativePay');
		vendor('WxpayAPI_php_v3_0_1.example.log');
		
		//初始化日志
		$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
		$log = Log::Init($logHandler, 15);

		//打印输出数组信息
		function printf_info($data)
		{
			foreach($data as $key=>$value){
				echo "<font color='#00ff55;'>$key</font> : $value <br/>";
			}
		}

		//①、获取用户openid
		$tools = new JsApiPay();
		$openId = $tools->GetOpenid();

		//②、统一下单
		$input = new WxPayUnifiedOrder();
		$input->SetBody("test");
		$input->SetAttach("test");
		$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
		$input->SetTotal_fee("1");
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag("test");
		$input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$order = WxPayApi::unifiedOrder($input);
		echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
		printf_info($order);
		$data['jsApiParameters'] = $tools->GetJsApiParameters($order);

		//获取共享收货地址js函数参数
		$data['editAddress'] = $tools->GetEditAddressParameters();
		
		return $data;
	}
}