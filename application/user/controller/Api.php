<?php
/**
 * 用户接口
 * User: lixiaoming(251603964@qq.com)
 * Date: 2018/2/5
 * Time: 10:50
 */
namespace app\user\controller;
use app\common\api\AApi;
use app\common\api\user\IRegister;
use app\common\api\user\ILogin;
use app\common\api\user\IUpdate;
use app\common\api\user\IBatch;
use app\common\api\user\IGet;


class Api extends AApi implements IRegister,ILogin,IUpdate,IBatch,IGet{

    function __construct(array $config = []){
        parent::__construct();
    }

    /**
     * 注册
     * @return mixed
     */
    public function register(){
        
        $data = self::$post_data;
        $type = '';

        if(isset($data['type'])){
            $type = $data['type'];
        }
//        return ($data);
        //检测调用类型
        switch($type){
            case 'email':
                //邮箱注册
                $o_register = new register\Email($data);
                break;
            case 'phone':
                //手机注册
                $o_register = new register\Phone($data);

                break;
            case 'qq':
                //QQ
                $o_register = new register\Qq($data);
                break;
            case 'wechat':
                //微信
                $o_register = new register\Wechat($data);
                break;
            case 'username':
                //账号
                $o_register = new register\Username($data);
                break;
            default:
                //新增用户
                $o_register = new register\Add($data);
                break;
        }

        $json = $o_register -> register();
        return create_callback($json);//返回数据
    }

    /**
     * 登录
     * @return mixed
     */
    public function login(){
        $data = self::$post_data;
        $type = '';

        if(isset($data['type'])){
            $type = $data['type'];
        }
        //检测调用类型
        switch($type){
            case 'email':
                //邮箱注册
                $obj = new login\Email($data);
                break;
            case 'phone':
                //手机注册
                $obj = new login\Phone($data);
                break;
            case 'qq':
                //QQ
                $obj = new login\Qq($data);
                break;
            case 'wechat':
                //微信
                $obj = new login\Wechat($data);
                break;
            default:
                //账号
                $obj = new login\Username($data);
        }

        $json = $obj -> login();
        return create_callback($json);//返回数据
    }


    /**
     * 修改密码
     * @return mixed
     */
    public function update_pwd(){
        $data = self::$post_data;
        $obj = new update\Update($data);
        $res = $obj->update_pwd($data);
        return create_callback($res);//返回数据
    }

    /**
     * 找回密码
     * @return mixed
     */
    public function back_pwd(){
        $data = self::$post_data;
        $obj = new update\Update($data);
        $res = $obj->back_pwd();
        return create_callback($res);//返回数据
    }

    /**
     * 修改基本信息
     * @return mixed
     */
    public function update_info(){
        $data = self::$post_data;
        $obj = new update\Update($data);
        $res = $obj->update_info();
        return create_callback($res);

    }

    /**
     * 删除1个或多个用户
     * @return mixed
     */
    public function delete(){
        $data = self::$post_data;
        $obj = new update\Update($data);
        $res = $obj->delete();
        return create_callback($res);
    }

    /**
     * 获取用户详细信息
     * @return mixed
     */
    public function get_info(){
        $data = self::$post_data;
        $type = '';

        if(isset($data['type'])){
            $type = $data['type'];
        }
        //除了默认类，可能还有其他的，比如学生，老师之类的扩展类型
        switch ($type){
            default:
                //信息获取，默认类
                $obj = new get\Get($data);
                break;
        }

        $res = $obj->get_info();
        return create_callback($res);
    }

    /**
     * 获取用户列表
     * @return mixed
     */
    public function get_list(){
        $data = self::$post_data;
        $type = '';

        if(isset($data['type'])){
            $type = $data['type'];
        }
        switch ($type){
            default:
                $obj = new get\Get($data);
                break;
        }
        $res = $obj->get_list();
        return create_callback($res);
    }


    /**
     * 批量导入
     * @param array $data
     * @return mixed
     */
    public function import(){
        $data = self::$post_data;
        $type = '';

        if(isset($data['type'])){
            $type = $data['type'];
        }
        switch ($type){
            default:
                //默认导入基本的
                $obj = new batch\Batch($data);
                break;
        }

        $res = $obj->import();
        return create_callback($res);
    }

    /**
     * ps:此方法是get方式请求 例：http://127.0.0.1:4010/user/api/export/data/wa10=/sign/dz11ffaxfjgdaf2sajgasdfa
     * 批量导出
     * @return mixed
     */
    public function export(){
        $data = self::$post_data;
        $type = '';

        if(isset($data['type'])){
            $type = $data['type'];
        }
        switch ($type){
            default:
                //默认导出基本的
                $obj = new batch\Batch($data);
                break;
        }
        $obj->export();
    }
}

