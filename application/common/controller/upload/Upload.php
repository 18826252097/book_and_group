<?php
/**
 * 本地上传
 * Author: Dejan  QQ:673008865
 * Date: 2018/2/1
 * Time: 10:50
 */
# 函数使用说明  @see \app\common\controller\upload\IUpload

namespace app\common\controller\upload;
use app\common\api\common\IUpload;

class Upload implements IUpload{
    
    private static $upload_path; # 网站根下public目录绝对路径
    private static $path_prefix; # public/uploads/
    
    # 上传配置
    protected static $config = [
        # 文件上传的服务器存储路径(本地)
        'upload_path'=>'./public/uploads',
    
        # 文件归档类型
        'filetype' => [
            'img'  => ['jpg','jpeg','png','gif','bmp'], # 图片格式
            'vdo'  => ['flv','swf','mp4','ts','mp3','ogg'], # 音频格式
            'app'  => ['apk','ios'], # 应用格式
            'doc'  => ['doc','docx','xls','xlsx','ppt','pptx','pdf','txt','xml'], # document 文档格式
            'pack' => ['rar','zip','tar','gz','7z','bz2','iso'] # package 压缩包格式
        ],
        
        # 本地文件上传设置
        'upload' => [
            'classify' => true, # 文件分类存储,参照类型config('upload.filetype')  [img]/20180129/123.jpg , 选填
            'ext'     => [
                'jpg','jpeg','png','gif','bmp', # 图片
                'flv','swf','mp4','ts','mp3','ogg', # 音频
                'apk','ios', # APP
                'doc','docx','xls','xlsx','ppt','pptx','pdf','txt','xml', # 文档
                'rar','zip','tar','gz','7z','bz2','iso' # 压缩包
            ],
            'img_size'=> 2097152, #  图片上传大小限制最大 2M
            'size'    => 314572800 # 其他文件上传大小限制最大 300M
        ]
    ];
    
    /**
     * 构造函数
     * @param  Array  $config  文件上传配置
     */
    public function __construct(array $config = []){
        # 判断载入文件上传配置
        if(empty($config)){
            $config = config('upload'); # 默认转读文件配置
        }
        self::$config = array_merge(self::$config,$config);
        
        # 初始化默认配置
        self::$upload_path = str_replace('\\', '/', realpath(self::$config['upload_path'])).'/'; # 网站根下public/uploads目录绝对路径
        self::$path_prefix = rtrim(str_replace('./', '', self::$config['upload_path']),'/').'/'; # public/uploads/
    }
    
    /**
     * 上传文件类型限制方法
     * @param   Mixed  $ext  文件扩展名,数组 或 ','链接字符串
     * @return  ClassObject
     */
    private function ext($ext){
        switch(gettype($ext)){
            case 'string':{ # 以 ','链接字符串
                self::$config['upload']['ext'] = explode(',', $ext);
                break;
            }
            case 'array':{ # 数组形式
                self::$config['upload']['ext'] = $ext;
                break;
            }
            default:{
                throw new \RuntimeException('参数非法类型!');
                break;
            }
        }
        return $this;
    }
    
    /**
     * 获取文件扩展名
     * @param  String  $filename  文件名
     * @return String
     */
    private function get_ext($filename){
        return substr($filename, strrpos($filename, '.')+1);
    }
    
    /**
     * 获取文件类型 - 类型读配置现有5种
     * @param  String  $ext  文件名扩展名
     * @return String  [默认类型:img, vdo, app, doc, pack]
     */
    private function get_type($ext){
        foreach(self::$config['filetype'] as $k=>$v){
            if(in_array($ext, $v)){
                return $k;
            }
        }
        return 'unknown';
    }

    /**
     * 上传文件到服务器本地 - 入口函数
     * @param  String  $frm_name      文件表单name(name="file"),默认'file' 
     * @param  Mixed   $ext[可选]      文件类型限制(扩展名),数组或','链接字符串 , 默认NULL
     * @param  Int     $resize[可选]   限制上传文件大小优先级最高, 单位:字节
     * @return Array
     */
    public function upload($frm_name='file',$ext=NULL,$resize=NULL){
        # 指定可上传文件类型 Start
        if(is_array($frm_name)){$this->ext($frm_name);$frm_name='file';} # upload(['txt','doc','zip']);
        if(empty($frm_name)){$frm_name='file';}
        if(!empty($ext)){$this->ext($ext);} # upload('file',['txt','doc','zip']) 或  upload('file','txt,doc,zip')
        # - End
        
        $conf = self::$config['upload']; # 获取上传配置参数
        $ext = implode(',', $conf['ext']); # 许可上传文件类型
        $img_size = empty($conf['img_size']) || !isset($conf['img_size'])?$conf['size']:$conf['img_size']; # 图片允许上传大小
        
        $filelist = request()->file($frm_name);
        if(is_array($filelist)){
            # 多文件上传
            $saveinfo = []; # 文件保存信息
            $err_msg = '';  # 文件上传失败提示信息
            
            # 提交空文件判断
            if(empty($_FILES[$frm_name]['name'][0])){
                $data = [
                    'code'=>400, # 上传失败
                    'msg' =>'提交空表单上传失败!'
                ];
                return $data;
            }
            
            foreach($filelist as $i=>$file){
                # 提交空文件判断
                if(empty($_FILES[$frm_name]['name'][$i])){
                    # 上传失败获取错误信息
                    $err_msg .= '文件:'.$_FILES[$frm_name]['name'][$i].'提交空表单上传失败!;';
                }
                
                # 指定大小上传判断
                $cur_file_ext = $this->get_ext($_FILES[$frm_name]['name'][$i]);
                if($resize !== NULL){
                    $size = $resize;
                }else{
                    if(in_array($cur_file_ext, self::$config['filetype']['img'])){
                        $size = $img_size;
                    }else{
                        $size = $conf['size'];
                    }
                }
                
                # 判断是否开启目录分类
                $classify_dir = '';
                if($conf['classify']){
                    $classify_dir = '/'.$this->get_type($cur_file_ext);
                }
                
                $info = $file->validate(['size'=>$size,'ext'=>$ext])->move(ROOT_PATH . 'public' . '/' . 'uploads'. $classify_dir);
                chmod(ROOT_PATH . 'public' . '/' . 'uploads'. $classify_dir, 0777);
                if($info){
                    # 列入文件信息数组
                    $saveinfo[] = [
                        'name'=>$_FILES[$frm_name]['name'][$i],                   # 用户上传时的文件名
                        'ext'=>$info->getExtension(),                             # 文件扩展名
                        'savepath'=>$classify_dir === ''?self::$path_prefix.str_replace('\\', '/', $info->getSaveName()):self::$path_prefix.substr($classify_dir, 1).'/'.str_replace('\\', '/', $info->getSaveName()), # 文件保存所在路径
                        'savename'=>$info->getFilename(),                         # 文件保存后随机名
                        'size'=>$_FILES[$frm_name]['size'][$i]                    # 文件大小
                    ];
                }else{
                    # 上传失败获取错误信息
                    $err_msg .= '文件:'.$_FILES[$frm_name]['name'][$i].$file->getError().';';
                }
            }
            if($err_msg === ''){
                $data = [
                    'code'=>200, # 上传成功
                    'data'=>$saveinfo,
                    'msg' =>'上传成功'
                ];
            }else{
                $data = [
                    'code'=>400, # 部分上传失败
                    'data'=>$saveinfo,
                    'msg'=>$err_msg
                ];
            }
            unset($saveinfo,$filelist,$info); # 腾出点内存
            return $data;
        }else{
            # 单文件上传
            $err_msg = '';  # 文件上传失败提示信息
            $saveinfo = []; # 文件保存信息
            
            # 提交空文件判断
            if(empty($_FILES[$frm_name]['name'])){
                $data = [
                    'code'=>400, # 上传失败
                    'msg' =>'提交空表单上传失败!'
                ];
                return $data;
            }
            
            if($filelist){
                # 指定大小上传判断
                $cur_file_ext = $this->get_ext($_FILES[$frm_name]['name']);
                if($resize !== NULL){
                    $size = $resize;
                }else{
                    if(in_array($cur_file_ext, self::$config['filetype']['img'])){
                        $size = $img_size;
                    }else{
                        $size = $conf['size'];
                    }
                }
                
                # 判断是否开启目录分类
                $classify_dir = '';
                if($conf['classify']){
                    $classify_dir = '/'.$this->get_type($cur_file_ext);
                }
                
                $info = $filelist->validate(['size'=>$size,'ext'=>$ext])->move(ROOT_PATH . 'public' . '/' . 'uploads'. $classify_dir);
                chmod(ROOT_PATH . 'public' . '/' . 'uploads'. $classify_dir, 0777);
                if($info){
                    $saveinfo = [
                        'name'=>$_FILES[$frm_name]['name'],                       # 用户上传时的文件名
                        'ext'=>$info->getExtension(),                             # 文件扩展名
                        'savepath'=>$classify_dir === ''?self::$path_prefix.str_replace('\\', '/', $info->getSaveName()):self::$path_prefix.substr($classify_dir, 1).'/'.str_replace('\\', '/', $info->getSaveName()), # 文件保存所在路径
                        'savename'=>$info->getFilename(),                         # 文件保存后随机名
                        'size'=>$_FILES[$frm_name]['size']                        # 文件大小
                    ];
                }else{
                    # 上传失败获取错误信息
                    $err_msg = '文件:'.$_FILES[$frm_name]['name'].$filelist->getError().';';
                }
            }
            if($err_msg === ''){
                $data = [
                    'code'=>200, # 上传成功
                    'data'=>$saveinfo,
                    'msg' =>'上传成功'
                ];
            }else{
                $data = [
                    'code'=>400, # 部分上传失败
                    'msg'=>'文件上传失败!'.$err_msg
                ];
            }
            unset($filelist,$file,$info); # 腾出点内存
            return $data;
        }
    }

    /**
     * 删除文件
     * @param  String  $file  文件路径 或 URL
     * @return Bool
     */
    public function delFile($filename = NULL){
        if(empty($filename)){
            throw new \RuntimeException("大哥你要删除文件为什么不告诉我它具体路径? 缺少参数\$file(文件路径 或 URL)");
        }
        
        # 本地服务器上文件删除操作
        if(is_array($filename)){
            # 批量多个文件删除
            foreach($filename as $i=>$file){
                $file = realpath('./').preg_replace(['/http\:\/\/.+?\//','/https\:\/\/.+?\//'], ['/','/'], $file);
                if(isset($file)){
                    unlink($file);
                }else{
                    return false;
                }
                
            }
            return true;
        }else{
            # 单个文件删除
            $filename = realpath('./').preg_replace(['/http\:\/\/.+?\//','/https\:\/\/.+?\//'], ['/','/'], $filename);
            if(isset($filename)){
                return unlink($filename);
            }else{
                return false;
            }
        }
    }
    
    
}