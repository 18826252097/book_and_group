<?php
/**
 * 七牛上传
 * Author: Dejan  QQ:673008865
 * Date: 2018/2/1
 * Time: 10:50
 */
# 函数使用说明  @see \app\common\controller\upload\IUpload

namespace app\common\controller\upload;
use app\common\api\common\IUpload;

class Qupload implements IUpload{
    
    private static $upload_path; # 网站根下public目录绝对路径
    private static $local_path_prefix; # public/uploads/
    
    # 文件上传配置(七牛云)
    private static $config = [
        # 文件上传的服务器存储路径(本地)
        'upload_path'=>'./public/uploads',
    
        # 七牛文件上传设置
        'qupload'=>[
            'AK' => '', # QINIU_ACCESS_KEY , 必填
            'SK' => '', # QINIU_SECRET_KEY , 必填
            'bucket' => '', # 七牛云空间名 , 必填
            'path_prefix' => 'upload/', # 路径前缀可以用来分类文件，例如： [image/jpg/]your-file-name.jpg 中的 image/jpg/ , 选填
            'expires'=> 7200, # 2小时，expires单位为秒，为上传凭证的有效时间,默认是1个小时
            'sync'=> false      # true文件上传同步七牛云 , false本地服务器不保留该文件仅上传到七牛云
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
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []){
        # 判断载入文件上传配置
        if(empty($config)){
            $config = config('upload'); # 默认转读文件配置
        }
        self::$config = array_merge(self::$config,$config);
        
        # 初始化默认配置
        self::$upload_path = str_replace('\\', '/', realpath(self::$config['upload_path'])).'/'; # 网站根下public/uploads目录绝对路径
        self::$local_path_prefix = rtrim(str_replace('./', '', self::$config['upload_path']),'/').'/'; # public/uploads/
    }

    /**
     * 检查必要配置项
     * @return bool
     */
    private function check(){
        # 检查 QINIU_ACCESS_KEY、QINIU_SECRET_KEY、QINIU_BUCKET
        if(empty(self::$config['qupload']['AK'])
            || empty(self::$config['qupload']['SK'])
            || empty(self::$config['qupload']['bucket'])){
            return false;
        }
        return true;
    }

    /**
     * 上传文件到七牛云 - 入口函数
     * @param  String  $frm_name      文件表单name(name="file"),默认'file'
     * @param  Mixed   $ext[可选]      文件类型限制(扩展名),数组或','链接字符串 , 默认NULL
     * @param  Int     $resize[可选]   限制上传文件大小优先级最高, 单位:字节
     * @return array
     */
    public function upload($frm_name='file',$ext=NULL,$resize=NULL){
        if (!self::check()){
            return ['code'=>10010,'msg'=>config('msg.10010'),'data'=>''];
        }
        # 指定可上传文件类型 Start
        $frm_name = empty($frm_name)?'file':$frm_name;
        # - End

        $filelist = request()->file($frm_name);

        if (is_array($filelist)){
            $return_data = [];
            $err = [];
            $flag = true;

            foreach ($filelist as $file_item) {
                $temp_resu = self::upload_qiniu($file_item);
                if ($temp_resu['code'] == 500){
                    $flag = false;
                    $err = $temp_resu['data'];
                    break;
                }
                $return_data[] = $temp_resu['data'];
            }

            if ($flag){
                return [
                    'code' => 200,
                    'msg' => config('msg.200'),
                    'data' => $return_data
                ];
            }else{
                return [
                    'code' => 500,
                    'msg' => config('msg.500'),
                    'data' => [
                        'msg' => $err,
                    ]
                ];
            }
        }else{
            return self::upload_qiniu($filelist);
        }
    }

    /**
     * 七牛上传文件
     * @param $file
     * @return array
     */
    private function upload_qiniu($file)
    {
        $real_file_name = isset($file->getInfo()['name'])?$file->getInfo()['name']:'';
        $ext = !empty($ext)?$ext:pathinfo($real_file_name,PATHINFO_EXTENSION);
        if (!$this->cheak_ext($ext)){
            return ['code'=>'500',config('msg.500'),'data'=>''];
        }
        $size = $file->getSize();
        $conf = self::$config['qupload']; # 获取上传配置参数
        //实例化授权类
        $auth = new \Qiniu\Auth($conf['AK'], $conf['SK']);
        //生成上传授权凭证
        $access_token = $auth->uploadToken($conf['bucket']);
        $filePath = $file->getRealPath();
        $key = $conf['path_prefix'].getCode(6).time().'.'.$ext;
        //实例化七牛上传类
        $uploadManager = new \Qiniu\Storage\UploadManager();
        //上传文件
        list($ret,$err) = $uploadManager->putFile($access_token,$key,$filePath);

        if ($err !== null) {
            return [
                'code' => 500,
                'msg' => config('msg.500'),
                'data' => [
                    'msg' => $err,
                ]
            ];
        } else {
            return [
                'code' => 200,
                'msg' => config('msg.200'),
                'data' => [
                    "name" => $real_file_name,#原本客户端保存的文件名
                    "ext" => $ext,
                    "savepath" => $key,
                    "savename" => $key,
                    "size" => $size
                ]
            ];
        }
    }

    private function cheak_ext($ext)
    {
        if (!in_array($ext,self::$config['upload']['ext'])){
            return false;
        }
        return true;
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
        
        # 七牛云空间上文件删除操作
        $conf = self::$conf['qupload']; # 获取上传七牛配置参数
        
        # 初始化七牛对象 Start
        $AK = $conf['AK']; # QINIU_ACCESS_KEY
        $SK = $conf['SK']; # QINIU_SECRET_KEY
        $bucket = $conf['bucket']; # 七牛云空间名
        $auth = new \Qiniu\Auth($AK, $SK);
        $config = new \Qiniu\Config();
        $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
        # - End
        if(is_array($filename)){
            # 批量多个文件删除
            
            # $filename = array( // 每次最多不能超过1000个
            #     'image/jpg/your-file-name.jpg',
            #     'qiniu.png',
            #     'qiniu.jpg'
            # );
            $ops = $bucketManager->buildBatchDelete($bucket, $filename);
            list($ret, $err) = $bucketManager->batch($ops);
            if($err){
                return false;
            }else{
                return true;
            }
        }else{
            # 单个文件删除
            $err = $bucketManager->delete($bucket, $filename);
            if($err){
                return false;
            }else{
                return true;
            }
        }
    }
}