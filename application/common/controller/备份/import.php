<?php

namespace app\common\controller;
use think\Controller;

class import extends Controller{

    /**
     * 批量导入
     * @return mixed
     * $type  文件类型
     * $tmp_name  文件路径
     */
    public function _import($type,$tmp_name){
        vendor('PHPExcel.PHPExcel');   //引入第三方类库
        if($type == 'application/octet-stream'){
            $PHPReader = new \PHPExcel_Reader_Excel2007();//Excel2007
        }else{
            $PHPReader = new \PHPExcel_Reader_Excel5();//Excel2003
        }
        $Excel = $PHPReader->load($tmp_name);
        $sheet = $Excel->getSheet(0);//选择第几个表
        $Column = $sheet->getHighestColumn();//获取总列数
        $Row = $sheet->getHighestRow();//获取总行数

        $data = array();//用于保存Excel中的数据
        for($i=2;$i<=$Row;$i++){//循环获取表中的数据，$i表示当前行,索引值从0开始
            for($j='A';$j<=$Column;$j++){//从哪列开始，A表示第一列
                $address=$j.$i;//数据坐标
                $data[$i][$j]=(string)$sheet->getCell($address)->getValue();//读取到的数据，保存到数组$arr中
            }
        }
        return $data;
    }

    /**
     * 批量导出
     * @return mixed
     * $tableheader   填充表头信息
     * $data     导入数据
     * $tablename  文件名称
     */
    public function _export($tableheader=array(),$data=array(),$tablename){
        vendor('PHPExcel.PHPExcel');   //引入第三方类库
        $excel = new \PHPExcel();
        $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        for($i = 0;$i < count($tableheader);$i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
        }
        //填充表格信息
        for ($i = 2;$i <= count($data) + 1;$i++) {
            $j = 0;
            foreach ($data[$i - 2] as $key=>$value) {
                $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
                $j++;
            }
        }
        //创建Excel输入对象
        $write = new \PHPExcel_Writer_Excel5($excel);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="'.$tablename.'.xls"');
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
        exit;
    }
}