<?php
/**
 * 通信模块-单元测试
 * User: linweixin(502510773@qq.com)
 * Date: 2018/2/11
 * Time: 10:35
 */
namespace app\communication\controller;

use think\Controller;

class Unit extends Controller
{
    public function test()
    {

        return $this->fetch('Unit/test');
    }
    public function send_phone($mobile='')
    {
        $data = array(
            'type'=>'phone',
            'mobile'=>$mobile,
            'len'=>'6'
        );
        $res = getapi($data,'/communication/Api/send');//调用邮箱注册接口
//        $res = decodeData($res);//解析返回参数
        echo $res;
        //dump($res);
        return json($res);
    }
    public function check_phone($mobile='',$codes='')
    {
        $data = array(
            'type'=>'phone',
            'mobile'=>$mobile,
            'codes' =>$codes,
        );
        $res = getapi($data,'/communication/Api/check');
        $res = decodeData($res);//解析返回参数
        return json($res);
    }
    public function send_email($to='',$name='',$body='',$subject='')
    {
        $data = array(
            'type'      =>  'email',
            'name'      =>  $name,
            'to'        =>  $to,
            'body'      =>  $body,
            'subject'   =>  $subject,
        );

        $res = getapi($data,'/communication/Api/send');//调用邮箱注册接口
        $res = decodeData($res);//解析返回参数
        return json($res);
    }
}