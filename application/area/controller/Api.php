<?php
/**
 * 地区接口
 * User: zhouzeken(1454740914@qq.com)
 * Date: 2018/3/7
 * Time: 10:50
 */
namespace app\area\controller;
use app\common\api\AApi;
use app\common\api\area\IGet;


class Api extends AApi implements IGet{

    function __construct(array $config = []){
        parent::__construct();
    }

    /**
     * 根据id获取详细信息
     * @return mixed
     */
    public function get_info(){
        $data = self::$post_data;
        $obj = new get\Get($data);
        $json = $obj->get_info();
        return create_callback($json);//返回数据
    }

    /**
     * 根据父级id获取地区子列表
     * @return mixed
     */
    public function get_list(){
        $data = self::$post_data;
        $obj = new get\Get($data);
        $json = $obj->get_list();
        return create_callback($json);//返回数据
    }

}

