<?php
/**
 * 微信注册实现类
 * @Author zhouzeken(1454740914@qq.com)
 * @Date 2018/02/08
 */
namespace app\user\controller\register;
use app\common\api\user\IRegister;
use app\user\validate\ValidateFun;#验证类
use app\user\validate\Reg;#验证规则
use app\user\model\Member;
class Wechat implements IRegister{
    private $config = [
        'aga_group_id'   => '',#角色身份id（必填）
        'mtp_openid'     => '', #第三方openid 必填
        'mtp_content'    => '', #第三方用户信息，json格式
        'mtp_third_id'   => 2, #第三方平台id，1qq，2微信，3微博
        'm_password'     => '', #密码，不传则默认生成12345678
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
        $vali_data = Reg::weChat(); #获取验证规则
        $validate = new ValidateFun($vali_data['rule'], $vali_data['message']);#实例化验证类
        if(!$validate->check($data)){
            $err=$validate->getError();
            return ['code'=>$err,'msg'=>config("msg.{$err}")];
        }

        $create_username = createUsername();#生成账号

        if(empty($data['m_username']) && empty($data['m_password'])){
            //1、账号密码都为空，生成新账号跟默认密码12345678
            $m_username = $create_username;
            $create_pwd = createPwd($m_username);#生成默认密码12345678
            $m_password = $create_pwd['password'];
            $m_encrypt = $create_pwd['encrypt'];
        }elseif($data['m_username'] && $data['m_password']){
            //2、账号密码都存在，则只需要加密密码
            $m_username = $data['m_username'];
            $m_encrypt  = getCode(6,3);
            $m_password = getMd5($data['m_password'],$m_username,$m_encrypt);
        }elseif(empty($data['m_username']) && $data['m_password']){
            //3、账号空，密码存在，则只是生成账号，跟加密密码
            $m_username = $create_username;
            $m_encrypt  = getCode(6,3);
            $m_password = getMd5($data['m_password'],$m_username,$m_encrypt);
        }elseif(empty($data['m_password']) && $data['m_username']){
            //4、账号存在，密码空，则只是生成默认密码12345678
            $m_username = $data['m_username'];
            $create_pwd = createPwd($m_username);#生成默认密码12345678
            $m_password = $create_pwd['password'];
            $m_encrypt = $create_pwd['encrypt'];
        }

        //member主表
        $data['m_username'] = $m_username;
        $data['m_password'] = $m_password;
        $data['m_encrypt'] = $m_encrypt;

        $model = new Member();
        \app\common\controller\Log::addlog(['msg'=>'微信注册']);#添加系统日志
        return $model->_addInfo($data);
    }

}