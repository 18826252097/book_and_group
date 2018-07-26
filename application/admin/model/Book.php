<?php
/**
 * Created by crp.
 * User: Administrator
 * Date: 2018/5/22
 * Time: 14:32
 */
namespace app\admin\model;
use think\Model;

class Book extends Model{
	public function menu()
	{
		return $this->belongsTo('Menu','menu_id')->bind(['menu_name'=>'name']);
	}

	public function get_book_list($data)
	{
		$curr = isset($data['curr'])?$data['curr']:1;
		$limits = isset($data['limits'])?$data['limits']:10;

		$map = [
			'del' => 1
		];

		if (!empty($data['keyword'])) {
			$map['title'] = ['like','%'.$data['keyword'].'%'];
		}

		$total = $this->where($map)->count();
		$resu = $this->with('menu')->where($map)->page($curr,$limits)->select();

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

	public function add_book($data)
	{
		if (!isset($data['title']) || !isset($data['author']) || !isset($data['menu_id']) || !isset($data['remark'])) {
			return ['code'=>10002,'msg'=>'缺少参数'];
		}

		$insert_data = [
			'create_time' => time(),
			'title' => $data['title'],
			'author' => $data['author'],
			'menu_id' => $data['menu_id'],
			'remark' => $data['remark']
		];

		$resu = $this->insert($insert_data);
		return ['code'=>200,'msg'=>'成功'];
	}

	public function edit_book($data)
	{
		if (!isset($data['book_id']) || !isset($data['title']) || !isset($data['status']) || !isset($data['author']) || !isset($data['menu_id']) || !isset($data['remark'])) {
			return ['code'=>10002,'msg'=>'缺少参数'];
		}

		$update_data = [
			'status' => $data['status'],
			'title' => $data['title'],
			'author' => $data['author'],
			'menu_id' => $data['menu_id'],
			'remark' => $data['remark']
		];

		$resu = $this->where('id',$data['book_id'])->update($update_data);
		return ['code'=>200,'msg'=>'成功'];
	}

	public function del_book($data)
	{
		if (!isset($data['book_id'])) {
			return ['code'=>10002,'msg'=>'缺少参数'];
		}

		$resu = $this->where('id',$data['book_id'])->update(['del'=>2]);
		return ['code'=>200,'msg'=>'成功'];
	}
}