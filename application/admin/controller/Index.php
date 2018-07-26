<?php
namespace app\admin\controller;
use think\Controller;

class Index extends Base
{
	public function __construct()
	{
		parent::__construct();
	}

    public function index()
    {
    	if (request()->isAjax()) {
    		$post_data = input('post.');
    		return model('Menu')->get_menu_list($post_data);
    	}else{
			return view();
    	}
    }

    public function add()
    {
    	$post_data = input('post.');
    	return model('Menu')->add_menu($post_data);
    }

    public function edit()
    {
    	$post_data = input('post.');
    	return model('Menu')->edit_menu($post_data);
    }

    public function del()
    {
    	$post_data = input('post.');
    	return model('Menu')->del_menu($post_data);
    }
}