<?php
/**
 * 用户模块调用示例
 * @Author zhouzeken（1454740914@qq.com）
 * @Date 2018/02/08
 */
namespace app\index\controller;
use think\Db;
class User
{
    public function index(){
        return 'user——index';
    }

    /**
     * 注册模块开始 start
     */

    //账号注册
    public function reg_username(){
        /*

         */
//exit;
        $data=['type'=>'phone','password'=>'123456','phone'=>'17620897866'];
//        $data=input();
        $res = getapi($data,'/user/api/register');//调用邮箱注册接口
        $res = decodeData($res);//解析返回参数
        return json($res);
    }

    /**
     * 注册模块结束 end
     */

    /**
     * 登录模块开始 start
     */

    //账号登录
    public function login_username(){
//        echo getMd5('123456','_admin','1te2cs');exit;
        $data = [
            'username' => '_admin',
            'password' => '123456',
            'email'    => '123456@qq.com',
            'phone'    => '13500000000',
            'type'     => ''
        ];
//        $data=input();
        $res = getapi($data,'/user/api/login');//调用邮箱登录接口
        $res = decodeData($res);//解析返回参数
        return json($res);
    }
    //第三方登录
    public function login_party(){
        $data = [
            'third_id' => '1',
            'openid' => 'test',
            'type' => 'qq'
        ];
//        $data=input();
        $res = getapi($data,'/user/api/login');//调用邮箱登录接口
        $res = decodeData($res);//解析返回参数
        return json($res);
    }

    /**
     * 登录模块结束 end
     */
    //修改密码
    public function update_pwd(){
        $data = [
            'id' =>'2',
            'oldPasswd' => '123456',
            'newPasswd' => '111111'
        ];
//        $data=input();
        $res = getapi($data,'/user/api/update_pwd');//调用邮箱登录接口

        $res = decodeData($res);//解析返回参数
        return json($res);
    }

    //修改信息
    public function update_info(){
        $data = [
            'mc_uid' =>1,
            'mc_realname' => '小青',
            'mc_nickname' => '23456',
            'mc_icon' => '小333',
            'mc_sex' => 1,
            'mc_birthday' => '1996-02-10',
            'mc_content' => '123456',
            'mc_email' => '22222222@qq.com',
            'mc_phone' => '17620897865',
            'mc_wechat' => 'xiaohong222',
            'mc_tencent' => '123456',
            'mc_msn' => '123456',
            'mc_province_id' => '小红',
            'mc_city_id' => '123456',
            'mc_district_id' => '123456',
            'mc_address' => '小红',
            'mc_remark' => '12456',
            'm_status' => '1'
        ];

        $res = getapi($data,'/user/api/update_info');//调用邮箱登录接口
        $res = decodeData($res);//解析返回参数
        return json($res);
    }

    //找回密码
    public function back_pwd(){
        $data = [
            'code' => '1234',
            'phone' => '17620897866',
            'password'=>'123456'
        ];
        $res = getapi($data,'/user/api/back_pwd');//调用邮箱登录接口
        $res = decodeData($res);//解析返回参数
        return json($res);
    }

    //删除一个或多个用户
    public function delete_user(){
        $data = [
            'id' => 3,
            'del'=>2
        ];
        $res = getapi($data,'/user/api/delete');//调用邮箱登录接口
//        echo $res;die;
        $res = decodeData($res);//解析返回参数
//        dump($res);die;
        return json($res);
    }

    /**
     * 用户信息更改开始 start
     */

    //账号更新
    public function update_username(){

    }

    /**
     * 用户信息更改结束 end
     */

    /**
     * 用户信息获取开始 start
     */

    //账号信息获取
    public function get_username(){

    }

    /**
     * 用户信息获取结束 end
     */


}