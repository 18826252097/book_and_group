<?php
/**
 * 上传文件例子  - Author: Dejan  QQ:673008865
 * 2018.01.23
 */
namespace app\index\controller;

class UploadDemo{
    # 本地文件上传 or 七牛云文件上传例子
    public function index(){
        return view();
    }
    
    # 本地上传入口
    public function upload(){
        $func = new \app\common\controller\Upload();
        return json($func->upload());
        
    }
    
    # 七牛上传入口
    public function qupload(){
        $func = new \app\common\controller\upload\Qupload();
        return json($func->upload()); # 仅上传七牛云, 本地不存储该文件
    }
}
