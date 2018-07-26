<?php
namespace app\admin\controller;
use think\Controller;

class Login extends Controller
{
	public function index()
	{
		if (request()->isAjax()) {
			$post_data = input('post.');
			return model('admin')->_login($post_data);
		}else{
			return view();
		}
	}
}