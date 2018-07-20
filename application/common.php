<?php
// 应用公共函数

/**
 * 密码加密算法
 * @author lixiaoming
 * @time 2017/11/16
 * @param string $pwd 明文密码
 * @param string $username 用户名
 * @param string $encrypt 随机安全码
 * @return string 32位加密字符串
 */
function getMd5($pwd,$username,$encrypt){
    $username = strtolower($username);
    return md5(md5($pwd).$username.$encrypt);
}

/**
 * 随机产生安全码
 * @param $len int 长度
 * @param $type int 类型 1全部字母数字  2数字  3小写字母  4大写字母
 * @return string
 */
function getCode($len,$type=1){
    $code = '';
    switch($type){
        case 2:
            $str = '0123456789';
            break;
        case 3:
            $str = 'abcdefghijklmnopqrstuvwxyz';
            break;
        case 4:
            $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 1:
        default:
            $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    }
    for($i=0;$i<$len;$i++){
        $code .= $str[mt_rand(0,mb_strlen($str,'utf-8') -1 )] ;
    }
    return $code;
}

/**
 * 获取真实IP
 * @return array|false|string
 */
function get_ip() {
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else
        if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else
            if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
                $ip = getenv("REMOTE_ADDR");
            else
                if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
                    $ip = $_SERVER['REMOTE_ADDR'];
                else
                    $ip = "unknown";
    return ($ip);
}


/**
 * Excel表格获取
 * @param $PHPReader PHPExcel对象
 * @param $file_path 文件名
 * @return array
 */
function excel_import($PHPReader,$file_path){
    $Excel = $PHPReader->load($file_path);
    $sheet = $Excel->getSheet(0);           //选择第几个表
    $Column = $sheet->getHighestColumn();   //获取总列数
    $Row = $sheet->getHighestRow();         //获取总行数

    $data = array();                        //用于保存Excel中的数据
    for($i=1;$i<=$Row;$i++){
        //循环获取表中的数据，$i表示当前行,索引值从0开始
        for($j='A';$j<=$Column;$j++){       //从哪列开始，A表示第一列
            $address=$j.$i;//数据坐标
            $data[$i-1][$j]=trim((string)$sheet->getCell($address)->getValue());//读取到的数据，保存到数组$arr中
        }
    }
    return $data;
}

/**
 * 生成username账号
 */
function createUsername(){
    //打开锁文件
    $fp = fopen('./public/lock/create_username.lock','r');
    flock($fp,LOCK_SH);
    $str = getCode(2,3);//小写字母字符串(两位)
    $str .= msectime();//毫秒时间戳
    //关闭锁
    flock($fp,LOCK_UN);
    fclose($fp);
    return $str;
}

/**
 * 生成默认密码123456
 * @param string $username
 * @return ['encrypt','password'] 加密字段跟md5密码
 */
function createPwd($username=''){
    if(!empty($username)){
        $username = strval($username);
        $pwd = '12345678';
        $encrypt = getCode(6,3);
        return ['encrypt'=>$encrypt,'password'=>getMd5($pwd,$username,$encrypt)];
    }else{
        return false;
    }
}

/**
 * 获取毫秒时间戳
 */
function msectime() {
    list($msec, $sec) = explode(' ', microtime());
    $msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    return $msectime;
}


/**
 * 把数组元素为null的剔除,最多支持三维
 * @param array $arr
 */
function eliminateArrNull($arr=[]){
    foreach ($arr as $k=>$v){
        if(is_array($v)){
            foreach ($v as $kk=>$vv){
                if(is_array($vv)){
                    foreach ($vv as $kkk=>$vvv){
                        if($vvv === null) unset($arr[$k][$kk][$kkk]);
                    }
                }else{
                    if($vv === null) unset($arr[$k][$kk]);
                }
            }
        }else{
            if($v === null) unset($arr[$k]);
        }
    }
    return $arr;
}

/**
 * 查询一个数组（一维或多维）是否存在某字符串,返回对应id值
 * @param $str varchar 字符串
 * @param $array array()  数组
 * @return 返回匹配到的值对应的id值
 * */
function deep_in_array($str, $array) {
    $str = trim(str_replace(array("\r\n", "\r", "\n"), "",$str));
    foreach($array as $k=>$item) {
        if(!is_array($item)) {//一维数组
            if ($item == $str) {
                return $k;//返回key
            } else {
                //跳出循环
                continue;
            }
        }
        if(in_array($str, $item)) {//多维数组
            return $item['id'];
        } else if(deep_in_array($str, $item)) {
            return $item['id'];
        }
    }
    unset($array);
    return false;
}


/**
 * 把字符串格式为：1,2,3,a1,bb3,3a 这类的不符合int类型的剔除后返回
 * @param string $str
 * @return strint 1,2,3,3
 */
function strMtions($str=''){
    if(!empty($str)){
        $arr = explode(',',$str);
        $newarr = [];
        foreach ($arr as $v){
            $newarr[] = intval($v);
        }
        return implode(',',array_filter($newarr));
    }
    return '';
}

/**
 * 把年份补全日期
 * @param string $str
 * @return string 2018-01-01
 */
function compYear($str=''){
    $dates = '';
    if(!empty($str) && strlen($str) == 4){
        $dates = $str.'-01-01';
    }
    return $dates;
}

/**
 * Excel表得到的日期数值转换为时间戳
 * @param int $num 时间数值
 * @return int(10) 时间戳
 */
function verDates($num=0){
    if(!empty($num)){
        $d = 25569;
        $num = ($num-$d) * 86400;
    }
    return $num;
}

/**
 * 获取文件扩展名
 * @param string $file 文件路径
 * @return string txt/jpg
 * */
function getExtension($file=''){
    if($file){
        return pathinfo($file, PATHINFO_EXTENSION);
    }
}

/**
 * 把数组索引转为以id为索引
 * @param array $arr  数组
 * 例子：   array(0=>array('id'=>'1'),1=>array('id'=>'2'))
 * @return array(1=>array('id'=>'1'),2=>array('id'=>'2'))
 * */
function IdKeys($arr){
    if(is_array($arr)){
        $newarr = array();
        foreach ($arr as $k=>$v){
            $newarr[$v['id']] = $v;
        }
        return $newarr;
    }
}

/**
 * 生成订单号（可以根据需要修改）
 * @return string
 */
function ceate_out_trade_no(){
    return time().getCode(6,2);
}


