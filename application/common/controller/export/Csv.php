<?php
/**
 * Csv批量导入导出
 * User: lixiaoming(251603964@qq.com)
 * Date: 2018/1/29
 * Time: 13:44
 */
namespace app\common\controller\export;
use app\common\api\common\IExport;
use think\Config;

class Csv implements IExport{
    // 配置参数
    protected static $config = [
        'file_path'     =>  '',     //文件路径
        'sheets'        =>  1 ,     //数据表数
        'table_data'    => [],      //导出表格数据
        'table_header'  => [],      //导出表头
        'table_name'    => ''       //导出表格名称
    ];

    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []){
        if (empty($config)) {
            $config = Config::get('excel_export');
        }
        self::$config = array_merge(self::$config, array_change_key_case($config));


        $file_path = self::$config['file_path'];
        $DOCUMENT_ROOT = ($_SERVER['DOCUMENT_ROOT']);
        $file_path=$DOCUMENT_ROOT.url('/').$file_path;
        $file_path = str_replace('/index.php','',$file_path);#替换掉/index.php

        //判断系统类型
        $system = php_uname('s');
        if($system == 'Windows NT'){
            $file_path = str_replace('/','\\',$file_path);
        }

        self::$config['file_path'] = $file_path;
    }

    /**
     * 批量导入
     * @return mixed
     */
    public function import(){
        $csv = new \CsvReader(self::$config['file_path']);
        $data['list'] = $csv -> get_data();
        return $data;
    }

    /**
     * 批量导出
     * @return mixed
     */
    public function export(){
        ini_set('max_execution_time', '0');
        ini_set('memory_limit','-1');
        $file_name=self::$config['table_name'].date("YmdHis",time()).".csv";
        header ( 'Content-Type: application/vnd.ms-excel' );
        header ( 'Content-Disposition: attachment;filename='.$file_name );
        header ( 'Cache-Control: max-age=0' );
        ob_clean();
        $fp = fopen('php://output',"a");
        $limit=1000;
        $num = 0;
        $headlist =[];
        foreach (self::$config['table_header'] as $key => $value) {
            $headlist[$key] = iconv('utf-8', 'gbk', $value);
        }
        fputcsv($fp, $headlist);
        foreach (self::$config['table_data'] as $v){
            $num++;
            if($limit==$num){
                ob_flush();
                flush();
                $num=0;
            }
            foreach ($v as $t){
                $tarr[]=iconv('UTF-8', 'gbk',$t);
            }
            fputcsv($fp,$tarr);
            unset($tarr);
        }
        unset($data);
        fclose($fp);
        exit();
    }
}