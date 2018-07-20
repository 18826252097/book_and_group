<?php

/**
 * 用户更新实现类
 * @Author yangdj
 * @Date 2018/02/10
 */
namespace app\user\controller\update;
use app\common\api\user\IUpdate;
use app\user\validate\ValidateFun;#验证类
use app\user\validate\Updata;

class Update implements IUpdate
{
    //修改密码
    private $pwd = [
        'uid'  =>'',
        'old_passwd' => '',
        'new_passwd' => '',
    ];
    //修改基本信息
    private $info = [
        'uid'       => 0,
    ];

    //找回密码
    private $back = [
        'phone' => '', #手机号
    ];

    //删除1个或多个用户
    private $delUser = [
        'ids'    => '' #格式：1,2,3
    ];

    public function __construct(array $config = []){

        $this->info = array_merge($this->info,$config);
        $this->pwd = array_merge($this->pwd,$config);
        $this->back = array_merge($this->back,$config);
        $this->delUser = array_merge($this->delUser,$config);
    }

    /**
     * 修改密码
     * @param array $data
     * @return mixed
     */
    public function update_pwd(){
        $vali_data = \app\user\validate\Update::pwd(); #获取验证规则
        $validate = new ValidateFun($vali_data['rule'], $vali_data['message']);#实例化验证类
        if(!$validate->check($this->pwd)){
            return ['code'=>$validate->getError(),'msg'=>config('msg.'.$validate->getError())];
        }
        //验证通过
        \app\common\controller\Log::addlog(['msg'=>'修改密码']);#添加系统日志

        $update = new \app\user\model\Member();
        return  $update->update_pwd($this->pwd);
    }

    /**
     * 找回密码
     * @param array $data
     * @return mixed
     */
    public function back_pwd(){
        $vali_data = \app\user\validate\Update::back_pwd();
        $validate = new ValidateFun($vali_data['rule'], $vali_data['message']);#实例化验证类
//        return $this->back;
        if(!$validate->check($this->back)){
            return ['code'=>$validate->getError(),'msg'=>config('msg.'.$validate->getError())];
        }
        \app\common\controller\Log::addlog(['msg'=>'找回密码']);#添加系统日志
        //验证通过
        $update = new \app\user\model\Member();
        return   $update->back_pwd($this->back);
    }


    /**
     * 修改基本信息
     * @param array $data
     * @return mixed
     */
    public function update_info(){
        \app\common\controller\Log::addlog(['msg'=>'修改用户信息']);#添加系统日志
        $update = new \app\user\model\Member();
        return $update->update_info($this->info);
    }

    /**
     * 删除1个或多个用户
     * @return mixed
     */
    public function delete(){
        $data = $this->delUser;
        \app\common\controller\Log::addlog(['msg'=>'删除用户']);#添加系统日志
        $update = new \app\user\model\Member();
        return   $update->delUser($data['ids'],2);
    }
}