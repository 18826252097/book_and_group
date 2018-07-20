<?php

/**
 * 获取地区信息类
 * @Author zhouzeken（1454740914@qq.com）
 * @Date 2018/0209
 */
namespace app\area\controller\get;
use app\area\model\Area;
use app\common\api\area\IGet;
use think\Db;
use app\area\validate\AreaVal;#验证规则
use app\area\validate\ValidateFun;#验证方法

class Get implements IGet
{
    
    //获取详细信息
    private $info = [
        'id' => 0
    ];

    //获取列表参数
    private $list = [
        'pid'=>0,#地区父级id，0或空获取顶级列表，也就是省份
    ];

    public function __construct(array $config = []){
        $this->info = array_merge($this->info,$config);
        $this->list = array_merge($this->list,$config);
    }

    /**
     * 根据id获取详细信息
     */
    public function get_info(){
        $data = $this->info;
        $area = new Area();
        $name = $area->_getInfo($data['id']);
        return $name;
    }


    /**
     * 根据父级id获取地区子列表
     * @return mixed
     */
    public function get_list(){
        $data = $this->list;
        $area = new Area();
        $list = $area->_getList($data['pid']);
        return $list;
    }
}