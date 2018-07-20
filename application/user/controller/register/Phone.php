<?php
/**
 * 手机号注册实现类
 * @Author zhouzeken(1454740914@qq.com)
 * @Date 2018/02/08
 */
namespace app\user\controller\register;
use app\common\api\user\IRegister;
use app\user\validate\ValidateFun;#验证类
use app\user\validate\Reg;#验证规则
use app\user\model\Member;
class Phone implements IRegister{

    private $config = [
        'aga_group_id'   => '',#角色身份id（必填）
        'mc_phone'       => '', #手机号 必填
        'm_password'     => '', #密码 必填
        'm_username'     => '', #账号，不传则默认生成
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
        $vali_data = Reg::phone(); #获取验证规则
        $validate = new ValidateFun($vali_data['rule'], $vali_data['message']);#实例化验证类
        if(!$validate->check($data)){
            $err=$validate->getError();
            return ['code'=>$err,'msg'=>config("msg.{$err}")];
        }
        $create_username = createUsername();#生成账号
        $m_encrypt  = getCode(6,3);
        //账号为空的话，需要生成一个随机账号
        if(empty($data['m_username'])){
            $m_username = $create_username;
            $m_password = getMd5($data['m_password'],$m_username,$m_encrypt);
        }else{
            $m_username = $data['m_username'];
            $m_password = getMd5($data['m_password'],$m_username,$m_encrypt);
        }
        $data['m_username'] = $m_username;
        $data['m_password'] = $m_password;
        $data['m_encrypt'] = $m_encrypt;

        $model = new Member();
        \app\common\controller\Log::addlog(['msg'=>'手机用户注册']);#添加系统日志
        return $model->_addInfo($data);
    }
}