<?php
/**
 * 通信-验证数据类
 * User: linweixin(502510773@qq.com)
 * Date: 2018/2/10
 * Time: 10:35
 */
namespace app\communication\model;
use app\communication\validate\ValidateFactory;
use think\Model;
class CommCheck extends Model{
    /**
     * 添加记录
     * @param string $code
     * @param int $verify_time
     * @param string $key
     * @return array
     */
    public function _addRecord($code='',$verify_time=0,$key=''){
        $result = [];
        $validate = ValidateFactory::send('commCheck');
        if ($validate->check(['code'=>$code,'verify_time'=>$verify_time,'key'=>$key])) {
            $add_data['code'] = $code;
            $add_data['verify_time'] = $verify_time;
            $data=$this->where('key', $key)->find();
            if($data){
                $res = $this->save($add_data,['id'=>$data['id']]);
            }else{
                $add_data['key'] = $key;
                $res = $this->insert($add_data);
            }
            if($res){
                $result = ['code'=>true,'msg'=>'添加成功','data'=>[]];
            }else{
                $result = ['code'=>false,'msg'=>'添加失败','data'=>[]];
            }
        }else{
            $result = ['code'=>false,'msg'=>$validate->getError(),'data'=>[]];
        }

        return $result;
    }

}