<?php
/**
 * 公共对外接口
 * User: zhouzeken(1454740914@qq.com)
 * Date: 2018/2/5
 * Time: 10:50
 */
namespace app\opening\controller;
use app\common\api\AApi;
use app\common\api\opening\IPhpsession;

class Api extends AApi implements IPhpsession{

    function __construct(array $config = []){
        parent::__construct();
    }

    /**
     * 保存session
     * @return mixed
     */
    public function phpsession_set(){
        $data = self::$post_data;
        $obj = new phpsession\Phpsession($data);
        $res = $obj->phpsession_set();
        return create_callback($res);
    }

    /**
     * 获取session
     * @return mixed
     */
    public function phpsession_get(){
        $data = self::$post_data;
        $obj = new phpsession\Phpsession($data);
        $res = $obj->phpsession_get();
        return create_callback($res);
    }

    /**
     * 删除单个/多个session里的字段
     * @return mixed
     */
    public function phpsession_delete(){
        $data = self::$post_data;
        $obj = new phpsession\Phpsession($data);
        $res = $obj->phpsession_delete();
        return create_callback($res);
    }

    /**
     * 清空所有session
     * @return mixed
     */
    public function phpsession_clear(){
        $data = self::$post_data;
        $obj = new phpsession\Phpsession($data);
        $res = $obj->phpsession_clear();
        return create_callback($res);
    }

    /**
     * 获取验证码
     * @param int $codelen 验证码长度
     * @param int $width 宽度
     * @param int $height 高度
     * @param int $fontsize 字体大小
     * @return mixed
     */
    public function get_codes(){
        $data = self::$post_data;
        $obj = new \Codes($data);
        $codeurl = $obj->doimg();#验证码图片地址
        $codes = $obj->getCodes();#验证码值
        $res = ['code'=>200,'msg'=>config('msg.200'),'data'=>['codeurl'=>$codeurl,'codes'=>$codes]];
        return create_callback($res);
    }


    /**
     * 删除本地文件
     */
    public function del_files(){
        try{
            $data = self::$post_data;
            $files = isset($data['files'])?$data['files']:'';
            if(empty($files)){
                return create_callback(['code'=>10010,'msg'=>config('msg.10010')]);
            }
            if(is_file($files)){
                unlink($files);
            }
            return create_callback(['code'=>200,'msg'=>config('msg.200')]);
        }catch (\Exception $e){
            return create_callback(['code'=>201,'msg'=>config('msg.201')."：".$e->getMessage()]);
        }
    }
}

