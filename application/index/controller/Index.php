<?php
namespace app\index\controller;
use app\common\controller\Tool;
use think\Controller;
use think\Exception;

class Index extends Controller
{
    public function add_menu()
    {
        try{
            $post = input('post.');
            return json_encode(model('menu')->add_menu($post));
        }catch (Exception $e){
            return json_encode(['code'=>500,'msg'=>'服务器错误']);
        }
    }

    public function add_book()
    {
        try{
            $post = input('post.');
            return json_encode(model('book')->add_book($post));
        }catch (Exception $e){
            return json_encode(['code'=>500,'msg'=>'服务器错误']);
        }
    }

    public function get_menu_list()
    {
        try{
            $post = input('post.');
            return json_encode(model('menu')->get_menu_list($post));
        }catch (Exception $e){
            return json_encode(['code'=>500,'msg'=>'服务器错误']);
        }
    }

    public function get_book_list()
    {
        try{
            $post = input('post.');
            return json_encode(model('book')->get_book_list($post));
        }catch (Exception $e){
            return json_encode(['code'=>500,'msg'=>'服务器错误']);
        }
    }
}
