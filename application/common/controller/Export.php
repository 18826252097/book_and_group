<?php
/**
 * 批量导入导出工厂出口
 * User: lixiaoming(251603964@qq.com)
 * Date: 2018/2/1
 * Time: 11:19
 */
namespace app\common\controller;
use app\common\api\common\IExport;

class Export implements IExport {
    // 配置参数
    protected static $config = [
        'file_path'     =>  '',     //文件路径
        'sheets'        =>  1 ,     //数据表数
        'table_data'    => [],      //导出表格数据
        'table_header'  => [],      //导出表头
        'table_name'    => '',      //导出表格名称
        'suffix'        => 'xls'
    ];

    static public $obj;


    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []){
        if (empty($config)) {
            $config = Config::get('excel_export');
        }
        self::$config = array_merge(self::$config, array_change_key_case($config));

        //实例化对象
        self::getInstance();
    }

    /**
     * 实例化对象
     * @return export\Csv|export\Excel2007|export\Excel5
     */
    static function getInstance()
    {
        if (empty(self::$obj)) {
            //实例化对象
            if(self::$config['file_path'] == ''){
                $suffix = self::$config['suffix'];
            }else{
                $suffix = substr(strrchr(self::$config['file_path'], '.'), 1);
            }

            switch($suffix){
                case 'xlsx':
                    self::$obj = new export\Excel2007(self::$config);
                    break;
                case 'csv':
                    self::$obj = new export\Csv(self::$config);
                    break;
                default:
                    //Excel2003
                    self::$obj = new export\Excel5(self::$config);
            }
            return self::$obj;
        } else {
            return self::$obj;
        }
    }

    /**
     * 批量导入
     * @return mixed
     */
    public function import(){
        $obj = self::$obj;
        $data = $obj ->import();
        return $data;
    }

    /**
     * 批量导出
     * @return mixed
     */
    public function export(){
        $obj = self::$obj;
        $obj ->export();
    }
}