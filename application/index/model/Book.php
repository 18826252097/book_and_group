<?php
/**
 * Created by PhpStorm.
 * User: crp
 * Date: 2018/7/13
 * Time: 22:18
 */

namespace app\index\model;


use think\Model;

class Book extends Model
{
    public function menu()
    {
        return $this->belongsTo('Menu','menu_id')->bind(['menu_name'=>'name']);
    }

    public function add_book($data)
    {
        if(!isset($data['title']) || !isset($data['menu_id'])){
            return ['code'=>10002,'msg'=>'缺少必填参数'];
        }
        $insert = [
            'title' => $data['title'],
            'author' => $data['author'],
            'remark' => $data['remark'],
            'menu_id' => $data['menu_id'],
        ];
        $resu = $this->insert($insert);
        if ($resu){
            return ['code'=>200,'msg'=>'添加书本成功'];
        }else{
            return ['code'=>500,'msg'=>'服务器错误'];
        }
    }

    public function get_book_list($data)
    {
        $curr = isset($data['curr'])?$data['curr']:1;
        $limits = isset($data['limits'])?$data['limits']:10;
        $map = [];
        if (!empty($data['menu_id'])){
            $map['menu_id'] = $data['menu_id'];
        }
        $map['status'] = 1;
        $map['del'] = 1;
        $total = $this->where($map)->count();
        $list = $this->with('menu')->where($map)->page($curr,$limits)->select();
        return [
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'curr'=>$curr,
                'total'=>$total,
                'limits'=>$limits,
                'list'=>$list
            ]
        ];
    }
}