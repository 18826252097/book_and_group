<?php
/**
 * 地区表model
 * User: zhouzeken
 * Date: 2018/3/7
 * Time: 10:22
 */
namespace app\area\model;
use think\Db;
use think\Model;

class Area extends Model{

    /**
     * 根据id获取详细信息
     * @return mixed
     */
    public function _getInfo($id=''){
        if(empty($id)){
            return ['code'=>10010,'msg'=>config('msg.10010'),'data'=>[]];
        }
        $id = intval($id);
        $area = $this->where('id='.$id)->find();
        if(empty($area['id'])){
            return ['code'=>10060,'msg'=>config('msg.10060'),'data'=>[]];
        }
        return ['code'=>200,'msg'=>config('msg.200'),'data'=>$area];
    }

    /**
     * 根据父级id获取地区子列表
     * @return mixed
     */
    public function _getList($pid=0){
        $pid = intval($pid);
        $where['parent_id']=$pid;
        $where['is_show'] = 1;
        $area = $this->where($where)->select();
        
        return ['code'=>200,'msg'=>config('msg.200'),'data'=>$area];
    }
}
