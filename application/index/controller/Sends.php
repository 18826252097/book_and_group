<?php
/**
 * 发送调用测试
 * User: linweixin(502510773@qq.com)
 * Date: 2018/2/3
 * Time: 10:35
 */
namespace app\index\controller;
use app\communication\controller\BaseStation;

class Sends
{
    public function index()
    {
        header("Content-type: text/html; charset=utf-8");
        return 'index';
    }
    // 邮箱调用
    public function email(){
        $data = array(
            'type'      =>  'email',
            'name'      =>  '名称',
            'to'        =>  '502510773@qq.com',
            'body'      =>  'hello world',
            'subject'   =>  '主题',
        );

        $res = getapi($data,'/communication/Api/send');//调用邮箱注册接口
        $res = decodeData($res);//解析返回参数
        //echo $res;
        dump($res);
    }
    // 手机调用
    public function phone(){
        $data = array(
            'type'=>'phone',
            'mobile'=>'18819493724',
            'len'=>'6'
        );
        $res = getapi($data,'/communication/Api/send');//调用邮箱注册接口
        $res = decodeData($res);//解析返回参数
        //echo $res;
        dump($res);
    }
    // 验证短信验证码
    public function checkPhone($code){
        $data = array(
            'type'=>'phone',
            'mobile'=>'18819493724',
            'codes' =>$code,
        );
        $res = getapi($data,'/communication/Api/check');
        $res = decodeData($res);//解析返回参数
        dump($res);
    }
}