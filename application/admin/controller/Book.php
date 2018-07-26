<?php
namespace app\admin\controller;
use think\Controller;

class Book extends Base
{
	public function __construct()
	{
		parent::__construct();
	}

    public function index()
    {
    	if (request()->isAjax()) {
    		$post_data = input('post.');
    		return model('Book')->get_book_list($post_data);
    	}else{
			return view();
    	}
    }

    public function add()
    {
    	$post_data = input('post.');
    	return model('Book')->add_book($post_data);
    }

    public function edit()
    {
    	$post_data = input('post.');
    	return model('Book')->edit_book($post_data);
    }

    public function del()
    {
    	$post_data = input('post.');
    	return model('Book')->del_book($post_data);
    }

    public function get_menu_list()
    {
        $map = [
            'status' => 1,
            'del' => 1
        ];
        return db('menu')->field('id,name')->where($map)->select();
    }
}