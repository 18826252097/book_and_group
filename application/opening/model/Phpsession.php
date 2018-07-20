<?php

namespace app\opening\model;
use think\Db;
use think\Model;
use app\opening\validate\PhpsessionVal;#验证规则
use app\opening\validate\ValidateFun;#验证方法
class Phpsession extends Model{

    /**
     * 验证phpsession是否过期
     * @param string $phpsession
     */
    public function _checkPhpsession($phpsession=''){
        if(!empty($phpsession)){
            $endtime = $this->where('phpsession="'.$phpsession.'"')->value('endtime');
            if(time() > $endtime){
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }


    /**
     * 保存session
     * @param $data['phpsession']
     * @param $data['list']
     */
    public function _set($data=[]){
        $phpsession = isset($data['phpsession'])?trim($data['phpsession']):false;
        $list = isset($data['list'])?array_filter($data['list']):[];
        $times = isset($data['times'])?intval($data['times']):86400;#默认保存24小时

        if(empty($phpsession)){
            return ['code'=>10049,'msg'=>config('msg.10049')];
        }

        if(count($list) == 0 || empty($list)){
            return ['code'=>200,'msg'=>config('msg.200')];
        }

        $newtime = time();

        $content = $this->where('phpsession="'.$phpsession.'"')->value('content');
        if(empty($content)){
            //新增
            $add_data = [
                'phpsession'=> $phpsession,
                'content'   => serialize($list),
                'starttime' => $newtime,
                'endtime'   => $newtime+$times
            ];
            $this->insertGetId($add_data);
        }else{
            //修改
            $content = unserialize($content);
            $new_con = array_merge($content,$list);
            $up_data = [
                'content'=>serialize($new_con),
                'starttime' => $newtime,
                'endtime'   => $newtime+$times
            ];
            $this->where('phpsession="'.$phpsession.'"')->update($up_data);
        }
        return ['code'=>200,'msg'=>config('msg.200')];
    }

    /**
     * 获取session
     * @param $phpsession
     */
    public function _get($phpsession=''){
        $phpsession = $phpsession?trim($phpsession):false;
        if(empty($phpsession)){
            return ['code'=>10049,'msg'=>config('msg.10049'),'data'=>[]];
        }

        if(!$this->_checkPhpsession($phpsession)){
            return ['code'=>10050,'msg'=>config('msg.10050'),'data'=>[]];
        }
        
        $content = $this->where('phpsession="'.$phpsession.'"')->value('content');
        $content = unserialize($content);
        return ['code'=>200,'msg'=>config('msg.200'),'data'=>$content];
    }


    /**
     * 删除单个/多个session
     * @param $data['phpsession']
     * @param $data['name'] 要删除的字段名
     */
    public function _delete($data=[]){
        $phpsession = isset($data['phpsession'])?trim($data['phpsession']):false;
        $name = isset($data['name'])?$data['name']:[];

        if(empty($phpsession)){
            return ['code'=>10049,'msg'=>config('msg.10049')];
        }

        if(!$this->_checkPhpsession($phpsession)){
            return ['code'=>10050,'msg'=>config('msg.10050'),'data'=>[]];
        }

        $content = $this->where('phpsession="'.$phpsession.'"')->value('content');
        $content = unserialize($content);

        #把匹配到的字段剔除
        if(!empty($name)){
            foreach ($content as $k=>$v){
                foreach ($name as $vv){
                    if($k == $vv){
                        unset($content[$k]);
                    }
                }
            }
        }
        $this->where('phpsession="'.$phpsession.'"')->update(['content'=>serialize($content)]);#删除数据库中的session
        return ['code'=>200,'msg'=>config('msg.200'),'data'=>$content];
    }
    
    /**
     * 清空session
     * @param string $phpsession
     */
    public function _clear($phpsession=''){
        $phpsession = $phpsession?trim($phpsession):false;
        if(empty($phpsession)){
            return ['code'=>10049,'msg'=>config('msg.10049'),'data'=>[]];
        }

        if(!$this->_checkPhpsession($phpsession)){
            return ['code'=>10050,'msg'=>config('msg.10050'),'data'=>[]];
        }

        $content = [];
        $this->where('phpsession="'.$phpsession.'"')->update(['content'=>serialize($content)]);#删除数据库中的session
        return ['code'=>200,'msg'=>config('msg.200'),'data'=>$content];
    }

}