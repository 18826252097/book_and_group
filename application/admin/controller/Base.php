<?php
namespace app\admin\controller;
use think\Controller;

class Base extends Controller
{
	public function __construct()
	{
		if (!session('uid')) {
			$this->redirect('Login/index');
		}
	}
}