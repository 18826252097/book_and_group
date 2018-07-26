<?php
/**
 * Created by crp.
 * User: Administrator
 * Date: 2018/5/22
 * Time: 14:32
 */
namespace app\admin\Model;
use think\Model;

class Admin extends Model{
	public function _login($data)
	{
		if(!isset($data['username']) || !isset($data['password'])){
			return ['code'=>10002,'data'=>'','msg'=>'缺少参数'];
		}

		$encrypt = $this->where('username',$data['username'])->value('encrypt');

		if (empty($encrypt)) {
			return ['code'=>500,'data'=>'','msg'=>'用户不存在'];
		}

		$map = [
			'username' => $data['username'],
			'password' => md5('book_'.$data['username'].$data['password'].$encrypt)
		];
		$user_info = $this->where($map)->find();

		if (empty($user_info)) {
			return ['code'=>500,'data'=>'','msg'=>'账号密码错误'];
		}
		session('uid',$user_info['id']);
		return ['code'=>200,'data'=>$user_info,'msg'=>'成功'];
	}

	public function register($data)
	{
		if(!isset($data['username']) || !isset($data['password'])){
			return ['code'=>10002,'data'=>'','msg'=>'缺少参数'];
		}

		$encrypt = getCode(6);
		$insert = [
			'username' => $data['username'],
			'password' => md5('book_'.$data['username'].$data['password'].$encrypt),
			'encrypt' => $encrypt
		];

		$resu = $this->insert($insert);
		return ['code'=>200,'data'=>['uid'=>1],'msg'=>'成功'];
	}
}