<?php
/**
 * User: huangzhenxiong
 * Date: 2018/02/12
 * Time: 16:20
 */
namespace app\user\Model;

use think\Model;
use think\Db;

class MemberFail extends Model{
	
	/**
     * 保存登录信息
	 * @param  $userInfo  用户信息
	 * @param  $code  	   错误码
	 * @param  $type  	   登录方式 账号1 手机2 邮箱3 第三方4
     * @return array()
     */
    public function _addMemberFail($userInfo = array(),$code = 0,$type = 1)
	{
		$result = [];

		if(!empty($userInfo)){
			$add_data['login_ip'] = get_ip();
			$add_data['login_time'] = date('Y-m-d H:i:s');
			$add_data['login_type'] = $type;
			$add_data['login_data'] = json_encode($userInfo);
			$add_data['login_code'] = $code;
			$add_data['create_time'] = time();
			$res = $this->insert($add_data);
			if($res){
				$result = ['code'=>200,'msg'=>config('msg.200'),'data'=>['userInfo'=>$userInfo]];
			}else{
				$result = ['code'=>10013,'msg'=>config('msg.10013'),'data'=>['userInfo'=>$userInfo]];
			}
		}else{
			$result = false;
		}

		return $result;
	}
}