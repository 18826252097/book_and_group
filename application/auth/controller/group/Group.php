<?php

/**
 * 角色实现类
 * @Author zhouzeken（1454740914@qq.com）
 * @Date 2018/0209
 */
namespace app\auth\controller\group;
use app\common\api\auth\IGroup;
use think\Db;
use app\auth\model\AuthGroup;

class Group implements IGroup
{
    private $index = [
        'no_page'    => '',#关闭分页 传1关闭
        'curr'       => 1,#当前页
        'limits'     => 10,#每页显示数量
        'sort'       => 1,#排序
        'keyword'    => '',#搜索关键字，可匹配多个字段
    ];

    private $info = [
        'id' => '',#角色id
    ];

    private $add = [
        'title'      => '',#角色名称
        'rules'      => '',#角色拥有的权限
    ];

    private $edit = [
        'id'         => '',
    ];

    private $delete = [
        'ids'   => ''
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
        $m_auth_group = new AuthGroup();
        return $m_auth_group->_getList($this->index);
    }

    /**
     * 获取单个
     * @return mixed
     */
    public function info(){
        $data = $this->info;
        $m_auth_group = new AuthGroup();
        return $m_auth_group->_getInfo($data['id']);
    }

    /**
     * 增加
     * @return mixed
     */
    public function add(){
        $m_auth_group = new AuthGroup();
        return $m_auth_group->_addinfo($this->add);
    }

    /**
     * 修改
     * @return mixed
     */
    public function edit(){
        $m_auth_group = new AuthGroup();
        return $m_auth_group->_editInfo($this->edit);
    }

    /**
     * 删除
     * @return mixed
     */
    public function delete(){
        $m_auth_group = new AuthGroup();
        return $m_auth_group->_delArr($this->delete['ids']);
    }

}