<?php
/**
 * 文件上传下载接口类
 * Author: Dejan  QQ:673008865
 * Date: 2018/2/1
 * Time: 10:50
 */

namespace app\common\api\common;

interface IUpload{
    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []);

    /**
     * 上传文件到服务器本地
     * @param  String  $frm_name      文件表单name(name="file"),默认'file' 
     * @param  Mixed   $ext[可选]      文件类型限制(扩展名),数组或','链接字符串 , 默认NULL
     * @param  Int     $resize[可选]   限制上传文件大小优先级最高, 单位:字节
     * @return Array
     */
    public function upload($frm_name='file',$ext=NULL,$resize=NULL);

    /**
     * 删除本地文件
     * @param  String  $file     文件路径 或 URL
     * @return Bool
     */
    public function delFile($filename=null);
}