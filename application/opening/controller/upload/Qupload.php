<?php 
/**
 * 七牛云文件上传
 * Author: Dejan  QQ: 673008865
 * Date  : 2018.03.27
 */
namespace app\opening\controller\upload;
header('Access-Control-Allow-Origin: *'); # 设置http://www.baidu.com允许跨域访问
header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); # 设置允许的跨域header

class Qupload{
    private $token; # 上传口令
    
    /**
     * 初始化构造函数
     */
    public function __construct(){
        $this->token = config('upload.up_token'); # 读入上传配置
        
        $post = request()->post();
        if(!isset($post['token']) || empty($post['token']) || $post['token'] != $this->token){
            die('{"400":"非法操作 !"}');
        }
    }
    
    /**
     * 上传入口
     */
    public function index(){
        $obj = new \app\common\controller\upload\Qupload();
        return json($obj->upload());
    }
}