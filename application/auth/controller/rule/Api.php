<?php
/**
 * 权限出口
 * User: zhouzeken(1454740914@qq.com)
 * Date: 2018/3/7
 * Time: 10:50
 */
namespace app\auth\controller\rule;
use app\common\api\AApi;
use app\common\api\auth\IRule;


class Api extends AApi implements IRule{

    function __construct(array $config = []){
        parent::__construct();
    }

    /**
     * 获取列表
     * @return mixed
     */
    public function index(){
        $data = self::$post_data;
        $obj = new Rule($data);
        $json = $obj->index();
        return create_callback($json);//返回数据
    }

    /**
     * 获取单个
     * @return mixed
     */
    public function info(){
        $data = self::$post_data;
        $obj = new Rule($data);
        $json = $obj->info();
        return create_callback($json);//返回数据
    }

    /**
     * 增加
     * @return mixed
     */
    public function add(){
        $data = self::$post_data;
        $obj = new Rule($data);
        $json = $obj->add();
        return create_callback($json);//返回数据
    }

    /**
     * 修改
     * @return mixed
     */
    public function edit(){
        $data = self::$post_data;
        $obj = new Rule($data);
        $json = $obj->edit();
        return create_callback($json);//返回数据
    }

    /**
     * 删除
     * @return mixed
     */
    public function delete(){
        $data = self::$post_data;
        $obj = new Rule($data);
        $json = $obj->delete();
        return create_callback($json);//返回数据
    }

}

