<?php
/**
 * User: huangzhenxiong
 * Date: 2018/02/09
 * Time: 16:50
 */
namespace app\user\Model;

use think\Model;
use think\Db;

class MemberLog extends Model{
	
	/**
     * 保存登录信息
     * @param  $userId  用户ID
     * @return array()
     */
    public function _addMemberLog($userId = '')
	{
		$result = [];

		if(!empty($userId)){
			$add_data['uid'] = $userId;
			$add_data['login_time'] = date('Y-m-d H:i:s');
			$add_data['login_ip'] = get_ip();
			$add_data['create_time'] = time();
			$res = $this->insert($add_data);
			if($res){
				$result = ['code'=>200,'msg'=>config('msg.200'),'data'=>['uid'=>$userId]];
			}else{
				$result = ['code'=>10013,'msg'=>config('msg.10013'),'data'=>['uid'=>$userId]];
			}
		}else{
			$result = false;
		}

		return $result;
	}
}