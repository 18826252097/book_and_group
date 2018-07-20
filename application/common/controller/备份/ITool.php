<?php
/**
 * 公共接口类
 * User: lixiaoming
 * Date: 2018/1/22
 * Time: 14:35
 */
namespace app\common\controller;

interface ITool{
    /**
     * 文件上传  - Author: Dejan  2018.01.23
     * @param  String  $frm_name      文件表单name(name="file")
     * @param  Mixed   $ext[可选]      文件类型限制(扩展名),数组或','链接字符串
     * @param  Bool    $qupload[可选]  true同步上传七牛  ,默认false
     * @param  Bool    $clear[可选]    $qupload为true时可用,该参数为true时清除本地临时上传文件 ,默认true
     * @return Array
     */
    # - 函数使用说明
    # $this->_upload('file','doc,txt,mp3'); // 上传到服务器本地, 只允许上传doc,txt,mp3类型文件
    # $this->_upload('file','doc,txt,mp3',true); // 上传到七牛云服务器本地不存储该文件, 只允许上传doc,txt,mp3类型文件
    # $this->_upload('file','doc,txt,mp3',true,false); // 上传到服务器本地并同步上传到七牛云, 只允许上传doc,txt,mp3类型文件
    # $this->_upload('file'); // 上传到服务器本地, 允许上传类型文件读cocfig('upload')配置
    # $this->_upload('file',true); // 上传到七牛云服务器本地不存储该文件, 允许上传类型文件读cocfig('upload')配置
    # $this->_upload('file',true,false); // 上传到服务器本地并同步上传到七牛云, 允许上传类型文件读cocfig('upload')配置
    public function _upload($frm_name,$ext=NULL,$qupload=NULL,$clear=NULL);

    /**
     * 删除文件  - Author: Dejan  2018.01.24
     * @param  String  $file  文件路径 或 URL
     * @param  int     $type  类型，1本地，2七牛
     * @return Bool
     */
    public function _delFile($file,$type=1);

    /**
     * 批量导入
     * @param null $file_path  文件地址
     * @return array
     */
    public function _import($file_path=null);

    /**
     * 批量导出
     * @param array $tableheader 填充表头信息
     * @param array $data 导入数据
     * @param null $tablename 文件名称
     * @return mixed
     */
    public function _excelExport($tableheader=array(),$data=array(),$tablename=null);

    /**
     * 导出excel(csv)
     * @param array $headlist 第一行,列名
     * @param array $data 导出数据
     * @param null $fileName 输出Excel文件名
     */
    public function _csvExport($headlist = array(), $data = array(), $fileName=null);

    /**
     * 下订单
	 * @param array $orderInfo 订单信息
     * @return mixed
     */
    public function _order($orderInfo);

    /**
     * 补单
     * @return mixed
     */
    public function _supplementOrder();

    /**
     * 查订单
     * @return mixed
     */
    public function _searchOrder($infor);

    /**
     * 发送短信
     * @param null $mobile
     * @param int $len
     * @return array
     */
    public function _sendCode($mobile=null,$len=4);

    /**
     *  发送邮件函数
     * @Author: mazefeng
     * @param $to   邮箱号
     * @param $name 用户名
     * @param string $subject 主题
     * @param string $body  内容
     * @param null $attachment
     * @return bool|string
     */
    public function _sendEmail($to, $name, $subject = '', $body = '', $attachment = null);


}