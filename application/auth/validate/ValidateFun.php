<?php
/**
 * 自定义验证规则方法存放类
 * @Author zhouzeken（1454740914@qq.com）
 * @Date 2018/02/09
 */

namespace app\auth\validate;
use think\Validate;
use think\Db;

class ValidateFun extends Validate
{
    public function __construct(array $rules = [], $message = [], $field = [])
    {
        parent::__construct($rules, $message, $field);
    }

    //验证权限规则是否存在
    public function checkRuleName($value){
        $info = Db::name('auth_rule')->field('name')->where('name="'.$value.'"')->find();
        if(!empty($info['name'])){
            return false;
        }else{
            return true;
        }
    }

    //验证权限是否存在
    public function checkRuleId($value){
        $info = Db::name('auth_rule')->field('id')->where('id="'.$value.'"')->find();
        if(empty($info['id'])){
            return false;
        }else{
            return true;
        }
    }

    //验证权限是否存在，除了自身之外
    public function checkRuleEdit($value='',$rule,$data){
        $id = intval($data['id']);
        $name = $data['name'];
        $info = Db::name('auth_rule')->field('id')->where('id!='.$id.' and name="'.$name.'"')->find();
        if(!empty($info['id'])){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 角色验证开始
     */

    //验证角色是否存在
    public function checkGroupId($value){
        $info = Db::name('auth_group')->field('id')->where('id="'.$value.'"')->find();
        if(empty($info['id'])){
            return false;
        }else{
            return true;
        }
    }


    //验证角色名称是否存在,存在返回false
    public function checkTitleinfo($value){
        $info = Db::name('auth_group')->field('title')->where('title="'.$value.'"')->find();
        if(!empty($info['title'])){
            return false;
        }else{
            return true;
        }
    }

    //验证角色是否存在，除了自身之外
    public function checkGroupEdit($value='',$rule,$data){
        $id = intval($data['id']);
        $title = $data['title'];
        $info = Db::name('auth_group')->field('id')->where('id!='.$id.' and title="'.$title.'"')->find();
        if(!empty($info['id'])){
            return false;
        }else{
            return true;
        }
    }



}