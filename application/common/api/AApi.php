<?php
/**
 * 公共接口抽象类
 * User: lixiaoming(251603964@qq.com)
 * Date: 2018/2/5
 * Time: 9:48
 */
namespace app\common\api;
use \think\Request;

abstract class AApi{
    //传入参数
    public static $post_data;

    //接口初始化
    public function __construct(){
         header('Content-Type:text/html;charset=utf-8');
         //允许任意域名发起的跨域请求
         header("Access-Control-Allow-Origin: *");
      
        
        //数据接收
        $post_data = file_get_contents('php://input', 'r');

        //GET方式提交
        $get_data = input();
        if(!empty($get_data['data']) && !empty($get_data['sign'])){
            $post_data = json_encode($get_data);
        }

        $post_data = decodeData($post_data);
        $data = isset($post_data['data'])?$post_data['data']:'';
        $sign = isset($post_data['sign'])?$post_data['sign']:'';

        if(!$data && !$sign){
            //header("HTTP/1.0 404 Not Found");
            exit();
        }


        //签名验证
        if(!check_sign($data,$sign)){
            $json = array(
                'code' => '10001',
                'msg'  => config('msg.10001')
            );
            ob_clean();
            echo  create_callback($json);
            exit;
        }else{
            self::$post_data = $post_data['data'];
        }
    }

    /**
     * 修改器 设置数据对象的值
     * @access public
     * @param string $name  名称
     * @param mixed  $value 值
     * @return void
     */
    public function __set($name, $value)
    {
        $this->post_data[$name] = $value;
    }

    /**
     * 获取器 获取数据对象的值
     * @access public
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name)
    {
        return $this->post_data[$name];
    }

}