<?php
/**
 * 支付异步回调类
 * User: lizhengyu
 * Date: 2018/1/23
 * Time: 16:00
 */
namespace app\common\controller;

use app\common\model\OrderModel;


class Notify{
	
	/**
     * 支付宝支付回调地址
	 * @return mixed
    */
	public function alipay_notify()
	{
		vendor('alipay.lotusphp_runtime.config');
		vendor('alipay.pagepay.service.AlipayTradeService');

		$arr=$_POST;
		$alipaySevice = new \AlipayTradeService($config); 
		$alipaySevice->writeLog(var_export($_POST,true));
		$result = $alipaySevice->check($arr);

		/* 实际验证过程建议商户添加以下校验。
		1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
		2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
		3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
		4、验证app_id是否为该商户本身。
		*/
		if($result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代

			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			
			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			
			//商户订单号

			$out_trade_no = $_POST['out_trade_no'];

			//支付宝交易号

			$trade_no = $_POST['trade_no'];

			//交易状态
			$trade_status = $_POST['trade_status'];


			if($_POST['trade_status'] == 'TRADE_FINISHED') {

				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//请务必判断请求时的total_amount与通知时获取的total_fee为一致的
					//如果有做过处理，不执行商户的业务程序
						
				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
			}
			else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//请务必判断请求时的total_amount与通知时获取的total_fee为一致的
					//如果有做过处理，不执行商户的业务程序			
				//注意：
				//付款完成后，支付宝系统发送该交易状态通知
				
				
				//修改订单支付信息
				$where['out_trade_no'] = $_POST['out_trade_no'];
				
				$orderUpInfo['trade_no'] = $_POST['trade_no'];
				$orderUpInfo['update_titme'] = $_POST['update_time'];
				$orderUpInfo['status'] = '2';
				
				$orModel = new OrderModel;
				$orModel->_updateOrder($where,$orderUpInfo);
				
			}
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
				
			echo "success";		//请不要修改或删除
				
		}else {
			//验证失败
			echo "fail";	//请不要修改或删除

		}
		
	}
	
	
	
	/**
     * 微信支付回调地址
	 * @return mixed
    */
	public function weipay_notify()
	{  
        libxml_disable_entity_loader(true);  
  
        $postStr = postdata();//接收post数据  
  
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);  
        $arr = object2array($postObj);//对象转成数组  
        ksort($arr);// 对数据进行排序  
  
        $str = ToUrlParams($arr);//对数据拼接成字符串  
        $user_sign = strtoupper(md5($str));  
  
        if($user_sign == $arr['sign']){//验证成功  
  
            //修改订单支付信息
			$where['out_trade_no'] = $_POST['out_trade_no'];
				
			$orderUpInfo['trade_no'] = $_POST['trade_no'];
			$orderUpInfo['update_titme'] = $_POST['update_time'];
			$orderUpInfo['status'] = '2';
				
			$orModel = new OrderModel;
			$orModel->_updateOrder($where,$orderUpInfo);
        }  
  
  
  
  
    }  
      
    /*  
    *  接收post数据   微信是用$GLOBALS['HTTP_RAW_POST_DATA'];这个函数接收post数据的  
    */  
    function postdata(){  
        $receipt = $_REQUEST;  
        if($receipt==null){  
            $receipt = file_get_contents("php://input");  
            if($receipt == null){  
                $receipt = $GLOBALS['HTTP_RAW_POST_DATA'];  
            }  
        }  
        return $receipt;  
    }  
      
    //把对象转成数组  
    function object2array($array) {  
		if(is_object($array)) {  
			$array = (array)$array;  
		} if(is_array($array)) {  
			foreach($array as $key=>$value) {  
				$array[$key] = object2array($value);  
			}  
		}  
		return $array;  
    }  
      
      
     /**  
     * 格式化参数格式化成url参数  
     */  
    private function ToUrlParams($arr)  
    {  
        $weipay_key = 'sdfasdfasdfasd';//微信的key,这个是微信支付给你的key，不要瞎填。  
        $buff = "";  
        foreach ($arr as $k => $v)  
        {  
            if($k != "sign" && $v != "" && !is_array($v)){  
                $buff .= $k . "=" . $v . "&";  
            }  
        }  
  
        $buff = trim($buff, "&");  
        return $buff.'&key='.$weipay_key;  
    }  
	
}

?>