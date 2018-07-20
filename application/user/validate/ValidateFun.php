<?php
/**
 * 自定义验证规则方法存放类
 * @Author zhouzeken（1454740914@qq.com）
 * @Date 2018/02/09
 */

namespace app\user\validate;
use think\Validate;
use think\Db;

class ValidateFun extends Validate
{
    public function __construct(array $rules = [], $message = [], $field = [])
    {
        parent::__construct($rules, $message, $field);
    }

    //验证手机号
    public function checkPhone($value=''){
        if(!preg_match("/^1[3-8]{1}\d{9}$/",$value)){
            return false;
        }else{
            return true;
        }
    }

    public function checkUsername($value = ''){
        $s=substr($value,0,1);
        if(!$s ||intval($s)>0 ||strpos($value,'@')){
            return false;
        }else{
            return true;
        }
    }


    //第三方登录验证用户是否存在
    public function checkPartyLogin($value = '',$rule,$data){
        $check_where['del'] = 1;
        $check_where['third_id'] = $data['third_id'];
        $check_where['openid'] = $data['openid'];
        $this_user_data = Db::table('wyt_member')->alias('tb1')->field('tb1.id,tb1.status')
            ->join([['wyt_member_third_party tb2', 'tb2.uid=tb1.id']])->where($check_where)->find();
        if(empty($this_user_data['id'])){
            return false;
        }else{
            if($this_user_data['status'] == 2){
                return 10032;
            }elseif($this_user_data['status'] == 3){
                return 10033;
            }else {
                return true;
            }
        }
    }

    //验证用户名是否存在
    public function checkUserInfo($value){
        $info = Db::name('member')->field('username')->where('username="'.$value.'"')->find();
        if(!empty($info['username'])){
            return false;
        }else{
            return true;
        }
    }

    //验证邮箱是否存在
    public function checkEmailInfo($value){
        $info = Db::name('member_content')->field('email')->where('email="'.$value.'"')->find();
        if(!empty($info['email'])){
            return false;
        }else{
            return true;
        }
    }

    //验证手机号是否存在
    public function checkPhoneInfo($value){
        $info = Db::name('member_content')->field('phone')->where('phone="'.$value.'"')->find();
        if(!empty($info['phone'])){
            return false;
        }else{
            return true;
        }
    }

    //验证openid是否存在
    public function checkOpenidInfo($value){
        $info = Db::name('member_third_party')->field('openid')->where('openid="'.$value.'"')->find();
        if(!empty($info['openid'])){
            return false;
        }else{
            return true;
        }
    }

    //验证角色是否存在
    public function checkGroupId($value){
        $info = Db::name('auth_group')->field('id')->where('id="'.$value.'"')->find();
        if(empty($info['id'])){
            return false;
        }else{
            return true;
        }
    }

}