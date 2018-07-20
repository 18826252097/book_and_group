<?php
/**
 * 对外接口相关函数
 * User: lixiaoming(251603964@qq.com)
 * Date: 2018/2/8
 * Time: 10:47
 */

/*******对外接口相关函数开始********************************************************/
/**
 * 接口密钥检测
 * @param $data array 参数
 * @param $sign string 需要验证的签名
 * @return bool
 */
function check_sign($data,$sign){
    ksort($data);
    $str = r_implode(",",$data);
    $key =config('apikey.API_KEY');
    $newsign = md5($str.$key);
    if($newsign === $sign){
        return true;
    }else{
        return false;
    }
}

/**
 * 生成接口返回格式
 * @param $data
 * @return array|string
 */
function create_callback($data){
    $data['time'] = date('Y-m-d H:i:s');
    $sign = create_sign($data);
    $json = array(
        'data' => base64_encode(json_encode($data)),
        'sign' => $sign
    );
    $json = json_encode($json);
    return $json;
}

/**
 * 接口签名生成
 * @param $data array 参数
 * @return string
 */
function create_sign($data){
    ksort($data);
    $str = r_implode(",",$data);
    $key =config('apikey.API_KEY');
    $sign = md5($str.$key);
    return $sign;
}

/**
 * 多维数组返回一维数组，拼接字符串输出
 * @param $pieces array 多维数组
 * @param $glue array 分隔符
 * @return string
 */
function r_implode($glue,$pieces){
    foreach($pieces as $r_pieces){
        if(empty($r_pieces) && $r_pieces!==0 && $r_pieces!=='0' && $r_pieces !== false){
            continue;
        }
        if($r_pieces === true){
            $r_pieces = 'true';
        }elseif($r_pieces === false){
            $r_pieces = 'false';
        }
        if(is_array($r_pieces)){
            $retVal[] = r_implode($glue,$r_pieces);
        }else{
            $retVal[] = $r_pieces;
        }
    }
    if(!empty($retVal)) {
        return implode($glue, $retVal);
    }else{
        return '';
    }
}

/**
 * 接口数据解密
 * @param $post_data
 * @return mixed
 */
function decodeData($post_data){
    $post_data = dejson($post_data);
    $post_data['data'] = $post_data['data']?dejson(base64_decode($post_data['data'])):'';
    return $post_data;
}

/**
 * 重写json解析
 * @param $str
 * @return mixed
 */
function dejson($str){
    $str = str_replace("\r\n", '', $str);
    return json_decode($str, 1);
}


/**
 * 接口参数生成
 * @param $data
 * @return string
 */
function setkey($data){
    header('Content-Type:text/html;charset=utf-8');
    ksort($data);
    $str = r_implode(",",$data);
    $sign = create_sign($data);
    $json = array(
        'data' =>base64_encode(json_encode($data)),
        'sign' =>$sign
    );
    return json_encode($json);
}

/**
 * 接口调用方法
 * @param array $data  参数
 * @param null $host 域名 127.0.0.1
 * @param null $port 端口 80
 * @param null $path 接口地址 /api/login
 * @return string
 */
function getapi(array $data=[],$path=null,$host=null,$port=null){

    header("Content-type: text/html; charset=utf-8");
    $http_entity_body = setkey($data);
    $http_entity_type = 'application/x-www-form-urlencoded';
    $http_entity_length = strlen($http_entity_body);
    $host = trim($host);
    $port = trim($port);
    $host = !empty($host)?$host:config('apikey.API_URL_HOST');#ip
    $port = !empty($port)?$port:config('apikey.API_URL_PORT');#端口
    $path = $_SERVER['SCRIPT_NAME'].$path;
    $result = '';
    $fp = fsockopen($host, $port, $error_no, $error_desc, 30);
    stream_set_blocking($fp,0); //非阻塞模式
    if ($fp) {
        fputs($fp, "POST {$path} HTTP/1.0\r\n");
        fputs($fp, "Host: {$host}\r\n");
        fputs($fp, "Content-Type: {$http_entity_type}\r\n");
        fputs($fp, "Content-Length: {$http_entity_length}\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $http_entity_body . "\r\n\r\n");
        $body ='';
        while (!feof($fp)) {
            $body .= fgets($fp, 128);
        }
        fclose($fp);

        $result = substr ( $body, strpos ( $body, "\r\n\r\n" ) + 4 );
        //echo print_r($result,true);die;
    }
    return $result;

}

/**
 * curl
 * @Author: mazefeng
 * @param $url
 * @param $data
 * @param int $timeout
 * @return mixed
 */
function curl($url, $data,$timeout = 300){

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:")); //头部要送出'Expect: '
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
    $handles = curl_exec($ch);
    curl_close($ch);
    return $handles;
}
/*******对外接口相关函数结束********************************************************/