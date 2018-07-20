<?php

/**
 * 权限实现类
 * @Author zhouzeken（1454740914@qq.com）
 * @Date 2018/0209
 */
namespace app\auth\controller\rule;
use app\common\api\auth\IRule;
use think\Db;
use app\auth\model\AuthRule;

class Rule implements IRule
{

    private $index = [
        'is_top'     => '',#是否获取顶级，传1获取顶级
        'no_page'    => '',#关闭分页 传1关闭
        'curr'       => 1,#当前页
        'limits'     => 10,#每页显示数量
        'sort'       => 1,#排序
        'keyword'    => '',#搜索关键字，可匹配多个字段
    ];

    private $info = [
        'id'=>''#标签id
    ];

    private $add = [
        'title'    =>'',#权限名称
        'name'     =>'',#权限规则
        'pid'      =>'',#上级id
    ];

    private $edit = [
        'title'    =>null,#权限名称
        'name'     =>null,#权限规则
        'pid'      =>null,#上级id
    ];

    private $delete = [
        'ids'      =>''#id字符串，格式：1,2,3
    ];

    public function __construct(array $config = []){
        $this->index = array_merge($this->index,$config);
        $this->info = array_merge($this->info,$config);
        $this->add = array_merge($this->add,$config);
        $this->edit = array_merge($this->edit,$config);
        $this->delete = array_merge($this->delete,$config);
    }

    /**
     * 获取列表
     * @return mixed
     */
    public function index(){
        $data = $this->index;
        $m_auth_rule = new AuthRule();
        return $m_auth_rule->_getList($data);
    }

    /**
     * 获取单个
     * @return mixed
     */
    public function info(){
        $data = $this->info;
        $m_auth_rule = new AuthRule();
        return $m_auth_rule->_getInfo($data['id']);
    }

    /**
     * 增加
     * @return mixed
     */
    public function add(){
        $m_auth_rule = new AuthRule();
        return $m_auth_rule->_addinfo($this->add);
    }

    /**
     * 修改
     * @return mixed
     */
    public function edit(){
        $m_auth_rule = new AuthRule();
        return $m_auth_rule->_editInfo($this->edit);
    }

    /**
     * 删除
     * @return mixed
     */
    public function delete(){
        $data = $this->delete;
        $m_auth_rule = new AuthRule();
        return $m_auth_rule->_delArr($data['ids']);
    }

}