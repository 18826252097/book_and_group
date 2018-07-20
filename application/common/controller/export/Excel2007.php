<?php
/**
 * excel批量导入导出
 * User: lixiaoming(251603964@qq.com)
 * Date: 2018/1/29
 * Time: 13:41
 */
namespace app\common\controller\export;
use app\common\api\common\IExport;
use think\Config;

class Excel2007 implements IExport{

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

        ini_set('max_execution_time', '0');
        ini_set('memory_limit','-1');
        $data =[];

        if(self::$config['file_path'] == null || !file_exists(self::$config['file_path'])){
            //缺少参数
            $data['error'] = config('msg.10011');
        }else{
            vendor('PHPExcel.PHPExcel');   //引入第三方类库
            $PHPReader = new \PHPExcel_Reader_Excel2007();
            $file_name = self::$config['file_path'];
            $Excel = $PHPReader->load($file_name);

            for($a=1;$a<=self::$config['sheets'];$a++){
                $sheet = "sheet".$a;
                $Column = "Column".$a;
                $Row = "Row".$a;
                $$sheet = $Excel->getSheet($a-1);           //选择第几个表
                $$Column = $$sheet->getHighestColumn();     //获取总列数
                $$Row = $$sheet->getHighestRow();           //获取总行数

                $data['list'][$a] = array();                //用于保存Excel中的数据
                for($i=1;$i<=$$Row;$i++){
                    $empty_i = 0;
                    //循环获取表中的数据，$i表示当前行,索引值从0开始
                    for($j='A';$j<=$$Column;$j++){          //从哪列开始，A表示第一列
                        $address=$j.$i;                     //数据坐标
                        $data['list'][$a][$i-1][$j]=trim((string)$$sheet->getCell($address)->getValue());//读取到的数据，保存到数组中
                        if($data['list'][$a][$i-1][$j] != ''){
                            $empty_i++;
                        }
                    }
                    //过滤单行全部为空数据
                    if($empty_i == 0){
                        unset($data['list'][$a][$i-1]);
                    }
                }
            }

        }
        return $data;
    }

    /**
     * 批量导出
     * @return mixed
     */
    public function export(){
        ini_set('max_execution_time', '0');
        ini_set('memory_limit','-1');

        //引入第三方类库
        vendor('PHPExcel.PHPExcel');
        $excel = new \PHPExcel();
        $letter = array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S',
            'T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ',
            'AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
        );

        $table_header = self::$config['table_header'];
        $table_data = self::$config['table_data'];
        $table_name = self::$config['table_name'];

        for($i = 0;$i < count($table_header);$i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$table_header[$i]");
        }

        //填充表格信息
        for ($i = 2;$i <= count($table_data)+1;$i++) {
            $j = 0;
            foreach ($table_data[$i - 2] as $key=>$value) {
                $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
                $j++;
            }
        }

        if($table_name==''){
            $table_name=date('YmdHis',time());
        }

        //创建Excel输入对象
        $write = new \PHPExcel_Writer_Excel2007($excel);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="'.$table_name.'.xlsx"');
        header("Content-Transfer-Encoding:binary");
        ob_clean();
        $write->save('php://output');
        exit;

    }
}