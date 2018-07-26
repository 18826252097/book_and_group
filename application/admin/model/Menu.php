<?php
/**
 * Created by crp.
 * User: Administrator
 * Date: 2018/5/22
 * Time: 14:32
 */
namespace app\admin\model;
use think\Model;

class Menu extends Model{
	public function get_menu_list($data)
	{
		$curr = isset($data['curr'])?$data['curr']:1;
		$limits = isset($data['limits'])?$data['limits']:10;

		$map = [
			'del' => 1
		];

		if (!empty($data['keyword'])) {
			$map['name'] = ['like','%'.$data['keyword'].'%'];
		}

		$total = $this->where($map)->count();
		$resu = $this->where($map)->page($curr,$limits)->select();

		return [
			'code'=>200,
			'data'=>[
				'total'=>$total,
				'list'=>$resu,
				'limits'=> $limits,
				'curr' => $curr
			]
		];
	}

	public function add_menu($data)
	{
		if (!isset($data['name'])) {
			return ['code'=>10002,'msg'=>'缺少参数'];
		}

		$insert_data = [
			'create_time' => time(),
			'name' => $data['name']
		];

		$resu = $this->insert($insert_data);
		return ['code'=>200,'msg'=>'成功'];
	}

	public function edit_menu($data)
	{
		if (!isset($data['menu_id']) || !isset($data['name']) || !isset($data['status'])) {
			return ['code'=>10002,'msg'=>'缺少参数'];
		}

		$update_data = [
			'status' => $data['status'],
			'update_time' => time(),
			'name' => $data['name']
		];

		$resu = $this->where('id',$data['menu_id'])->update($update_data);
		return ['code'=>200,'msg'=>'成功'];
	}

	public function del_menu($data)
	{
		if (!isset($data['menu_id'])) {
			return ['code'=>10002,'msg'=>'缺少参数'];
		}

		$resu = $this->where('id',$data['menu_id'])->update(['del'=>2]);
		return ['code'=>200,'msg'=>'成功'];
	}
}