<?php
/**
 * 文件上传导入类
 * User: lixiaoming(251603964@qq.com)
 * Date: 2018/1/27
 * Time: 14:53
 */
namespace app\common\controller;

class Upload implements IUpload{
    /**
     * 文件上传  - Author: Dejan  2018.01.23
     * @param  String  $frm_name  文件表单name(name="file")
     * @param  Bool    $qupload   true七牛上传  ,默认false
     * @param  Bool    $clear     $qupload为true时可用,该参数为true时清除本地临时上传文件
     * @return Array
     */
    public function _upload($frm_name,$qupload=false,$clear=true){

    }

    /**
     * 删除文件  - Author: Dejan  2018.01.24
     * @param  String  $file  文件路径 或 URL
     * @param  int     $type  类型，1本地，2七牛
     * @return Bool
     */
    public function _delFile($file,$type=1){

    }
}
