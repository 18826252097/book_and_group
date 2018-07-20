<?php
namespace app\index\model;
use think\Model;

/**
 * Created by PhpStorm.
 * User: crp
 * Date: 2018/7/13
 * Time: 10:03
 */
class Menu extends Model
{
    public function add_menu($data)
    {
        if(!isset($data['name'])){
            return ['code'=>10002,'msg'=>'缺少必填参数'];
        }
        $insert = [
            'name' => $data['name']
        ];
        $resu = $this->insert($insert);
        if ($resu){
            return ['code'=>200,'msg'=>'添加主题成功'];
        }else{
            return ['code'=>500,'msg'=>'服务器错误'];
        }
    }

    public function get_menu_list($data)
    {
        $curr = isset($data['curr'])?$data['curr']:1;
        $limits = isset($data['limits'])?$data['limits']:10;
        $map = ['status'=>1,'del'=>1];
        $total = $this->where($map)->count();
        $list = $this->field('id,name')->where($map)->page($curr,$limits)->select();
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