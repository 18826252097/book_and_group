<?php
/**
 * 微信登录实现类
 * @Author huangzhenxiong（1454740914@qq.com）
 * @Date 2018/02/10
 */
namespace app\user\controller\login;
use think\Db;
use app\common\api\user\ILogin;
use app\user\validate\ValidateFun;#验证类
use app\user\validate\Login;#验证规则
use app\user\Model\MemberLog;#登录记录
use app\user\Model\MemberFail;#登录记录

class Wechat implements ILogin
{
    private $config = [
        'openid'   => '', #第三方平台openid
    ];

    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []){
        $this->config = array_merge($this->config,$config);
    }


    /**
     * 登录
     * @return mixed
     */
    public function login(){
        $vali_data = Login::party(); #获取验证规则
        $validate = new ValidateFun($vali_data['rule'], $vali_data['message']);#实例化验证类
        $data = $this->config;

        $m_member = new \app\user\model\Member();#用户model
        $m_member_fail = new \app\user\model\MemberFail();#登录失败记录表
        $m_member_log = new \app\user\model\MemberLog();#登录成功记录

        //规则验证
        if(!$validate->check($data)) {
            return ['code' => $validate->getError(), 'msg' => config('msg.' . $validate->getError())];
        }

        $where = 'm.del=1 and mtp.openid="'.$data['openid'].'" and mtp.third_id=2';
        $res = $m_member->_getWinfo($where);#获取该用户，判断是否存在
        if($res['code'] != 200){
            return ['code'=>$res['code'],'msg'=>$res['msg']];#用户不存在，或账号状态不正常
        }
        $info = $res['data'];

        //登录成功
        $m_member_log->_addMemberLog($info['id']);#登录日志
        unset($info['encrypt'],$info['password']);
        \app\common\controller\Log::addlog(['msg'=>'微信用户登录']);#添加系统日志
        return ['code'=>200,'msg'=>config('msg.200'),'data'=>$info];
    }
}
