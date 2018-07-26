<?php
/**
 * Created by PhpStorm.
 * User: crp
 * Date: 2018/7/26
 * Time: 11:53
 */

namespace app\admin\controller;
use think\Controller;

class Ajax extends Controller
{
    public function qupload()
    {
        $func = new \app\common\controller\upload\Qupload();
        return json($func->upload()); # 仅上传七牛云, 本地不存储该文件
    }

    public function upload(){
        $func = new \app\common\controller\Upload();
        return json($func->upload());
    }
}