<?php
/**
 * 文件上传类
 * Author: Dejan  QQ:673008865
 * Date: 2018/2/3
 * Time: 15:50
 */

# 函数使用说明:
#-| new Upload(Mixed[配置(Array)] or [存储类型七牛或本地(String)](可选), String[存储类型七牛或本地](可选));
#
# $obj = new Upload();        // 默认实例化服务器本地上传类
# $obj = new Upload('qiniu'); // 实例化七牛云上传文件类
#
#-| $obj->upload(Mixed[文件表单名(String)] or [可上传文件类型(Array)](可选), Mixed[可上传文件类型(String|Array)](可选));
#
# (new Upload())->upload(); // 上传到服务器本地
# (new Upload('qiniu'))->upload(); // 上传到七牛云
# (new Upload(config('upload2')))->upload(); // 上传到服务器本地,指定上传文件配置 config('upload2')是个数组
# (new Upload(config('upload2'),'qiniu'))->upload(); // 上传到七牛云,指定上传文件配置 config('upload2')是个数组
# $obj->upload(['doc','txt','ppt']); // 上传文件类型仅允许doc、txt、ppt后缀的文件
# $obj->upload(NULL,'doc,txt,ppt');  // 同上行
# $obj->upload('form-file-name');    // 指定文件表单名
# $obj->upload('form-file-name','doc,txt,ppt'); // 同上, 区别:上传文件类型仅允许doc、txt、ppt后缀的文件
# $obj->upload('form-file-name',['doc','txt','ppt']); // 同上

namespace app\common\controller;

class Upload{
    static public $obj; # 上传类实例化对象
    
    /**
     * 构造函数
     * @param  Array  $config  文件上传配置
     * @param  String $storage 存储类型,默认NULL [NULL本地  'qiniu'七牛]
     */
    public function __construct($config = [],$storage=NULL){
        if(!is_array($config)){
            $storage = $config;
            $config = [];
        }
        
        # 存储类型判断
        switch($storage){
            # 默认上传本地服务器
            case NULL:{ 
                self::$obj = new upload\Upload($config);
                break;
            }
            # 上传到七牛云
            case 'qiniu':{
                self::$obj = new upload\Qupload($config);
                break;
            }
            # 腾讯云OS 、阿里OS、百度OS 往下加同上...
            default:{
                throw new \RuntimeException('传递非法类型参数!');
                break;
            }
        }
    }
    
    /**
     * 上传文件到服务器本地
     * @param  String  $frm_name      文件表单name(name="file"),默认'file'
     * @param  Mixed   $ext[可选]      文件类型限制(扩展名),数组或','链接字符串 , 默认NULL
     * @param  Int     $resize[可选]   限制上传文件大小优先级最高, 单位:字节
     * @return Array
     */
    public function upload($frm_name='file',$ext=NULL,$resize=NULL){
        return self::$obj->upload($frm_name,$ext,$resize);
    }
    
    /**
     * 删除文件
     * @param  String  $file  文件路径 或 URL
     * @return Bool
     */
    public function delFile($filename = NULL){
        return self::$obj->delFile($filename);
    }
    
    /**
     * 上传类实例化对象
     * @return ClassObject 
     */
    static function getInstance(){
        return self::$obj;
    }
}