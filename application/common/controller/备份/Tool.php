<?php
/**
 * 公共工具类
 * User: lixiaoming
 * Date: 2018/1/22
 * Time: 14:44
 */

namespace app\common\controller;

use app\common\controller\Pay;
use app\common\model\OrderModel;
use app\common\model\OrderQuery;


class Tool implements ITool{
    /**
     * 文件上传  - Author: Dejan  2018.01.23
     * @param  String  $frm_name      文件表单name(name="file")
     * @param  Mixed   $ext[可选]      文件类型限制(扩展名),数组或','链接字符串
     * @param  Bool    $qupload[可选]  true同步上传七牛  ,默认false
     * @param  Bool    $clear[可选]    $qupload为true时可用,该参数为true时清除本地临时上传文件 ,默认true
     * @return Array
     */
    public function _upload($frm_name,$ext=NULL,$qupload=NULL,$clear=NULL){
        # - 函数使用说明
        # $this->_upload('file','doc,txt,mp3'); // 上传到服务器本地, 只允许上传doc,txt,mp3类型文件
        # $this->_upload('file','doc,txt,mp3',true); // 上传到七牛云服务器本地不存储该文件, 只允许上传doc,txt,mp3类型文件
        # $this->_upload('file','doc,txt,mp3',true,false); // 上传到服务器本地并同步上传到七牛云, 只允许上传doc,txt,mp3类型文件
        # $this->_upload('file'); // 上传到服务器本地, 允许上传类型文件读cocfig('upload')配置
        # $this->_upload('file',true); // 上传到七牛云服务器本地不存储该文件, 允许上传类型文件读cocfig('upload')配置
        # $this->_upload('file',true,false); // 上传到服务器本地并同步上传到七牛云, 允许上传类型文件读cocfig('upload')配置
        
        # 参数类型识别交换变量
        switch(gettype($ext)){
            case 'NULL':{ # $obj->_upload('file');
                $qupload = false; # 初始化默认值
                $clear   = true;  # 初始化默认值
                break;
            }
            case 'boolean':{ # $obj->_upload('file',true);
                $clear   = $qupload === NULL?true:$qupload;
                $qupload = $ext;
                $ext     = NULL;
                break;
            }
            case 'string':{ # $obj->_upload('file','doc,txt,mp3');
                $qupload = $qupload === NULL?false:$qupload;
                $clear   = $clear === NULL?true:$clear;
                break;
            }
            case 'array':{ # $obj->_upload('file',['doc','txt','mp3']);
                $qupload = $qupload === NULL?false:$qupload;
                $clear   = $clear === NULL?true:$clear;
                break;
            }
            default:{
                throw new \RuntimeException('传递非法参数! _upload($frm_name,$ext=[x错误], ...)');
                break;
            }
        }
        
        if(!$qupload){
            # 上传到本地
            return model('Upload')->ext($ext)->upload($frm_name);
        }else{
            $func = model('Upload');
            
            # 先传到本地 , 在用PHP同步存到七牛云上这方法是非常笨愚的。因为这方法会导致服务器上行宽带不够用且浪费资源的一种。这里个人提供了一种更好的方式就是"直接用javascript在客户端传七牛",这样有效降低服务器资源消耗成本!
            $filelist = $func->ext($ext)->upload($frm_name);
            
            # PHP同步上传到七牛,上传七牛完成后默认会清除清除本地当前临时上传的文件,如果仅是为了同步七牛把$clear赋值为false即可
            return $func->qupload($filelist,$clear);
        }
        
    }

    /**
     * 删除文件  - Author: Dejan  2018.01.24
     * @param  String  $file  文件路径 或 URL
     * @param  int     $type  类型，1本地，2七牛
     * @return Bool
     */
    public function _delFile($file,$type=1){
        return model('Upload')->delFile($file,$type);
    }


    /**
     * 批量导入
     * @param null $file_path  文件地址
     * @return array
     */
    public function _import($file_path=null){
        $data =[];
        if($file_path == null || !file_exists($file_path)){
            //缺少参数
            $data['error'] = config('msg.10011');
        }else{
            vendor('PHPExcel.PHPExcel');   //引入第三方类库
            $suffix = substr(strrchr($file_path, '.'), 1);
            switch($suffix){
                case 'xlsx':
                    //Excel2007
                    $PHPReader = new \PHPExcel_Reader_Excel2007();
                    $data['list'] = excel_import($PHPReader,$file_path);
                    break;
                case 'csv':
                    $csv = new \CsvReader($file_path);
                    $data['list'] = $csv -> get_data();
                    break;
                default:
                    //Excel2003
                    $PHPReader = new \PHPExcel_Reader_Excel5();
                    $data['list'] = excel_import($PHPReader,$file_path);
            }
        }
        return $data;
    }


    /**
     * 批量导出
     * @param array $tableheader 填充表头信息
     * @param array $data 导入数据
     * @param null $tablename 文件名称
     * @return mixed
     */
    public function _excelExport($tableheader=array(),$data=array(),$tablename=null){
        ini_set('max_execution_time', '0');
        ini_set('memory_limit','-1');

        //引入第三方类库
        vendor('PHPExcel.PHPExcel');
        $excel = new \PHPExcel();
        $letter = array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S',
            'T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ',
            'AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
        );
        for($i = 0;$i < count($tableheader);$i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
        }

        //填充表格信息
        for ($i = 2;$i <= count($data) + 1;$i++) {
            $j = 0;
            foreach ($data[$i - 2] as $key=>$value) {
                $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
                $j++;
            }
        }

        if($tablename==null){
            $tablename=date('YmdHis',time());
        }

        //创建Excel输入对象
        $write = new \PHPExcel_Writer_Excel5($excel);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="'.$tablename.'.xls"');
        header("Content-Transfer-Encoding:binary");
        ob_clean();
        $write->save('php://output');
        exit;
    }

     /**
     * 导出excel(csv)
     * @param array $headlist 第一行,列名
     * @param array $data 导出数据
     * @param null $fileName 输出Excel文件名
     */
    public function _csvExport($headlist = array(), $data = array(), $fileName=null) {
        ini_set('max_execution_time', '0');
        ini_set('memory_limit','-1');
        $file_name=$fileName.date("YmdHis",time()).".csv";
        header ( 'Content-Type: application/vnd.ms-excel' );
        header ( 'Content-Disposition: attachment;filename='.$file_name );
        header ( 'Cache-Control: max-age=0' );
        ob_clean();
        $fp = fopen('php://output',"a");
        $limit=1000;
        $num = 0;
        foreach ($headlist as $key => $value) {
            $headlist[$key] = iconv('utf-8', 'gbk', $value);
        }
        fputcsv($fp, $headlist);
        foreach ($data as $v){
            $num++;
            if($limit==$num){
                ob_flush();
                flush();
                $num=0;
            }
            foreach ($v as $t){
                $tarr[]=iconv('UTF-8', 'gbk',$t);
            }
            fputcsv($fp,$tarr);
            unset($tarr);
        }
        unset($data);
        fclose($fp);
        exit();
    }

    /**
     * 下订单
	 * @param   $orderInfo 订单信息
     * @return mixed
     */
    public function _order($orderInfo)
	{
        /**
         * out_trade_no 订单号
         * price 原价
         * real_pay 支付价
         * type 支付类型
         */
        //支付类型
        $pay_type = isset($orderInfo['pay_type'])?$orderInfo['pay_type']:1;

        //支付识别码
        $pay_code = '';
        switch($pay_type){
            case 2:
                //微信
                $pay_code = '0002';
                break;
            case 3:
                //其他
                $pay_code = '0003';
                break;
            case 1:
            default:
                //支付宝
                $pay_code = '0001';
        }

        //生成订单号
        $out_trade_no  = date('YmdHis').getCode(14).$pay_code;


		//将订单号添加到订单信息中
		$orderInfo['out_trade_no'] = $out_trade_no; 
		
		//根据支付方式调用支付
		$pay = new Pay();
		if($orderInfo['pay_type'] == 'AliPgPay'){
			//支付宝扫码支付
			$payback = $pay->qrpay($orderInfo);
		}elseif($orderInfo['pay_type'] == 'AliPay'){
			//支付宝网页支付
			$payback = $pay->alipay($orderInfo);
		}elseif($orderInfo['pay_type'] == 'WxPgPay'){
			//微信扫码支付
			$payback = $pay->wxqrpay($orderInfo);
		}elseif($orderInfo['pay_type'] == 'WxJsPay'){
			//微信公众号支付
			$payback = $pay->wx_jsapi_pay($orderInfo);
		}
		
		//订单信息保存数据库
		$orModel = new OrderModel;
		$orModel->_addOrder($orderInfo);
		
		//返回支付信息
		return $payback;
    }
	


    /**
     * 补单
     * @return mixed
     */
    public function _supplementOrder(){
		
		
    }

    /**
     * 查订单
	 * @param   $infor 查询条件
     * @return mixed
     */
    public function _searchOrder($infor)
	{
		//根据支付方式调用查询接口
		$orquery = new OrderQuery();
		if($infor['pay_type'] == '002'){
			//支付宝支付
			$orederInfor = $orquery->ali_order_query($infor);
		}elseif($infor['pay_type'] == '001'){
			//微信支付
			$orederInfor = $orquery->wx_order_query($infor);
		}
		
		return $orederInfor;
    }

    /**
     * 发送短信
     * @param null $mobile
     * @param int $len
     * @return array
     */
    public function _sendCode($mobile=null,$len=4){
        $sms=config('sms');
        $time=time();
        $code=getCode($len,2);
        $config=[
            'apiAccount'    =>  $sms['apiAccount'],                                         //开发者帐号
            'appId'         =>  $sms['appId'],                                              //应用Id
            'sign'          =>  md5($sms['apiAccount'] . $sms['apikey'] . $time),           //签名
            'timeStamp'     =>  $time,                                                      //当前时间戳(精度ms)
            'mobile'        =>  $mobile,                                                    //接收手机号
            'content'       =>  "【外语通】您好，您的验证码是{$code}，5分钟内有效，请尽快验证。",  //验证信息内容
            'smsType'       =>  '9',                //短信类型(8:通知、9:验证码、11:营销)
            'userData'      =>  $time,              //流水号(最长64字节)
            'url'           =>  $sms['uri'],        //请求URL
            'code'          =>  $code,              //验证码
            'expire'        =>  1800                // 验证码过期时间（s）5分钟
        ];

        $phone = new \PhoneCode($config);

        //$re_json ='{"desc":"成功","smsId":"4c86c338976f4a4bbbf6745c6165305a","status":"0"}';

        $re_json = null;
        $re_json = $phone->send();
        $re_json = json_decode($re_json, true);

        if ($re_json['status'] === '0') {
            //发送成功
            $json = [
                'code'  =>200,
                'msg'   => config('msg.200'),
                'data'  =>[
                    'verify_code'   =>  $code,
                    'mobile'        => $mobile,
                    'time'          =>  date('Y-m-d H:i:s')
                ]
            ];
        }else{
            //发送失败
            $json = [
                'code'  =>400,
                'msg'   => config('msg.400'),
            ];
        }
        return $json;
    }



    public function _sendEmail($to, $name, $subject = '', $body = '', $attachment = null){
        import('PHPMailerAutoload','vendor/PHPMailer');
        global $email_config;
        $config =config('smtp.mail');
        $mail = new \PHPMailer(); //PHPMailer对象
        $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP();  // 设定使用SMTP服务
        $mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
        $mail->SMTPSecure = 'ssl';                 // 使用安全协议
        $mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
        $mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
        $mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
        $mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
        $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
        $replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
        $replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
        $mail->AddReplyTo($replyEmail, $replyName);
        $mail->Subject    = $subject;
        $mail->MsgHTML($body);
        $mail->AddAddress($to, $name);
        if(is_array($attachment)){ // 添加附件
            foreach ($attachment as $file){
                is_file($file) && $mail->AddAttachment($file);
            }
        }
        return $mail->Send() ? true : $mail->ErrorInfo;
    }

}