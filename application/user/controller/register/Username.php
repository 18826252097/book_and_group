<?php
/**
 * 账号注册实现类
 * @Author zhouzeken(1454740914@qq.com)
 * @Date 2018/02/08
 */
namespace app\user\controller\register;
use app\common\api\user\IRegister;
use app\user\validate\ValidateFun;#验证类
use app\user\validate\Reg;#验证规则
use app\user\model\Member;
class Username implements IRegister{

    private $config = [
        'aga_group_id'   => '',#角色身份id（必填）
        'm_username'     => '', #账号
        'm_password'     => '', #密码
    ];

    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []){
        $this->config = array_merge($this->config,$config);
    }

    /**
     * 注册
     * @return mixed
     */
    public function register(){
        $data = $this->config;
        $vali_data = Reg::username(); #获取验证规则
        $validate = new ValidateFun($vali_data['rule'], $vali_data['message']);#实例化验证类
        if(!$validate->check($data)){
            $err=$validate->getError();
            return ['code'=>$err,'msg'=>config("msg.{$err}")];
        }
        $data['m_encrypt']  = getCode(6,3);
        $data['m_password'] = getMd5($data['m_password'],$data['m_username'],$data['m_encrypt']);

        $model = new Member();
        \app\common\controller\Log::addlog(['msg'=>'账号注册']);#添加系统日志
        return $model->_addInfo($data);
    }
}